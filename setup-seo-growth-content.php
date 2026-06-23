<?php
/**
 * Add location landing pages and long-tail SEO article clusters.
 *
 * Run locally with:
 * wp eval-file setup-seo-growth-content.php --path=wordpress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dbr_seo_log( $message, $type = 'success' ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		if ( 'warning' === $type ) {
			WP_CLI::warning( $message );
			return;
		}
		WP_CLI::success( $message );
		return;
	}

	echo esc_html( $message ) . "\n";
}

function dbr_seo_polylang_enabled() {
	return function_exists( 'pll_set_post_language' ) && function_exists( 'pll_save_post_translations' );
}

function dbr_seo_post_path( $slug, $parent_id ) {
	$path     = $slug;
	$ancestor = $parent_id;

	while ( $ancestor ) {
		$parent_post = get_post( $ancestor );
		if ( ! $parent_post ) {
			break;
		}
		$path     = $parent_post->post_name . '/' . $path;
		$ancestor = (int) $parent_post->post_parent;
	}

	return $path;
}

function dbr_seo_upsert_page( $slug, $title, $content, $excerpt, $meta_title, $meta_description, $lang = 'en', $parent_id = 0 ) {
	$existing = get_page_by_path( dbr_seo_post_path( $slug, $parent_id ), OBJECT, 'page' );
	$data     = array(
		'post_type'    => 'page',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
		'post_parent'  => $parent_id,
		'post_status'  => 'publish',
		'post_author'  => 1,
	);

	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$post_id    = wp_update_post( $data );
	} else {
		$post_id = wp_insert_post( $data );
	}

	if ( dbr_seo_polylang_enabled() && $post_id && ! is_wp_error( $post_id ) ) {
		pll_set_post_language( $post_id, $lang );
	}

	dbr_seo_set_yoast_meta( $post_id, $meta_title, $meta_description );
	return $post_id;
}

function dbr_seo_upsert_category( $slug, $name, $lang = 'en' ) {
	$term = get_term_by( 'slug', $slug, 'category' );
	if ( ! $term ) {
		$result = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		if ( is_wp_error( $result ) ) {
			return 0;
		}
		$term_id = (int) $result['term_id'];
	} else {
		$term_id = (int) $term->term_id;
		wp_update_term( $term_id, 'category', array( 'name' => $name ) );
	}

	if ( function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( $term_id, $lang );
	}

	return $term_id;
}

function dbr_seo_upsert_post( $slug, $title, $content, $excerpt, $category_id, $meta_title, $meta_description, $focus_keyword, $lang = 'en' ) {
	$existing = get_page_by_path( $slug, OBJECT, 'post' );
	$data     = array(
		'post_type'     => 'post',
		'post_name'     => $slug,
		'post_title'    => $title,
		'post_content'  => $content,
		'post_excerpt'  => $excerpt,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_category' => array_filter( array( $category_id ) ),
	);

	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$post_id    = wp_update_post( $data );
	} else {
		$post_id = wp_insert_post( $data );
	}

	if ( dbr_seo_polylang_enabled() && $post_id && ! is_wp_error( $post_id ) ) {
		pll_set_post_language( $post_id, $lang );
	}

	dbr_seo_set_yoast_meta( $post_id, $meta_title, $meta_description, $focus_keyword );
	return $post_id;
}

function dbr_seo_set_yoast_meta( $post_id, $title, $description, $focus_keyword = '' ) {
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_yoast_wpseo_title', $title );
	update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
	if ( $focus_keyword ) {
		update_post_meta( $post_id, '_yoast_wpseo_focuskw', $focus_keyword );
	}
}

function dbr_seo_link_post_translations( $english_id, $arabic_id ) {
	if ( dbr_seo_polylang_enabled() && $english_id && $arabic_id ) {
		pll_save_post_translations(
			array(
				'en' => $english_id,
				'ar' => $arabic_id,
			)
		);
	}
}

function dbr_seo_link_term_translations( $english_id, $arabic_id ) {
	if ( function_exists( 'pll_save_term_translations' ) && $english_id && $arabic_id ) {
		pll_save_term_translations(
			array(
				'en' => $english_id,
				'ar' => $arabic_id,
			)
		);
	}
}

function dbr_seo_internal_links_html() {
	return '<h2>Related rental pages</h2><ul>' .
		'<li><a href="/bobcat-rental-dubai/">Bobcat rental Dubai</a></li>' .
		'<li><a href="/bobcat-rental-sharjah/">Bobcat rental Sharjah</a></li>' .
		'<li><a href="/bobcat-rental-abu-dhabi/">Bobcat rental Abu Dhabi</a></li>' .
		'<li><a href="/skid-steer-loader-rental-uae/">Skid steer loader rental UAE</a></li>' .
		'<li><a href="/cat-226b-rental-uae/">CAT 226B rental UAE</a></li>' .
		'</ul>';
}

function dbr_seo_quote_block_html( $location = 'the UAE' ) {
	return '<h2>How to request a quote</h2>' .
		'<p>For a useful quote, send the map location, access notes, work type, preferred date, expected duration and any attachment requirement. Photos or a short site video help confirm whether the CAT 226B is the right machine before delivery is discussed.</p>' .
		'<p><strong>Call or WhatsApp:</strong> +971 54 738 8695. Operator is included with the bobcat. Delivery can be free near Fujairah and is quoted for ' . esc_html( $location ) . ' by distance, timing and booking duration.</p>';
}

function dbr_seo_faq_html( $location ) {
	return '<h2>FAQs</h2>' .
		'<h3>Can I hire the bobcat with an operator in ' . esc_html( $location ) . '?</h3><p>Yes. The CAT 226B bobcat / skid steer loader is supplied with an operator for rental jobs.</p>' .
		'<h3>What is the minimum booking duration?</h3><p>Most jobs are discussed as half-day, full-day or multi-day bookings. If the job is small, share the details and we will confirm the practical option.</p>' .
		'<h3>Is delivery free?</h3><p>Delivery can be free near Fujairah. Other UAE areas are quoted after the exact job location, date and duration are known.</p>';
}

function dbr_seo_location_content( $city, $areas, $delivery_note, $extra_note ) {
	$image_alt = 'CAT 226B bobcat rental with operator in ' . $city;

	return '<h2>Bobcat rental service in ' . esc_html( $city ) . '</h2>' .
		'<p>UAE Equipment Rental provides CAT 226B bobcat and skid steer loader rental with operator for ' . esc_html( $city ) . ' jobsites. This service is built for contractors, site supervisors, villa projects, landscaping teams and maintenance work where a compact loader is faster than manual labour but a larger machine is unnecessary.</p>' .
		'<figure><img src="/wp-content/themes/dubai-bobcat-rental/assets/bobcat-yard-wide.jpg" alt="' . esc_attr( $image_alt ) . '"></figure>' .
		'<h2>What work the machine can do</h2><p>The CAT 226B can support site cleaning, bucket loading, sand shifting, loose material handling, small plot levelling, trench backfilling and compact surface preparation. Customers often search for this service as bobcat rental, while the correct machine category is skid steer loader.</p>' .
		'<h2>Areas covered in ' . esc_html( $city ) . '</h2><p>Common request areas include ' . esc_html( $areas ) . '. If your site is outside these areas, send a map pin and the working duration so delivery can be checked.</p>' .
		'<h2>CAT 226B machine details</h2><ul><li>Machine: Caterpillar CAT 226B skid steer loader</li><li>Rated operating capacity: 680 kg</li><li>Operating weight: 2641 kg</li><li>Common tools: bucket, forks, auger, broom, trencher or hammer depending on availability</li></ul>' .
		'<h2>Operator included</h2><p>The bobcat is supplied with an operator. This helps reduce booking confusion because machine suitability, access and work method can be discussed before dispatch. ' . esc_html( $extra_note ) . '</p>' .
		'<h2>Half-day, full-day and multi-day rental</h2><p>Booking duration depends on the site scope. Half-day, full-day and longer work can be discussed after you share the location, work type, access and estimated material volume.</p>' .
		dbr_seo_quote_block_html( $city ) .
		dbr_seo_internal_links_html() .
		dbr_seo_faq_html( $city );
}

function dbr_seo_ar_location_content( $city, $areas, $delivery_note ) {
	return '<h2>خدمة تأجير بوبكات في ' . esc_html( $city ) . '</h2>' .
		'<p>توفر UAE Equipment Rental تأجير بوبكات وسكيد ستير لودر CAT 226B مع مشغل لأعمال المواقع في ' . esc_html( $city ) . '. الخدمة مناسبة للمقاولين ومشرفي المواقع وأعمال الفلل والتنسيق والصيانة عندما تكون الآلة المدمجة أسرع من العمل اليدوي.</p>' .
		'<figure><img src="/wp-content/themes/dubai-bobcat-rental/assets/bobcat-yard-wide.jpg" alt="' . esc_attr( 'تأجير بوبكات CAT 226B مع مشغل في ' . $city ) . '"></figure>' .
		'<h2>الأعمال المناسبة</h2><p>يمكن استخدام CAT 226B في تنظيف المواقع، التحميل، نقل الرمل والمواد السائبة، التسوية، ردم الخنادق وتجهيز الأسطح الصغيرة. كثير من العملاء يسمونها بوبكات، والاسم الفني هو سكيد ستير لودر.</p>' .
		'<h2>المناطق المغطاة</h2><p>تشمل الطلبات الشائعة: ' . esc_html( $areas ) . '. أرسل موقع الخريطة ومدة العمل لمعرفة التوفر والتوصيل.</p>' .
		'<h2>تفاصيل الآلة</h2><ul><li>الآلة: Caterpillar CAT 226B</li><li>سعة التشغيل المقدرة: 680 كجم</li><li>وزن التشغيل: 2641 كجم</li><li>الملحقات حسب التوفر: بكت، فورك، أوجر، مكنسة، حفار خنادق أو هامر</li></ul>' .
		'<h2>المشغل متوفر</h2><p>يتم توفير البوبكات مع مشغل. مدة الحجز قد تكون نصف يوم أو يوم كامل أو عدة أيام حسب نطاق العمل. ' . esc_html( $delivery_note ) . '</p>' .
		'<h2>طلب السعر</h2><p>أرسل موقع العمل ونوع العمل والتاريخ والمدة والملحق المطلوب عبر واتساب: +971 54 738 8695.</p>';
}

$categories = array(
	'location' => array(
		'en' => dbr_seo_upsert_category( 'location-guides', 'Location Guides', 'en' ),
		'ar' => dbr_seo_upsert_category( 'ar-location-guides', 'أدلة المناطق', 'ar' ),
	),
	'pricing' => array(
		'en' => dbr_seo_upsert_category( 'rental-pricing', 'Rental Pricing', 'en' ),
		'ar' => dbr_seo_upsert_category( 'ar-rental-pricing', 'أسعار التأجير', 'ar' ),
	),
	'use_case' => array(
		'en' => dbr_seo_upsert_category( 'jobsite-use-cases', 'Jobsite Use Cases', 'en' ),
		'ar' => dbr_seo_upsert_category( 'ar-jobsite-use-cases', 'استخدامات المواقع', 'ar' ),
	),
	'machine' => array(
		'en' => dbr_seo_upsert_category( 'machine-guides', 'Machine Guides', 'en' ),
		'ar' => dbr_seo_upsert_category( 'ar-machine-guides', 'أدلة الآلات', 'ar' ),
	),
);

foreach ( $categories as $category ) {
	dbr_seo_link_term_translations( $category['en'], $category['ar'] );
}

$landing_pages = array(
	array( 'key' => 'dubai', 'slug' => 'bobcat-rental-dubai', 'city' => 'Dubai', 'areas' => 'Al Quoz, Jebel Ali, DIP, Ras Al Khor, Dubai Industrial City, Nad Al Hamar and active villa or contractor sites', 'delivery' => 'Delivery to Dubai is quoted by distance and working duration.', 'extra' => 'Dubai jobs are best quoted with a map pin because access and traffic timing can affect dispatch.', 'title' => 'Bobcat Rental Dubai With Operator', 'meta' => 'Bobcat Rental Dubai With Operator | UAE Equipment Rental', 'desc' => 'Hire CAT 226B bobcat / skid steer loader in Dubai with operator for site cleaning, loading, levelling and backfilling. Call or WhatsApp 24/7.' ),
	array( 'key' => 'sharjah', 'slug' => 'bobcat-rental-sharjah', 'city' => 'Sharjah', 'areas' => 'Industrial Area, Sajaa, Al Nahda, Muwaileh, Al Qasimia and nearby contractor sites', 'delivery' => 'Sharjah delivery is quoted after site location and timing are shared.', 'extra' => 'Sharjah industrial and construction jobs usually need clear access notes before dispatch.', 'title' => 'Bobcat Rental Sharjah With Operator', 'meta' => 'Bobcat Rental Sharjah With Operator | CAT 226B Hire', 'desc' => 'Rent CAT 226B bobcat in Sharjah with operator for loading, sand shifting, site cleaning, levelling and backfilling. WhatsApp for quote.' ),
	array( 'key' => 'abudhabi', 'slug' => 'bobcat-rental-abu-dhabi', 'city' => 'Abu Dhabi', 'areas' => 'Mussafah, Khalifa City, Baniyas, Al Mafraq, Al Ain road jobs and selected project sites', 'delivery' => 'Abu Dhabi is a long-distance delivery area and is quoted by job duration.', 'extra' => 'For Abu Dhabi, longer bookings are usually more practical because delivery distance is higher.', 'title' => 'Bobcat Rental Abu Dhabi With Operator', 'meta' => 'Bobcat Rental Abu Dhabi With Operator | UAE Equipment Rental', 'desc' => 'CAT 226B bobcat rental in Abu Dhabi with operator for compact site cleaning, loading, grading and backfilling. Request availability on WhatsApp.' ),
	array( 'key' => 'ajman', 'slug' => 'bobcat-rental-ajman', 'city' => 'Ajman', 'areas' => 'Ajman Industrial, Al Jurf, Al Rawda, Al Mowaihat and nearby construction areas', 'delivery' => 'Ajman delivery is quoted by map pin, access and working hours.', 'extra' => 'Ajman jobs can often be combined with Sharjah route planning if the timing is practical.', 'title' => 'Bobcat Rental Ajman With Operator', 'meta' => 'Bobcat Rental Ajman With Operator | CAT 226B Hire', 'desc' => 'Book CAT 226B bobcat rental in Ajman with operator for site cleaning, loading, levelling, sand shifting and compact material handling.' ),
	array( 'key' => 'fujairah', 'slug' => 'bobcat-rental-fujairah', 'city' => 'Fujairah', 'areas' => 'Dibba, Fujairah city, Khor Fakkan side enquiries, Al Hayl, Sakamkam and nearby sites', 'delivery' => 'Delivery can be free near Fujairah and Dibba depending on timing.', 'extra' => 'Fujairah and Dibba are the strongest local dispatch areas because the business is headquartered there.', 'title' => 'Bobcat Rental Fujairah and Dibba With Operator', 'meta' => 'Bobcat Rental Fujairah and Dibba | CAT 226B With Operator', 'desc' => 'Hire CAT 226B bobcat in Fujairah and Dibba with operator. Free delivery can be discussed near Fujairah. Call or WhatsApp 24/7.' ),
	array( 'key' => 'rak', 'slug' => 'bobcat-rental-rak', 'city' => 'Ras Al Khaimah', 'areas' => 'RAK industrial areas, Al Hamra, Mina Al Arab, Khuzam, Julphar and nearby construction sites', 'delivery' => 'RAK delivery is quoted after job duration and access are checked.', 'extra' => 'RAK enquiries should include whether the work is in a residential plot, industrial yard or open construction site.', 'title' => 'Bobcat Rental Ras Al Khaimah With Operator', 'meta' => 'Bobcat Rental RAK With Operator | CAT 226B Hire', 'desc' => 'CAT 226B bobcat rental in Ras Al Khaimah with operator for site cleaning, loading, levelling, backfilling and compact UAE jobs.' ),
);

$page_ids = array();
foreach ( $landing_pages as $page ) {
	$page_ids[ $page['key'] ]['en'] = dbr_seo_upsert_page(
		$page['slug'],
		$page['title'],
		dbr_seo_location_content( $page['city'], $page['areas'], $page['delivery'], $page['extra'] ),
		'CAT 226B bobcat rental with operator for ' . $page['city'] . ' site cleaning, loading, levelling and backfilling.',
		$page['meta'],
		$page['desc'],
		'en'
	);

	$page_ids[ $page['key'] ]['ar'] = dbr_seo_upsert_page(
		'ar-' . $page['slug'],
		'تأجير بوبكات في ' . $page['city'] . ' مع مشغل',
		dbr_seo_ar_location_content( $page['city'], $page['areas'], $page['delivery'] ),
		'تأجير بوبكات CAT 226B مع مشغل في ' . $page['city'] . ' لتنظيف المواقع والتحميل والتسوية والردم.',
		'تأجير بوبكات في ' . $page['city'] . ' مع مشغل | UAE Equipment Rental',
		'استأجر بوبكات CAT 226B في ' . $page['city'] . ' مع مشغل لأعمال تنظيف المواقع والتحميل والتسوية والردم. واتساب 24/7.',
		'ar'
	);

	dbr_seo_link_post_translations( $page_ids[ $page['key'] ]['en'], $page_ids[ $page['key'] ]['ar'] );
}

$service_pages = array(
	array(
		'key' => 'skiduae',
		'slug' => 'skid-steer-loader-rental-uae',
		'title' => 'Skid Steer Loader Rental UAE',
		'content' => '<h2>Skid steer loader rental across the UAE</h2><p>Hire a CAT 226B skid steer loader with operator for compact loading, grading, site cleaning, backfilling and material movement. This page targets customers who search by the correct machine category rather than the common term bobcat.</p><h2>Best-fit work</h2><ul><li>Loading sand, soil and loose material</li><li>Cleaning compact construction sites</li><li>Levelling small plots and villa sites</li><li>Backfilling trenches and preparing interlock bases</li></ul><h2>Why CAT 226B?</h2><p>The CAT 226B is compact enough for restricted sites and strong enough for practical material handling. The machine is supplied with operator and job fit is confirmed before dispatch.</p>' . dbr_seo_quote_block_html( 'the UAE' ) . dbr_seo_internal_links_html() . dbr_seo_faq_html( 'the UAE' ),
		'excerpt' => 'CAT 226B skid steer loader rental with operator across the UAE.',
		'meta' => 'Skid Steer Loader Rental UAE | CAT 226B With Operator',
		'desc' => 'Hire CAT 226B skid steer loader in the UAE with operator for site cleaning, loading, levelling and backfilling. WhatsApp for quote.',
		'ar_title' => 'تأجير سكيد ستير لودر في الإمارات',
		'ar_content' => '<h2>تأجير سكيد ستير لودر داخل الإمارات</h2><p>استأجر CAT 226B مع مشغل لأعمال التحميل والتنظيف والردم والتسوية ونقل المواد. هذه الصفحة تخدم العملاء الذين يبحثون بالاسم الفني للآلة وليس فقط كلمة بوبكات.</p><h2>الأعمال المناسبة</h2><ul><li>تحميل الرمل والتربة</li><li>تنظيف مواقع البناء الصغيرة</li><li>تسوية مواقع الفلل</li><li>ردم الخنادق وتجهيز الإنترلوك</li></ul><h2>طلب السعر</h2><p>أرسل الموقع ونوع العمل والتاريخ والمدة عبر واتساب: +971 54 738 8695.</p>',
	),
	array(
		'key' => 'catrental',
		'slug' => 'cat-226b-rental-uae',
		'title' => 'CAT 226B Rental UAE',
		'content' => '<h2>CAT 226B rental with operator</h2><p>The CAT 226B is the available skid steer loader for UAE Equipment Rental. It is commonly requested as bobcat rental and is suitable for compact jobsite support where manoeuvrability matters.</p><h2>Machine details</h2><ul><li>Model: Caterpillar 226B</li><li>Rated operating capacity: 680 kg</li><li>Tipping load: 1360 kg</li><li>Operating weight: 2641 kg</li><li>Auxiliary hydraulics for compatible work tools</li></ul><h2>Rental uses</h2><p>Use this machine for site cleaning, loading, sand shifting, levelling, backfilling, landscaping preparation and compact material handling.</p>' . dbr_seo_quote_block_html( 'the UAE' ) . dbr_seo_internal_links_html() . dbr_seo_faq_html( 'the UAE' ),
		'excerpt' => 'CAT 226B rental UAE with operator for compact construction and site cleaning jobs.',
		'meta' => 'CAT 226B Rental UAE | Bobcat With Operator',
		'desc' => 'Rent CAT 226B skid steer loader in the UAE with operator. View specs, uses and WhatsApp quote path for site cleaning and loading work.',
		'ar_title' => 'تأجير CAT 226B في الإمارات',
		'ar_content' => '<h2>تأجير CAT 226B مع مشغل</h2><p>الآلة المتوفرة هي سكيد ستير لودر CAT 226B، ويبحث عنها كثير من العملاء باسم بوبكات. تناسب أعمال المواقع الصغيرة التي تحتاج مناورة وسرعة تحميل.</p><h2>المواصفات</h2><ul><li>الموديل: Caterpillar 226B</li><li>سعة التشغيل المقدرة: 680 كجم</li><li>حمل الانقلاب: 1360 كجم</li><li>وزن التشغيل: 2641 كجم</li></ul><p>للحجز أرسل تفاصيل العمل عبر واتساب.</p>',
	),
);

foreach ( $service_pages as $page ) {
	$page_ids[ $page['key'] ]['en'] = dbr_seo_upsert_page( $page['slug'], $page['title'], $page['content'], $page['excerpt'], $page['meta'], $page['desc'], 'en' );
	$page_ids[ $page['key'] ]['ar'] = dbr_seo_upsert_page( 'ar-' . $page['slug'], $page['ar_title'], $page['ar_content'], $page['excerpt'], $page['ar_title'] . ' | UAE Equipment Rental', $page['desc'], 'ar' );
	dbr_seo_link_post_translations( $page_ids[ $page['key'] ]['en'], $page_ids[ $page['key'] ]['ar'] );
}

$areas_page = get_page_by_path( 'service-areas', OBJECT, 'page' );
if ( $areas_page ) {
	$areas_content = '<p>The business is headquartered in Dibba, Fujairah and can discuss bobcat delivery across the UAE. Delivery can be free near Fujairah, while other locations are quoted by distance, timing and job duration.</p>' .
		'<h2>Bobcat rental location pages</h2><ul>' .
		'<li><a href="/bobcat-rental-dubai/">Bobcat rental Dubai</a></li>' .
		'<li><a href="/bobcat-rental-sharjah/">Bobcat rental Sharjah</a></li>' .
		'<li><a href="/bobcat-rental-abu-dhabi/">Bobcat rental Abu Dhabi</a></li>' .
		'<li><a href="/bobcat-rental-ajman/">Bobcat rental Ajman</a></li>' .
		'<li><a href="/bobcat-rental-fujairah/">Bobcat rental Fujairah and Dibba</a></li>' .
		'<li><a href="/bobcat-rental-rak/">Bobcat rental Ras Al Khaimah</a></li>' .
		'</ul>';
	wp_update_post(
		array(
			'ID'           => $areas_page->ID,
			'post_content' => $areas_content,
		)
	);
}

function dbr_seo_article_content( $article ) {
	$sections = '';
	foreach ( $article['sections'] as $section ) {
		$sections .= '<h2>' . esc_html( $section[0] ) . '</h2><p>' . esc_html( $section[1] ) . '</p>';
	}

	return '<p>' . esc_html( $article['intro'] ) . '</p>' .
		$sections .
		'<h2>Quote checklist</h2><ul><li>Send a Google Maps pin or exact location.</li><li>Describe the work: loading, site cleaning, levelling, backfilling or material handling.</li><li>Share the preferred date, expected duration and site access notes.</li><li>Mention whether bucket, forks, auger, broom, trencher or hammer support is needed.</li></ul>' .
		dbr_seo_internal_links_html() .
		'<p><strong>Need availability?</strong> Call or WhatsApp UAE Equipment Rental on +971 54 738 8695.</p>';
}

function dbr_seo_ar_article_content( $article ) {
	return '<p>' . esc_html( $article['ar_intro'] ) . '</p>' .
		'<h2>النقاط المهمة قبل الحجز</h2><p>أرسل موقع العمل، نوع الخدمة، التاريخ، المدة المتوقعة، ملاحظات الدخول والملحق المطلوب. هذه التفاصيل تساعد على تأكيد مناسبة CAT 226B قبل مناقشة التوصيل.</p>' .
		'<h2>الأعمال المناسبة</h2><p>يمكن استخدام البوبكات في التنظيف والتحميل والتسوية والردم ونقل الرمل والمواد السائبة داخل مواقع العمل الصغيرة والمتوسطة.</p>' .
		'<h2>طلب السعر</h2><p>للحصول على سعر عملي، اتصل أو أرسل واتساب إلى +971 54 738 8695 مع صورة أو رابط خريطة للموقع.</p>';
}

$articles = array(
	array( 'cat' => 'pricing', 'slug' => 'bobcat-rental-price-dubai', 'title' => 'Bobcat Rental Price in Dubai: What Affects the Cost?', 'focus' => 'bobcat rental price Dubai', 'excerpt' => 'Learn what affects bobcat rental price in Dubai, including duration, delivery, operator, attachment and site access.', 'intro' => 'Bobcat rental price in Dubai changes from job to job because access, duration, machine timing and delivery all matter. A compact CAT 226B with operator is usually quoted after the job location and work scope are clear.', 'sections' => array( array( 'Duration changes the quote', 'Half-day, full-day and multi-day bookings are priced differently because operator time, transport and machine scheduling are planned around the total working window.' ), array( 'Delivery and site access matter', 'A Dubai job in Al Quoz is different from a restricted villa site or a long-distance industrial project. The clearer the map pin and access notes, the faster the quote can be confirmed.' ), array( 'Attachments affect planning', 'Bucket work is common, but forks, auger, broom, trencher or hammer requirements should be mentioned early so availability can be checked.' ) ), 'ar_title' => 'أسعار تأجير البوبكات في دبي: ما الذي يؤثر على التكلفة؟', 'ar_intro' => 'يتغير سعر تأجير البوبكات في دبي حسب الموقع والمدة ونوع العمل والتوصيل والملحق المطلوب.' ),
	array( 'cat' => 'pricing', 'slug' => 'bobcat-rental-price-uae', 'title' => 'Bobcat Rental Price UAE Guide for Contractors', 'focus' => 'bobcat rental price UAE', 'excerpt' => 'A UAE contractor guide to bobcat rental price factors and quote preparation.', 'intro' => 'Across the UAE, bobcat rental pricing depends on distance, timing, job type and booking duration. The best quote starts with clear job details rather than a single broad question.', 'sections' => array( array( 'Distance affects delivery', 'Fujairah and Dibba can be easier dispatch areas, while Dubai, Sharjah and Abu Dhabi need delivery planning by distance and timing.' ), array( 'Work type affects machine fit', 'Site cleaning, loading and levelling are not quoted the same way because each job needs a different amount of machine time.' ), array( 'Operator is included', 'The CAT 226B is supplied with operator, so the quote covers practical machine support rather than bare equipment hire.' ) ), 'ar_title' => 'دليل أسعار تأجير البوبكات في الإمارات للمقاولين', 'ar_intro' => 'يعتمد سعر تأجير البوبكات في الإمارات على المسافة والتوقيت ونوع العمل ومدة الحجز.' ),
	array( 'cat' => 'location', 'slug' => 'bobcat-rental-dubai-with-operator-guide', 'title' => 'Bobcat Rental Dubai With Operator: Booking Guide', 'focus' => 'bobcat rental Dubai with operator', 'excerpt' => 'How to book bobcat rental in Dubai with operator and what details to send.', 'intro' => 'Bobcat rental in Dubai works best when the operator, machine, access and job scope are confirmed together before dispatch. This avoids sending the wrong machine for a tight or unsuitable site.', 'sections' => array( array( 'Send the exact location', 'A map pin helps confirm delivery timing and whether the site can be reached safely.' ), array( 'Explain the work type', 'Cleaning debris, shifting sand, levelling and loading loose material all require slightly different planning.' ), array( 'Mention preferred working hours', 'Some Dubai sites have access restrictions, security gates or working-hour limits that should be shared before booking.' ) ), 'ar_title' => 'تأجير بوبكات دبي مع مشغل: دليل الحجز', 'ar_intro' => 'أفضل طريقة لحجز بوبكات في دبي هي إرسال الموقع ونوع العمل والمدة وملاحظات الدخول قبل الإرسال.' ),
	array( 'cat' => 'location', 'slug' => 'bobcat-rental-sharjah-guide', 'title' => 'Bobcat Rental Sharjah for Industrial and Construction Sites', 'focus' => 'bobcat rental Sharjah', 'excerpt' => 'Bobcat rental guidance for Sharjah industrial and construction jobsites.', 'intro' => 'Sharjah bobcat rental enquiries often come from industrial yards, active construction sites and compact material handling jobs where a skid steer loader is practical.', 'sections' => array( array( 'Industrial access notes help', 'Gate size, loading area and surface condition should be shared before dispatch.' ), array( 'Common Sharjah jobs', 'Bucket loading, debris movement, levelling and site clean-up are common requests.' ), array( 'Delivery is quoted by location', 'Sharjah delivery can be practical when the exact map pin, date and duration are known.' ) ), 'ar_title' => 'تأجير بوبكات الشارقة للمواقع الصناعية والإنشائية', 'ar_intro' => 'طلبات الشارقة غالبا تكون من المواقع الصناعية وأعمال التحميل والتنظيف داخل مواقع البناء.' ),
	array( 'cat' => 'location', 'slug' => 'bobcat-rental-abu-dhabi-guide', 'title' => 'Bobcat Rental Abu Dhabi: When It Makes Sense', 'focus' => 'bobcat rental Abu Dhabi', 'excerpt' => 'When CAT 226B bobcat rental makes sense for Abu Dhabi projects.', 'intro' => 'Abu Dhabi bobcat rental is most practical when the job duration justifies long-distance delivery and the work is suited to a compact loader.', 'sections' => array( array( 'Long-distance planning', 'Abu Dhabi bookings should include expected hours or days so transport can be quoted sensibly.' ), array( 'Suitable project types', 'Mussafah yards, villa plots and compact construction work can fit the CAT 226B when access is suitable.' ), array( 'Confirm before dispatch', 'Photos and site access details help avoid wasted transport on unsuitable jobs.' ) ), 'ar_title' => 'تأجير بوبكات أبوظبي: متى يكون مناسبا؟', 'ar_intro' => 'تأجير البوبكات في أبوظبي يكون عمليا عندما تكون مدة العمل مناسبة لمسافة التوصيل ونوع العمل مناسب للآلة.' ),
	array( 'cat' => 'location', 'slug' => 'bobcat-rental-fujairah-dibba-guide', 'title' => 'Bobcat Rental Fujairah and Dibba: Local Delivery Guide', 'focus' => 'bobcat rental Fujairah', 'excerpt' => 'Local bobcat rental guidance for Fujairah and Dibba jobsites.', 'intro' => 'Fujairah and Dibba are strong service areas because UAE Equipment Rental is headquartered in Dibba. Local delivery can be easier and may be free near Fujairah depending on timing.', 'sections' => array( array( 'Best local jobs', 'Site cleaning, villa plot levelling, sand shifting and small contractor support are good local fit cases.' ), array( 'Free delivery discussion', 'Near Fujairah and Dibba, delivery can be discussed differently from long-distance emirate work.' ), array( 'Still share full details', 'Even local jobs need location, access, work type and duration to confirm availability.' ) ), 'ar_title' => 'تأجير بوبكات الفجيرة ودبا: دليل التوصيل المحلي', 'ar_intro' => 'الفجيرة ودبا من أقوى مناطق الخدمة لأن مقر العمل في دبا ويمكن مناقشة التوصيل المحلي بسهولة.' ),
	array( 'cat' => 'machine', 'slug' => 'skid-steer-loader-rental-uae-guide', 'title' => 'Skid Steer Loader Rental UAE: Practical Buyer Guide', 'focus' => 'skid steer loader rental UAE', 'excerpt' => 'A practical guide to skid steer loader rental in the UAE.', 'intro' => 'Skid steer loader rental in the UAE is useful for compact jobsites that need fast movement, loading and levelling without bringing in oversized earthmoving equipment.', 'sections' => array( array( 'Bobcat is the common search term', 'Many buyers say bobcat even when the technical category is skid steer loader.' ), array( 'CAT 226B machine fit', 'The CAT 226B is compact, manoeuvrable and suitable for controlled site support tasks.' ), array( 'Operator-led rental', 'Operator support helps customers confirm safe access and realistic output.' ) ), 'ar_title' => 'تأجير سكيد ستير لودر في الإمارات: دليل عملي', 'ar_intro' => 'تأجير السكيد ستير في الإمارات مناسب للمواقع الصغيرة التي تحتاج تحميل وتسوية وتنظيف بسرعة.' ),
	array( 'cat' => 'machine', 'slug' => 'cat-226b-rental-uae-specs-guide', 'title' => 'CAT 226B Rental UAE: Specs, Capacity and Uses', 'focus' => 'CAT 226B rental UAE', 'excerpt' => 'CAT 226B rental guide covering capacity, weight and UAE jobsite uses.', 'intro' => 'CAT 226B rental is useful for buyers who want a known machine model rather than a vague bobcat request. The available machine is a Caterpillar 226B skid steer loader.', 'sections' => array( array( 'Capacity anchors', 'Rated operating capacity is 680 kg, tipping load is 1360 kg and operating weight is 2641 kg.' ), array( 'Common uses', 'The machine supports site cleaning, loading, sand shifting, backfilling and compact surface preparation.' ), array( 'Attachment planning', 'Auxiliary hydraulics support compatible work tools depending on availability.' ) ), 'ar_title' => 'تأجير CAT 226B في الإمارات: المواصفات والاستخدامات', 'ar_intro' => 'تأجير CAT 226B مناسب للعملاء الذين يريدون معرفة موديل الآلة وسعتها واستخداماتها قبل الحجز.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-for-site-cleaning-dubai-guide', 'title' => 'Bobcat for Site Cleaning in Dubai', 'focus' => 'site cleaning bobcat rental Dubai', 'excerpt' => 'How bobcat rental helps site cleaning jobs in Dubai.', 'intro' => 'A bobcat can make site cleaning in Dubai faster when loose debris, sand, soil and construction waste need to be gathered and moved within a compact area.', 'sections' => array( array( 'When it helps', 'It helps when manual labour is too slow and a full-size loader is too large for the available space.' ), array( 'Site access matters', 'The machine needs safe entry, turning space and a working surface that suits a compact loader.' ), array( 'Photos speed up quoting', 'Photos show debris volume and whether the bucket can reach the required area.' ) ), 'ar_title' => 'بوبكات لتنظيف المواقع في دبي', 'ar_intro' => 'يساعد البوبكات في تنظيف مواقع دبي عندما توجد مخلفات ورمل وتربة تحتاج إلى جمع ونقل بسرعة.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-for-sand-shifting-uae', 'title' => 'Bobcat for Sand Shifting in UAE Jobsites', 'focus' => 'bobcat sand shifting UAE', 'excerpt' => 'Use CAT 226B bobcat for sand shifting and loose material movement.', 'intro' => 'Sand shifting is one of the common reasons customers request bobcat rental in the UAE. A compact loader can move loose material faster than manual handling.', 'sections' => array( array( 'Material volume matters', 'A clear estimate or photo helps decide whether the CAT 226B is the right size.' ), array( 'Short-distance movement', 'Skid steers are useful for moving material within the same plot or nearby loading area.' ), array( 'Surface condition', 'Soft sand, slopes and restricted access should be mentioned before booking.' ) ), 'ar_title' => 'بوبكات لنقل الرمل داخل مواقع الإمارات', 'ar_intro' => 'نقل الرمل من أكثر أسباب طلب البوبكات في الإمارات لأنه يسرع حركة المواد السائبة داخل الموقع.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-for-levelling-dubai', 'title' => 'Bobcat for Levelling Work in Dubai', 'focus' => 'bobcat levelling Dubai', 'excerpt' => 'Guide to using bobcat rental for levelling and grading work in Dubai.', 'intro' => 'Bobcat levelling work in Dubai is practical for compact plots, landscape preparation and small surface corrections where a skid steer loader can move and spread material efficiently.', 'sections' => array( array( 'Not every levelling job is the same', 'The required finish, material type and site size affect how much machine time is needed.' ), array( 'Good access improves output', 'The machine works better when it can enter, turn and move material without repeated interruptions.' ), array( 'Operator discussion helps', 'Share the expected finish so the operator can confirm whether the machine is suitable.' ) ), 'ar_title' => 'بوبكات لأعمال التسوية في دبي', 'ar_intro' => 'يناسب البوبكات أعمال التسوية الصغيرة وتجهيز المساحات وأعمال التنسيق في دبي عند توفر دخول مناسب.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-for-backfilling-trenches', 'title' => 'Bobcat for Trench Backfilling in UAE', 'focus' => 'bobcat backfilling UAE', 'excerpt' => 'How a CAT 226B bobcat supports trench backfilling and compact site work.', 'intro' => 'Trench backfilling is a good use case for a compact bobcat when material needs to be pushed, moved and spread in a controlled work area.', 'sections' => array( array( 'Trench size matters', 'Depth, width and access determine whether the machine can support the work safely.' ), array( 'Material type matters', 'Loose sand and soil are different from mixed debris or compacted material.' ), array( 'Plan the working route', 'The operator needs enough space to move between the stockpile and trench area.' ) ), 'ar_title' => 'بوبكات لردم الخنادق في الإمارات', 'ar_intro' => 'يعد ردم الخنادق من الاستخدامات المناسبة للبوبكات عندما تكون المساحة محدودة والمواد قريبة.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-for-landscaping-uae', 'title' => 'Bobcat Rental for Landscaping Work in UAE', 'focus' => 'bobcat landscaping UAE', 'excerpt' => 'Use bobcat rental for landscape preparation, soil movement and compact grading.', 'intro' => 'Landscaping projects often need small machine support for soil movement, surface preparation and cleanup before finishing work starts.', 'sections' => array( array( 'Villa and garden projects', 'Compact loaders are useful where the work area is too small for larger equipment.' ), array( 'Soil and aggregate movement', 'The machine can move loose material between stockpiles and preparation areas.' ), array( 'Protect finished areas', 'Access should be planned so the machine does not damage completed paving or landscaping.' ) ), 'ar_title' => 'تأجير بوبكات لأعمال تنسيق المواقع في الإمارات', 'ar_intro' => 'تحتاج أعمال التنسيق أحيانا إلى آلة صغيرة لنقل التربة وتجهيز السطح وتنظيف الموقع قبل التشطيب.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-for-interlock-preparation', 'title' => 'Bobcat for Interlock Base Preparation', 'focus' => 'bobcat interlock preparation UAE', 'excerpt' => 'How bobcat rental supports interlock base preparation and material movement.', 'intro' => 'Before interlock work, sites often need loose material movement, surface preparation and cleanup. A compact bobcat can support the early preparation stages.', 'sections' => array( array( 'Base preparation support', 'The machine can help distribute material before final compaction and finishing by the specialist team.' ), array( 'Access and levels', 'Share the required area and level expectations so suitability can be confirmed.' ), array( 'Avoid overpromising finish', 'A skid steer supports preparation; final interlock quality depends on the full civil and compaction process.' ) ), 'ar_title' => 'بوبكات لتجهيز قاعدة الإنترلوك', 'ar_intro' => 'قبل أعمال الإنترلوك قد تحتاج المواقع إلى نقل مواد وتجهيز سطح وتنظيف، ويمكن للبوبكات دعم هذه المرحلة.' ),
	array( 'cat' => 'machine', 'slug' => 'bobcat-vs-skid-steer-loader-uae', 'title' => 'Bobcat vs Skid Steer Loader in UAE Searches', 'focus' => 'bobcat vs skid steer loader', 'excerpt' => 'Understand the difference between bobcat and skid steer loader search terms.', 'intro' => 'In UAE rental searches, bobcat is often used as a general word for a compact loader. Skid steer loader is the machine category, and CAT 226B is the actual model.', 'sections' => array( array( 'Bobcat is a common market word', 'Customers may ask for bobcat rental even when they are not asking for the Bobcat brand specifically.' ), array( 'Skid steer is the technical type', 'The category describes the compact loader design and how it moves.' ), array( 'Why the website uses both', 'Using both terms helps customers find the service while still explaining the correct machine type.' ) ), 'ar_title' => 'بوبكات أم سكيد ستير لودر في بحث الإمارات', 'ar_intro' => 'في الإمارات يستخدم العملاء كلمة بوبكات كثيرا، بينما الاسم الفني للآلة هو سكيد ستير لودر.' ),
	array( 'cat' => 'machine', 'slug' => 'bobcat-vs-wheel-loader-dubai', 'title' => 'Bobcat vs Wheel Loader: Which Machine Fits a Dubai Site?', 'focus' => 'bobcat vs wheel loader Dubai', 'excerpt' => 'Compare bobcat and wheel loader suitability for compact Dubai jobsites.', 'intro' => 'A bobcat and a wheel loader are not the same jobsite solution. The right choice depends on access, material volume, loading height and required working space.', 'sections' => array( array( 'Bobcat for compact spaces', 'The CAT 226B fits smaller plots and tighter access where manoeuvrability matters.' ), array( 'Wheel loader for larger volumes', 'A wheel loader may be better for high-volume loading and larger open sites.' ), array( 'Quote with site details', 'Photos and volume estimates help decide whether a compact loader is enough.' ) ), 'ar_title' => 'بوبكات أم ويل لودر: أي آلة تناسب موقع دبي؟', 'ar_intro' => 'اختيار البوبكات أو الويل لودر يعتمد على المساحة وحجم المواد وارتفاع التحميل والدخول للموقع.' ),
	array( 'cat' => 'machine', 'slug' => 'skid-steer-attachments-bucket-forks-auger', 'title' => 'Skid Steer Attachments: Bucket, Forks, Auger and More', 'focus' => 'skid steer attachments UAE', 'excerpt' => 'Understand common skid steer attachments and when to request them.', 'intro' => 'Skid steer attachments expand what a compact loader can do, but availability must always be confirmed before booking.', 'sections' => array( array( 'Bucket attachment', 'Bucket work is common for loading, cleaning and loose material movement.' ), array( 'Forks and auger', 'Forks support pallet movement while augers can support post-hole style work when available.' ), array( 'Broom, trencher and hammer', 'These tools may be useful for specific jobs but should be requested early.' ) ), 'ar_title' => 'ملحقات السكيد ستير: بكت وفورك وأوجر وأكثر', 'ar_intro' => 'توسع ملحقات السكيد ستير استخدامات الآلة، لكن يجب تأكيد توفرها قبل الحجز.' ),
	array( 'cat' => 'location', 'slug' => 'how-to-book-bobcat-with-operator-uae', 'title' => 'How to Book Bobcat With Operator in UAE', 'focus' => 'bobcat with operator UAE', 'excerpt' => 'Step-by-step guide to booking bobcat rental with operator in the UAE.', 'intro' => 'Booking bobcat with operator in the UAE is simple when you send the right information from the start.', 'sections' => array( array( 'Start with location', 'A map pin is the first detail needed to check delivery and timing.' ), array( 'Describe the job', 'Explain whether it is cleaning, loading, levelling, backfilling, sand shifting or another compact loader task.' ), array( 'Confirm duration', 'Half-day, full-day and multi-day work can be discussed after scope is clear.' ) ), 'ar_title' => 'طريقة حجز بوبكات مع مشغل في الإمارات', 'ar_intro' => 'حجز بوبكات مع مشغل في الإمارات يصبح أسرع عندما ترسل المعلومات الصحيحة من البداية.' ),
	array( 'cat' => 'location', 'slug' => 'how-to-book-bobcat-rental-whatsapp', 'title' => 'How to Request a Bobcat Rental Quote on WhatsApp', 'focus' => 'bobcat rental WhatsApp quote', 'excerpt' => 'What to send on WhatsApp for a faster bobcat rental quote.', 'intro' => 'WhatsApp is often the fastest way to request bobcat rental because you can send location, photos and job details in one conversation.', 'sections' => array( array( 'Send a map pin', 'The quote starts with knowing where the machine needs to go.' ), array( 'Add photos or video', 'Photos show access, surface condition and material volume.' ), array( 'Mention the expected time', 'Date and duration help confirm machine and operator availability.' ) ), 'ar_title' => 'طريقة طلب سعر تأجير بوبكات عبر واتساب', 'ar_intro' => 'واتساب هو أسرع طريقة لطلب سعر البوبكات لأنك تستطيع إرسال الموقع والصور وتفاصيل العمل.' ),
	array( 'cat' => 'pricing', 'slug' => 'minimum-booking-duration-bobcat-uae', 'title' => 'Minimum Booking Duration for Bobcat Rental in UAE', 'focus' => 'minimum bobcat rental duration UAE', 'excerpt' => 'Understand half-day, full-day and multi-day bobcat rental booking in the UAE.', 'intro' => 'Minimum booking duration depends on the job location, travel time and work scope. A small job near Fujairah may be different from a long-distance Dubai or Abu Dhabi booking.', 'sections' => array( array( 'Half-day bookings', 'Half-day may work when the job is compact, local and easy to access.' ), array( 'Full-day bookings', 'Full-day is often more practical when delivery distance or work volume is higher.' ), array( 'Multi-day bookings', 'Longer bookings can make sense for ongoing contractor, landscaping or cleanup work.' ) ), 'ar_title' => 'أقل مدة حجز لتأجير البوبكات في الإمارات', 'ar_intro' => 'أقل مدة حجز تعتمد على موقع العمل ووقت التوصيل وحجم العمل المطلوب.' ),
	array( 'cat' => 'pricing', 'slug' => 'bobcat-delivery-cost-uae', 'title' => 'Bobcat Delivery Cost in UAE: What You Need to Know', 'focus' => 'bobcat delivery cost UAE', 'excerpt' => 'What affects bobcat delivery cost across Dubai, Sharjah, Fujairah and other emirates.', 'intro' => 'Bobcat delivery cost in the UAE depends on distance, timing and job duration. Delivery can be free near Fujairah, while other emirates are quoted case by case.', 'sections' => array( array( 'Distance is the main factor', 'A nearby Fujairah job is different from Dubai, Abu Dhabi or Ras Al Khaimah delivery.' ), array( 'Duration affects practicality', 'A longer job may make transport more practical than a very short distant job.' ), array( 'Send accurate location', 'A map pin avoids guesswork and helps confirm the route.' ) ), 'ar_title' => 'تكلفة توصيل البوبكات في الإمارات', 'ar_intro' => 'تعتمد تكلفة توصيل البوبكات على المسافة والتوقيت ومدة العمل، وقد يكون التوصيل مجانيا قرب الفجيرة.' ),
	array( 'cat' => 'use_case', 'slug' => 'construction-site-cleaning-machine-dubai', 'title' => 'Best Machine for Construction Site Cleaning in Dubai', 'focus' => 'construction site cleaning machine Dubai', 'excerpt' => 'When a bobcat is the best machine for compact construction site cleaning in Dubai.', 'intro' => 'For compact construction site cleaning in Dubai, a CAT 226B bobcat can be a practical choice when loose material and debris need to be moved quickly.', 'sections' => array( array( 'Why compact size matters', 'Many Dubai sites have tight access, nearby walls, parked vehicles or restricted working zones.' ), array( 'What it can move', 'Loose debris, sand, soil and light construction waste are common cleaning tasks.' ), array( 'When to use another solution', 'If the volume is very large or the debris is heavy demolition waste, a different machine may be needed.' ) ), 'ar_title' => 'أفضل آلة لتنظيف مواقع البناء في دبي', 'ar_intro' => 'في مواقع دبي الصغيرة يمكن أن يكون بوبكات CAT 226B خيارا عمليا لتنظيف المخلفات ونقل المواد السائبة.' ),
	array( 'cat' => 'use_case', 'slug' => 'villa-construction-bobcat-rental-uae', 'title' => 'Bobcat Rental for Villa Construction Sites in UAE', 'focus' => 'villa construction bobcat rental UAE', 'excerpt' => 'How bobcat rental supports villa construction, landscaping and cleanup work.', 'intro' => 'Villa construction sites often need compact machine support because access is limited and the work happens close to walls, gates and finished areas.', 'sections' => array( array( 'Useful stages', 'Bobcat rental can help with early cleanup, sand movement, levelling and backfilling.' ), array( 'Access must be checked', 'Gate width, ground condition and turning space should be shared before booking.' ), array( 'Protect finished work', 'Plan the route to avoid damaging tiles, interlock or landscaping.' ) ), 'ar_title' => 'تأجير بوبكات لمواقع بناء الفلل في الإمارات', 'ar_intro' => 'تحتاج مواقع الفلل غالبا إلى آلة صغيرة بسبب محدودية الدخول وقرب العمل من الجدران والبوابات.' ),
	array( 'cat' => 'use_case', 'slug' => 'small-site-equipment-rental-dubai', 'title' => 'Small Site Equipment Rental Dubai: Why Bobcat Fits', 'focus' => 'small site equipment rental Dubai', 'excerpt' => 'Why a compact bobcat can be the right rental machine for small Dubai sites.', 'intro' => 'Small Dubai sites do not always need large heavy equipment. A compact CAT 226B can fit where the work needs speed, manoeuvrability and controlled material movement.', 'sections' => array( array( 'Compact but useful', 'The machine can clean, load, push and spread material in restricted spaces.' ), array( 'Operator included', 'Operator support helps use the machine efficiently and safely.' ), array( 'Quote by scope', 'Small sites vary widely, so photos and access notes are important.' ) ), 'ar_title' => 'تأجير معدات للمواقع الصغيرة في دبي: لماذا يناسب البوبكات؟', 'ar_intro' => 'المواقع الصغيرة في دبي لا تحتاج دائما معدات كبيرة، ويمكن للبوبكات المدمج إنجاز أعمال كثيرة بسرعة.' ),
	array( 'cat' => 'machine', 'slug' => 'compact-loader-rental-uae', 'title' => 'Compact Loader Rental UAE: CAT 226B Bobcat Guide', 'focus' => 'compact loader rental UAE', 'excerpt' => 'A guide to compact loader rental in UAE using CAT 226B bobcat with operator.', 'intro' => 'Compact loader rental is useful when the job needs more power than labour but less size than a full loader. CAT 226B sits in that practical middle ground.', 'sections' => array( array( 'Best for restricted access', 'The machine is suited to compact plots, yards and job areas where turning space is limited.' ), array( 'Material movement support', 'It can support loose material movement, site cleanup and preparation work.' ), array( 'Ask with work details', 'The quote depends on location, surface, duration and machine fit.' ) ), 'ar_title' => 'تأجير لودر صغير في الإمارات: دليل CAT 226B', 'ar_intro' => 'تأجير لودر صغير مناسب عندما يحتاج العمل قوة أكبر من العمالة وحجما أصغر من اللودر الكبير.' ),
	array( 'cat' => 'machine', 'slug' => 'cat-226b-operating-capacity-680kg', 'title' => 'CAT 226B Operating Capacity: What 680 kg Means', 'focus' => 'CAT 226B operating capacity', 'excerpt' => 'Understand CAT 226B rated operating capacity and job planning.', 'intro' => 'The CAT 226B rated operating capacity is 680 kg. This number helps buyers understand the machine category, but real job suitability also depends on material, surface and access.', 'sections' => array( array( 'Capacity is not the whole story', 'Loose sand, soil, debris and pallet material behave differently in real work.' ), array( 'Operating weight matters', 'At 2641 kg operating weight, site surface and access should be suitable.' ), array( 'Operator judgement matters', 'The operator helps confirm safe and practical use on the actual site.' ) ), 'ar_title' => 'سعة تشغيل CAT 226B: ماذا يعني 680 كجم؟', 'ar_intro' => 'سعة التشغيل المقدرة لـ CAT 226B هي 680 كجم، لكنها ليست العامل الوحيد لتحديد مناسبة الآلة.' ),
	array( 'cat' => 'use_case', 'slug' => 'bobcat-safety-site-access-checklist', 'title' => 'Bobcat Site Access Checklist Before Booking', 'focus' => 'bobcat site access checklist', 'excerpt' => 'Use this checklist before booking bobcat rental for UAE jobsites.', 'intro' => 'A simple site access checklist can prevent delays when booking bobcat rental. The goal is to confirm that the CAT 226B can enter and work safely.', 'sections' => array( array( 'Check gate width', 'Tell the rental team if access is through a narrow gate, ramp or shared road.' ), array( 'Check surface condition', 'Soft sand, wet areas, slopes and loose edges should be mentioned.' ), array( 'Check working room', 'The machine needs room to turn, load and move material efficiently.' ) ), 'ar_title' => 'قائمة فحص دخول الموقع قبل حجز البوبكات', 'ar_intro' => 'قائمة فحص بسيطة تساعد على تجنب التأخير قبل حجز البوبكات والتأكد من إمكانية دخول CAT 226B بأمان.' ),
	array( 'cat' => 'location', 'slug' => 'emergency-bobcat-rental-dubai', 'title' => 'Urgent Bobcat Rental Dubai: What to Send First', 'focus' => 'urgent bobcat rental Dubai', 'excerpt' => 'What to send first when you need urgent bobcat rental in Dubai.', 'intro' => 'Urgent bobcat rental in Dubai needs clear details. The faster you send the right information, the faster availability and delivery can be checked.', 'sections' => array( array( 'Send location first', 'A map pin confirms route and whether the timing is realistic.' ), array( 'Send work photos', 'Photos quickly show the job type and material volume.' ), array( 'Confirm duration', 'Urgent short jobs and urgent full-day jobs are planned differently.' ) ), 'ar_title' => 'تأجير بوبكات عاجل في دبي: ماذا ترسل أولا؟', 'ar_intro' => 'عند الحاجة إلى بوبكات بشكل عاجل في دبي، أرسل الموقع والصور والمدة لتسريع التأكيد.' ),
	array( 'cat' => 'pricing', 'slug' => 'monthly-bobcat-rental-uae', 'title' => 'Monthly Bobcat Rental UAE: When Longer Booking Helps', 'focus' => 'monthly bobcat rental UAE', 'excerpt' => 'When monthly or multi-day bobcat rental makes sense for UAE contractors.', 'intro' => 'Monthly bobcat rental can make sense for contractors with repeated loading, cleanup, landscaping or material movement work across an active project.', 'sections' => array( array( 'Repeated work saves coordination', 'Longer bookings reduce repeated delivery discussions.' ), array( 'Project planning matters', 'Share expected weekly tasks, location and working hours before asking for monthly pricing.' ), array( 'Machine fit still matters', 'Even long bookings need suitable access and realistic CAT 226B work scope.' ) ), 'ar_title' => 'تأجير بوبكات شهري في الإمارات: متى يكون مناسبا؟', 'ar_intro' => 'قد يناسب التأجير الشهري المقاولين الذين لديهم أعمال متكررة في التحميل والتنظيف ونقل المواد.' ),
	array( 'cat' => 'location', 'slug' => 'equipment-rental-company-uae-checklist', 'title' => 'Choosing an Equipment Rental Company in UAE: Checklist', 'focus' => 'equipment rental company UAE', 'excerpt' => 'Checklist for choosing an equipment rental company in the UAE.', 'intro' => 'Choosing an equipment rental company in the UAE should be based on machine clarity, contact speed, operator availability, service area and honest delivery discussion.', 'sections' => array( array( 'Check the exact machine', 'Know whether you are getting a CAT 226B, another skid steer or a different loader type.' ), array( 'Check operator support', 'Operator-led rental can reduce mistakes on compact jobsites.' ), array( 'Check service areas', 'A clear company will explain where delivery is free, possible or quoted separately.' ) ), 'ar_title' => 'اختيار شركة تأجير معدات في الإمارات: قائمة فحص', 'ar_intro' => 'اختيار شركة تأجير معدات في الإمارات يجب أن يعتمد على وضوح الآلة وسرعة التواصل وتوفر المشغل ومناطق الخدمة.' ),
);

// Add a few more long-tail support articles to complete a 30-post cluster.
$articles = array_merge(
	$articles,
	array(
		array( 'cat' => 'use_case', 'slug' => 'bobcat-loading-loose-material-dubai', 'title' => 'Bobcat for Loading Loose Material in Dubai', 'focus' => 'bobcat loading Dubai', 'excerpt' => 'Use bobcat rental for loading loose sand, soil and site material in Dubai.', 'intro' => 'Loose material loading is one of the simplest reasons to rent a bobcat in Dubai. A CAT 226B with bucket can reduce manual handling time on compact sites.', 'sections' => array( array( 'Good material types', 'Loose sand, soil, gravel and light site material are common loading requests.' ), array( 'Stockpile location matters', 'The distance between stockpile and loading point affects time.' ), array( 'Access and height', 'Tell the team what the material is being loaded into.' ) ), 'ar_title' => 'بوبكات لتحميل المواد السائبة في دبي', 'ar_intro' => 'تحميل المواد السائبة من أبسط أسباب تأجير البوبكات في دبي، خصوصا الرمل والتربة والحصى.' ),
		array( 'cat' => 'location', 'slug' => 'bobcat-rental-ajman-guide', 'title' => 'Bobcat Rental Ajman: Compact Site Support Guide', 'focus' => 'bobcat rental Ajman', 'excerpt' => 'Guide to bobcat rental in Ajman for compact construction and site cleaning.', 'intro' => 'Ajman bobcat rental can support compact construction, villa, yard and cleanup jobs when access and timing are practical.', 'sections' => array( array( 'Common Ajman requests', 'Site cleaning, loading, sand shifting and small levelling jobs are common.' ), array( 'Plan delivery clearly', 'A precise map pin helps quote Ajman delivery.' ), array( 'Operator included', 'The CAT 226B is supplied with operator for practical work.' ) ), 'ar_title' => 'تأجير بوبكات عجمان: دليل دعم المواقع الصغيرة', 'ar_intro' => 'يمكن لتأجير البوبكات في عجمان دعم أعمال البناء والتنظيف والتحميل عندما يكون الدخول والتوقيت مناسبين.' ),
		array( 'cat' => 'location', 'slug' => 'bobcat-rental-rak-guide', 'title' => 'Bobcat Rental RAK for Construction and Yard Work', 'focus' => 'bobcat rental RAK', 'excerpt' => 'Bobcat rental RAK guide for construction, yards and compact site work.', 'intro' => 'Ras Al Khaimah bobcat rental is useful for contractor yards, villa sites and compact construction work where a skid steer loader can enter safely.', 'sections' => array( array( 'Map pin required', 'RAK delivery needs a clear location and expected duration.' ), array( 'Good use cases', 'Loading, cleaning, levelling and backfilling are practical work types.' ), array( 'Longer bookings help', 'For longer-distance work, full-day or multi-day jobs may be easier to schedule.' ) ), 'ar_title' => 'تأجير بوبكات رأس الخيمة لأعمال البناء والساحات', 'ar_intro' => 'تأجير البوبكات في رأس الخيمة مناسب للساحات ومواقع الفلل والأعمال الصغيرة عند توفر دخول مناسب.' ),
		array( 'cat' => 'machine', 'slug' => 'cat-226b-auxiliary-hydraulics-attachments', 'title' => 'CAT 226B Auxiliary Hydraulics and Attachment Planning', 'focus' => 'CAT 226B auxiliary hydraulics', 'excerpt' => 'How auxiliary hydraulics matter when planning CAT 226B attachment jobs.', 'intro' => 'Auxiliary hydraulics are important when customers ask for tools beyond a standard bucket. Availability and compatibility should be confirmed before booking.', 'sections' => array( array( 'Bucket work is simplest', 'Most loading and cleaning jobs use the bucket.' ), array( 'Hydraulic tools need checking', 'Augers, trenchers, brooms and hammers require compatibility and availability checks.' ), array( 'Mention the tool early', 'Do not wait until dispatch to mention attachment requirements.' ) ), 'ar_title' => 'هيدروليك CAT 226B الإضافي وتخطيط الملحقات', 'ar_intro' => 'الهيدروليك الإضافي مهم عند طلب أدوات غير البكت، ويجب تأكيد التوافق والتوفر قبل الحجز.' ),
		array( 'cat' => 'use_case', 'slug' => 'bobcat-for-debris-removal-uae', 'title' => 'Bobcat for Debris Removal in UAE Jobsites', 'focus' => 'bobcat debris removal UAE', 'excerpt' => 'When a bobcat can help move debris and loose construction waste.', 'intro' => 'Bobcat rental can help with debris removal when material is loose, accessible and suitable for bucket work.', 'sections' => array( array( 'Loose debris works best', 'Mixed heavy demolition waste may need different equipment.' ), array( 'Loading point matters', 'Tell the team where debris will be moved or loaded.' ), array( 'Photos are useful', 'Photos help estimate volume and machine fit.' ) ), 'ar_title' => 'بوبكات لإزالة المخلفات في مواقع الإمارات', 'ar_intro' => 'يساعد البوبكات في إزالة المخلفات عندما تكون المواد سائبة ومناسبة للعمل بالبكت.' ),
		array( 'cat' => 'pricing', 'slug' => 'half-day-vs-full-day-bobcat-rental', 'title' => 'Half-Day vs Full-Day Bobcat Rental: Which Is Better?', 'focus' => 'half day bobcat rental UAE', 'excerpt' => 'Compare half-day and full-day bobcat rental for UAE jobsites.', 'intro' => 'Choosing half-day or full-day bobcat rental depends on travel time, work volume and how certain the site access is.', 'sections' => array( array( 'Half-day is for tight scopes', 'It can work for small local jobs with clear access.' ), array( 'Full-day reduces pressure', 'It gives more room for loading, cleanup and unexpected site delays.' ), array( 'Distance changes the answer', 'Long-distance delivery may make full-day booking more practical.' ) ), 'ar_title' => 'تأجير بوبكات نصف يوم أم يوم كامل: أيهما أفضل؟', 'ar_intro' => 'اختيار نصف يوم أو يوم كامل يعتمد على وقت التوصيل وحجم العمل ووضوح دخول الموقع.' ),
		array( 'cat' => 'machine', 'slug' => 'cat-226b-villa-site-access', 'title' => 'Can a CAT 226B Enter a Villa Site?', 'focus' => 'CAT 226B villa site access', 'excerpt' => 'Villa site access notes for CAT 226B bobcat rental.', 'intro' => 'A CAT 226B may fit many villa sites, but the gate width, turning area, surface condition and nearby finished work must be checked.', 'sections' => array( array( 'Measure access', 'Gate and path width are important before dispatch.' ), array( 'Check turning space', 'The machine needs room to work, not just enter.' ), array( 'Protect finished surfaces', 'Mention tiles, interlock, garden edges or delicate areas.' ) ), 'ar_title' => 'هل يمكن لـ CAT 226B دخول موقع فيلا؟', 'ar_intro' => 'قد يناسب CAT 226B مواقع الفلل، لكن يجب التأكد من عرض الدخول ومساحة الدوران وحالة السطح.' ),
	)
);

$articles = array_slice( $articles, 0, 30 );

$post_count = 0;
foreach ( $articles as $article ) {
	$en_id = dbr_seo_upsert_post(
		$article['slug'],
		$article['title'],
		dbr_seo_article_content( $article ),
		$article['excerpt'],
		$categories[ $article['cat'] ]['en'],
		$article['title'] . ' | UAE Equipment Rental',
		$article['excerpt'],
		$article['focus'],
		'en'
	);
	$ar_id = dbr_seo_upsert_post(
		'ar-' . $article['slug'],
		$article['ar_title'],
		dbr_seo_ar_article_content( $article ),
		$article['ar_intro'],
		$categories[ $article['cat'] ]['ar'],
		$article['ar_title'] . ' | UAE Equipment Rental',
		$article['ar_intro'],
		$article['ar_title'],
		'ar'
	);
	dbr_seo_link_post_translations( $en_id, $ar_id );
	$post_count++;
}

flush_rewrite_rules();

dbr_seo_log( 'Created or updated ' . count( $landing_pages ) . ' location pages, ' . count( $service_pages ) . ' service pages and ' . $post_count . ' SEO articles in English and Arabic.' );
