<?php
/**
 * Populate the local WordPress install from the SRS with English and Arabic content.
 *
 * Run with:
 * php /opt/homebrew/bin/wp eval-file setup-wordpress-content.php --path=wordpress
 */

wp_delete_post( 1, true );
wp_delete_post( 2, true );

function dbr_polylang_enabled() {
	return function_exists( 'pll_set_post_language' ) && function_exists( 'pll_save_post_translations' );
}

function dbr_ensure_languages() {
	if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'PLL' ) ) {
		WP_CLI::warning( 'Polylang is not active. English and Arabic content will be created without language links.' );
		return;
	}

	$languages = pll_languages_list( array( 'fields' => 'slug' ) );

	if ( ! in_array( 'en', $languages, true ) ) {
		$result = PLL()->model->add_language(
			array(
				'name'       => 'English',
				'slug'       => 'en',
				'locale'     => 'en_US',
				'rtl'        => false,
				'flag'       => 'us',
				'term_group' => 0,
			)
		);
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( 'Could not add English language: ' . $result->get_error_message() );
		}
	}

	if ( ! in_array( 'ar', $languages, true ) ) {
		$result = PLL()->model->add_language(
			array(
				'name'       => 'العربية',
				'slug'       => 'ar',
				'locale'     => 'ar',
				'rtl'        => true,
				'flag'       => 'ae',
				'term_group' => 1,
			)
		);
		if ( is_wp_error( $result ) ) {
			WP_CLI::warning( 'Could not add Arabic language: ' . $result->get_error_message() );
		}
	}

	$options                  = get_option( 'polylang', array() );
	$options['force_lang']    = 1;
	$options['hide_default']  = true;
	$options['rewrite']       = true;
	$options['redirect_lang'] = false;
	$options['browser']       = false;
	$options['media_support'] = false;
	$options['default_lang']  = 'en';
	update_option( 'polylang', $options );
}

function dbr_delete_bad_flat_pages() {
	$bad_slugs = array(
		'services-bobcat-rental-dubai',
		'services-bobcat-rental-dubai-2',
		'services-skid-steer-loader-rental-dubai',
		'services-skid-steer-loader-rental-dubai-2',
		'services-bobcat-with-operator-dubai',
		'services-bobcat-with-operator-dubai-2',
		'machines-cat-226b-skid-steer-loader',
		'machines-cat-226b-skid-steer-loader-2',
		'service-areas-dubai',
		'service-areas-dubai-2',
	);

	foreach ( $bad_slugs as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page ) {
			wp_delete_post( $page->ID, true );
		}
	}
}

function dbr_post_path( $slug, $parent_id ) {
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

function dbr_upsert_page( $slug, $title, $content, $parent_id = 0, $excerpt = '', $lang = 'en' ) {
	$existing = get_page_by_path( dbr_post_path( $slug, $parent_id ), OBJECT, 'page' );
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

	if ( dbr_polylang_enabled() && $post_id && ! is_wp_error( $post_id ) ) {
		pll_set_post_language( $post_id, $lang );
	}

	return $post_id;
}

function dbr_upsert_category( $slug, $name, $lang = 'en' ) {
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

function dbr_upsert_post( $slug, $title, $content, $excerpt, $category_id, $lang = 'en' ) {
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

	if ( dbr_polylang_enabled() && $post_id && ! is_wp_error( $post_id ) ) {
		pll_set_post_language( $post_id, $lang );
	}

	return $post_id;
}

function dbr_link_post_translations( $english_id, $arabic_id ) {
	if ( dbr_polylang_enabled() && $english_id && $arabic_id ) {
		pll_save_post_translations(
			array(
				'en' => $english_id,
				'ar' => $arabic_id,
			)
		);
	}
}

function dbr_link_term_translations( $english_id, $arabic_id ) {
	if ( function_exists( 'pll_save_term_translations' ) && $english_id && $arabic_id ) {
		pll_save_term_translations(
			array(
				'en' => $english_id,
				'ar' => $arabic_id,
			)
		);
	}
}

function dbr_set_yoast_meta( $post_id, $title, $description ) {
	update_post_meta( $post_id, '_yoast_wpseo_title', $title );
	update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
}

function dbr_build_menu( $menu_name, $items, $lang ) {
	$menu    = wp_get_nav_menu_object( $menu_name );
	$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );

	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
		wp_delete_post( $item->ID, true );
	}

	foreach ( $items as $item ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $item['label'],
				'menu-item-object-id' => $item['id'],
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}

	if ( function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( $menu_id, $lang );
	}

	return $menu_id;
}

dbr_ensure_languages();
dbr_delete_bad_flat_pages();

$categories = array(
	'machine_specs' => array(
		'en' => dbr_upsert_category( 'machine-specs', 'Machine Specs', 'en' ),
		'ar' => dbr_upsert_category( 'ar-machine-specs', 'مواصفات المعدات', 'ar' ),
	),
	'rental_guides' => array(
		'en' => dbr_upsert_category( 'rental-guides', 'Rental Guides', 'en' ),
		'ar' => dbr_upsert_category( 'ar-rental-guides', 'أدلة التأجير', 'ar' ),
	),
	'pricing' => array(
		'en' => dbr_upsert_category( 'pricing', 'Pricing', 'en' ),
		'ar' => dbr_upsert_category( 'ar-pricing', 'الأسعار', 'ar' ),
	),
	'use_cases' => array(
		'en' => dbr_upsert_category( 'use-cases', 'Use Cases', 'en' ),
		'ar' => dbr_upsert_category( 'ar-use-cases', 'استخدامات العمل', 'ar' ),
	),
	'attachments' => array(
		'en' => dbr_upsert_category( 'attachments', 'Attachments', 'en' ),
		'ar' => dbr_upsert_category( 'ar-attachments', 'الملحقات', 'ar' ),
	),
);

foreach ( $categories as $category ) {
	dbr_link_term_translations( $category['en'], $category['ar'] );
}

$en = array();
$ar = array();

$en['home'] = dbr_upsert_page( 'home', 'Bobcat Rental UAE', '', 0, 'CAT 226B bobcat and skid steer loader rental with operator across the UAE.', 'en' );
$ar['home'] = dbr_upsert_page( 'الرئيسية', 'تأجير بوبكات في الإمارات', '', 0, 'تأجير بوبكات وسكيد ستير لودر CAT 226B مع مشغل داخل الإمارات.', 'ar' );

$en['services'] = dbr_upsert_page(
	'services',
	'Equipment Rental Services',
	'<p>UAE-focused compact equipment rental services for contractors, site supervisors, villa builders, landscaping teams and small project managers. The launch offer is deliberately narrow: one CAT 226B skid steer loader with operator, clear job-fit guidance, and fast quote paths through phone and WhatsApp.</p>',
	0,
	'Bobcat and skid steer loader rental service hub for UAE jobsites.',
	'en'
);
$ar['services'] = dbr_upsert_page(
	'خدمات-تأجير-المعدات',
	'خدمات تأجير المعدات',
	'<p>خدمات تأجير معدات مدمجة داخل الإمارات للمقاولين ومشرفي المواقع وأعمال الفلل والتنسيق والصيانة. التركيز الحالي واضح: سكيد ستير لودر CAT 226B واحد مع مشغل، إرشاد مناسب لطبيعة العمل، وطلب سعر سريع عبر الاتصال أو واتساب.</p>',
	0,
	'صفحة خدمات تأجير البوبكات والسكيد ستير داخل الإمارات.',
	'ar'
);

$en['bobcat'] = dbr_upsert_page(
	'bobcat-rental-dubai',
	'Bobcat Rental Dubai with Fast WhatsApp Quotes',
	'<p>If you are looking for bobcat rental in Dubai or anywhere in the UAE, we provide CAT 226B skid steer loader hire with operator for practical site work where access, manoeuvrability and fast loading matter. Customers often call this machine a bobcat, while the correct machine type is a skid steer loader.</p><p>Use this service for site cleaning, debris movement, compact loading, grading, levelling, trench backfilling and short-distance material handling.</p>',
	$en['services'],
	'CAT 226B bobcat rental with operator for site cleaning, loading, levelling and backfilling.',
	'en'
);
$ar['bobcat'] = dbr_upsert_page(
	'تأجير-بوبكات-دبي',
	'تأجير بوبكات دبي مع عرض سريع عبر واتساب',
	'<p>إذا كنت تبحث عن تأجير بوبكات في دبي أو أي مكان داخل الإمارات، نوفر سكيد ستير لودر CAT 226B مع مشغل لأعمال المواقع التي تحتاج دخول سهل، مناورة جيدة وتحميل سريع. كثير من العملاء يسمون هذه الآلة بوبكات، والاسم الفني هو سكيد ستير لودر.</p><p>تستخدم هذه الخدمة لتنظيف المواقع، نقل المخلفات، التحميل، التسوية، الردم، ومناولة المواد لمسافات قصيرة.</p>',
	$ar['services'],
	'تأجير بوبكات CAT 226B مع مشغل لتنظيف المواقع والتحميل والتسوية والردم.',
	'ar'
);

$en['skid'] = dbr_upsert_page(
	'skid-steer-loader-rental-dubai',
	'Skid Steer Loader Rental Dubai',
	'<p>Rent a compact skid steer loader with operator in the UAE for loading, site cleaning, grading, levelling, backfilling and material movement. This page supports buyers who use the technical machine term rather than the market term bobcat.</p>',
	$en['services'],
	'Technical skid steer loader rental page for UAE commercial search intent.',
	'en'
);
$ar['skid'] = dbr_upsert_page(
	'تأجير-سكيد-ستير-لودر-دبي',
	'تأجير سكيد ستير لودر دبي',
	'<p>استأجر سكيد ستير لودر مدمج مع مشغل داخل الإمارات لأعمال التحميل، تنظيف المواقع، التمهيد، التسوية، الردم ونقل المواد. هذه الصفحة تخدم العملاء الذين يبحثون بالاسم الفني للآلة وليس فقط بكلمة بوبكات.</p>',
	$ar['services'],
	'صفحة تأجير سكيد ستير لودر للعملاء الباحثين عن الاسم الفني للآلة.',
	'ar'
);

$en['operator'] = dbr_upsert_page(
	'bobcat-with-operator-dubai',
	'Bobcat With Operator Dubai',
	'<p>Request a CAT 226B skid steer loader with operator for UAE site work. Operator is provided with the bobcat, while dispatch and delivery cost depend on the job date, location, attachment requirement, site access and work type.</p>',
	$en['services'],
	'Bobcat rental with operator for UAE compact jobsites.',
	'en'
);
$ar['operator'] = dbr_upsert_page(
	'بوبكات-مع-مشغل',
	'بوبكات مع مشغل في الإمارات',
	'<p>اطلب سكيد ستير لودر CAT 226B مع مشغل لأعمال المواقع داخل الإمارات. يتم توفير المشغل مع البوبكات، بينما تعتمد تكلفة الإرسال والتوصيل على التاريخ والموقع والملحق المطلوب ودخول الموقع ونوع العمل.</p>',
	$ar['services'],
	'تأجير بوبكات مع مشغل لأعمال المواقع داخل الإمارات.',
	'ar'
);

$en['machines'] = dbr_upsert_page( 'machines', 'Machines', '<p>Machine pages provide specifications, job-fit guidance, attachment notes and quote paths. The initial fleet page is the CAT 226B skid steer loader.</p>', 0, 'Machine hub for rental equipment specifications and availability.', 'en' );
$ar['machines'] = dbr_upsert_page( 'المعدات', 'المعدات', '<p>توفر صفحات المعدات المواصفات وإرشادات مناسبة العمل وملاحظات الملحقات وطرق طلب السعر. الصفحة الأولى للأسطول هي سكيد ستير لودر CAT 226B.</p>', 0, 'صفحة مواصفات وتوفر المعدات للتأجير.', 'ar' );

$en['machine'] = dbr_upsert_page(
	'cat-226b-skid-steer-loader',
	'CAT 226B Skid Steer Loader',
	'<p>The rental machine is a CAT 226B skid steer loader, commonly searched as a bobcat. Public machine details confirm Caterpillar 226B, model year 2015, yellow colour and American origin.</p><p>Key anchors: rated operating capacity 680 kg, tipping load 1360 kg, operating weight 2641 kg, with auxiliary hydraulics for compatible work tools.</p>',
	$en['machines'],
	'CAT 226B skid steer loader specs, uses and rental availability in the UAE.',
	'en'
);
$ar['machine'] = dbr_upsert_page(
	'cat-226b-سكيد-ستير-لودر',
	'سكيد ستير لودر CAT 226B',
	'<p>الآلة المتوفرة للتأجير هي سكيد ستير لودر CAT 226B، وغالبا يبحث عنها العملاء باسم بوبكات. التفاصيل العامة تؤكد أنها Caterpillar 226B موديل 2015 باللون الأصفر ومنشأ أمريكي.</p><p>أهم المواصفات: سعة تشغيل مقدرة 680 كجم، حمل انقلاب 1360 كجم، وزن تشغيل 2641 كجم، مع هيدروليك إضافي للملحقات المتوافقة.</p>',
	$ar['machines'],
	'مواصفات واستخدامات وتوفر سكيد ستير لودر CAT 226B داخل الإمارات.',
	'ar'
);

$en['areas'] = dbr_upsert_page( 'service-areas', 'Service Areas', '<p>The business is headquartered in Dibba, Fujairah and can discuss bobcat delivery across the UAE. Delivery can be free near Fujairah, while other locations are quoted by distance, timing and job duration.</p>', 0, 'UAE service area hub for bobcat and skid steer rental.', 'en' );
$ar['areas'] = dbr_upsert_page( 'مناطق-الخدمة', 'مناطق الخدمة', '<p>يقع مقر العمل في دبا، الفجيرة ويمكن مناقشة توصيل البوبكات إلى مختلف مناطق الإمارات. التوصيل قد يكون مجانيا قرب الفجيرة، أما المواقع الأخرى فيتم تسعيرها حسب المسافة والتوقيت ومدة العمل.</p>', 0, 'صفحة مناطق خدمة تأجير البوبكات والسكيد ستير داخل الإمارات.', 'ar' );

$en['dubai'] = dbr_upsert_page( 'dubai', 'Bobcat Rental in Dubai', '<p>Dubai service is available for bobcat and CAT 226B skid steer loader rental with operator. Share your job location, work type, date, duration and attachment requirement by WhatsApp for availability and delivery discussion.</p>', $en['areas'], 'Bobcat rental in Dubai with CAT 226B skid steer and operator availability.', 'en' );
$ar['dubai'] = dbr_upsert_page( 'دبي', 'تأجير بوبكات في دبي', '<p>خدمة دبي متاحة لتأجير بوبكات وسكيد ستير لودر CAT 226B مع مشغل. أرسل موقع العمل ونوعه والتاريخ والمدة والملحق المطلوب عبر واتساب لمناقشة التوفر والتوصيل.</p>', $ar['areas'], 'تأجير بوبكات في دبي مع توفر CAT 226B ومشغل.', 'ar' );

$en['about'] = dbr_upsert_page( 'about', 'About UAE Equipment Rental', '<p>UAE Equipment Rental is headquartered in Dibba, Fujairah and currently focuses this site on one real machine: a CAT 226B skid steer loader supplied with operator. The business can expand into a broader heavy equipment rental taxonomy later without changing the site structure.</p>', 0, 'About the UAE bobcat and skid steer rental business.', 'en' );
$ar['about'] = dbr_upsert_page( 'من-نحن', 'عن UAE Equipment Rental', '<p>يقع مقر UAE Equipment Rental في دبا، الفجيرة ويركز هذا الموقع حاليا على آلة حقيقية واحدة: سكيد ستير لودر CAT 226B يتم توفيرها مع مشغل. يمكن توسيع الموقع لاحقا لفئات تأجير معدات أوسع دون تغيير هيكله الأساسي.</p>', 0, 'نبذة عن خدمة تأجير البوبكات والسكيد ستير داخل الإمارات.', 'ar' );

$en['contact'] = dbr_upsert_page(
	'contact',
	'Contact and Quote Request',
	'<p>Call or WhatsApp 24/7 for current machine availability, operator-led bobcat rental and dispatch timing.</p><ul><li>Phone: +971 54 738 8695</li><li>WhatsApp: +971 54 738 8695</li><li>Headquarters: Dibba, Fujairah, United Arab Emirates</li><li>Service areas: all UAE emirates by delivery discussion</li></ul><p>Delivery can be free near Fujairah. Other UAE locations are quoted by distance, date, timing and booking duration.</p><h2>Quote request form</h2>[fluentform id="3"]',
	0,
	'Contact and quote request page for bobcat rental in the UAE.',
	'en'
);
$ar['contact'] = dbr_upsert_page(
	'اتصل-بنا',
	'اتصل بنا واطلب السعر',
	'<p>اتصل أو راسلنا واتساب 24/7 لمعرفة توفر الآلة، تأجير البوبكات مع مشغل، ووقت الإرسال.</p><ul><li>الهاتف: +971 54 738 8695</li><li>واتساب: +971 54 738 8695</li><li>المقر: دبا، الفجيرة، الإمارات العربية المتحدة</li><li>مناطق الخدمة: جميع إمارات الدولة حسب مناقشة التوصيل</li></ul><p>يمكن أن يكون التوصيل مجانيا قرب الفجيرة. يتم تسعير باقي مناطق الإمارات حسب المسافة والتاريخ والتوقيت ومدة الحجز.</p><h2>نموذج طلب السعر</h2>[fluentform id="4"]',
	0,
	'صفحة تواصل وطلب سعر لتأجير البوبكات داخل الإمارات.',
	'ar'
);

$en['blog'] = dbr_upsert_page( 'blog', 'Bobcat Rental Guides', '<p>Guides for skid steer loader rental, CAT 226B use cases, pricing questions and UAE jobsite planning.</p>', 0, 'Blog and buying guides for UAE bobcat and skid steer rental searchers.', 'en' );
$ar['blog'] = dbr_upsert_page( 'دليل-تأجير-البوبكات', 'أدلة تأجير البوبكات', '<p>أدلة عملية حول تأجير السكيد ستير، استخدامات CAT 226B، أسئلة الأسعار، وتجهيز مواقع العمل داخل الإمارات.</p>', 0, 'مدونة وأدلة للعملاء الباحثين عن تأجير البوبكات والسكيد ستير.', 'ar' );

$en['privacy'] = dbr_upsert_page( 'privacy-policy', 'Privacy Policy', '<p>UAE Equipment Rental respects customer privacy. This website collects the details you choose to send through quote forms, phone calls or WhatsApp messages, such as name, mobile number, job location, service requirement, date, duration and attachment needs.</p><h2>How we use information</h2><p>We use submitted information to respond to bobcat rental enquiries, confirm machine availability, discuss delivery, prepare quotes and improve customer service.</p><h2>Contact and WhatsApp</h2><p>When you contact us by phone or WhatsApp, your message is handled through those communication providers and may be subject to their own privacy terms.</p><h2>Website data</h2><p>The site may use basic cookies and security tools to keep the website working, remember consent choices and understand general site performance. Analytics or advertising tools should only be enabled with the correct consent settings.</p><h2>Contact</h2><p>For privacy questions, contact UAE Equipment Rental at +971 54 738 8695.</p>', 0, '', 'en' );
$ar['privacy'] = dbr_upsert_page( 'سياسة-الخصوصية', 'سياسة الخصوصية', '<p>تحترم UAE Equipment Rental خصوصية العملاء. يجمع هذا الموقع التفاصيل التي تختار إرسالها عبر نموذج طلب السعر أو الاتصال أو واتساب، مثل الاسم ورقم الهاتف وموقع العمل ونوع الخدمة والتاريخ والمدة والملحقات المطلوبة.</p><h2>كيف نستخدم المعلومات</h2><p>نستخدم المعلومات للرد على استفسارات تأجير البوبكات، تأكيد توفر الآلة، مناقشة التوصيل، تجهيز الأسعار وتحسين خدمة العملاء.</p><h2>الاتصال وواتساب</h2><p>عند التواصل معنا عبر الهاتف أو واتساب، قد تخضع الرسائل لشروط الخصوصية الخاصة بتلك الخدمات.</p><h2>بيانات الموقع</h2><p>قد يستخدم الموقع ملفات كوكيز أساسية وأدوات أمان للحفاظ على عمل الموقع وتذكر اختيارات الموافقة وفهم الأداء العام. يجب تفعيل التحليلات أو الإعلانات فقط مع إعدادات الموافقة المناسبة.</p><h2>التواصل</h2><p>لأسئلة الخصوصية، تواصل مع UAE Equipment Rental على +971 54 738 8695.</p>', 0, '', 'ar' );

$en['cookie'] = dbr_upsert_page( 'cookie-policy', 'Cookie Policy', '<p>This website uses cookies to keep the site working, remember cookie choices and support basic security and performance. Some cookies are necessary for the website to function correctly.</p><h2>Optional cookies</h2><p>If analytics, advertising or third-party tracking tools are added, they should be managed through the cookie consent banner before those cookies are stored.</p><h2>Managing cookies</h2><p>You can accept, reject or customise cookie choices through the consent banner where available. You can also clear cookies in your browser settings.</p><h2>Contact</h2><p>For cookie questions, contact UAE Equipment Rental at +971 54 738 8695.</p>', 0, '', 'en' );
$ar['cookie'] = dbr_upsert_page( 'سياسة-الكوكيز', 'سياسة الكوكيز', '<p>يستخدم هذا الموقع ملفات كوكيز للحفاظ على عمل الموقع، تذكر اختيارات الكوكيز، ودعم الأمان والأداء الأساسي. بعض ملفات الكوكيز ضرورية لعمل الموقع بشكل صحيح.</p><h2>كوكيز اختيارية</h2><p>إذا تمت إضافة أدوات تحليلات أو إعلانات أو تتبع طرف ثالث، يجب إدارتها من خلال شريط موافقة الكوكيز قبل تخزينها.</p><h2>إدارة الكوكيز</h2><p>يمكنك قبول أو رفض أو تخصيص اختيارات الكوكيز من خلال شريط الموافقة عند توفره. يمكنك أيضا حذف الكوكيز من إعدادات المتصفح.</p><h2>التواصل</h2><p>لأسئلة الكوكيز، تواصل مع UAE Equipment Rental على +971 54 738 8695.</p>', 0, '', 'ar' );

$page_pairs = array( 'home', 'services', 'bobcat', 'skid', 'operator', 'machines', 'machine', 'areas', 'dubai', 'about', 'contact', 'blog', 'privacy', 'cookie' );
foreach ( $page_pairs as $key ) {
	dbr_link_post_translations( $en[ $key ], $ar[ $key ] );
}

$posts = array(
	array(
		'key' => 'post_one',
		'cat' => 'machine_specs',
		'en' => array(
			'cat-226b-specifications-and-uses',
			'CAT 226B Specifications and Uses for Dubai Jobsites',
			'<p>The CAT 226B skid steer loader is a compact machine suited to site cleaning, loading, grading, backfilling and landscaping support where access is tight.</p><h2>Specification anchors</h2><ul><li>Machine type: Caterpillar 226B</li><li>Model year: 2015</li><li>Rated operating capacity: 680 kg</li><li>Tipping load: 1360 kg</li><li>Operating weight: 2641 kg</li><li>Auxiliary hydraulics for compatible work tools</li></ul><h2>Best-fit jobs</h2><p>The machine is most useful where a full-size loader is excessive but manual labour is too slow: villa plots, compact construction sites, interlock preparation, backfilling, loading loose material and clearing debris.</p>',
			'CAT 226B model year, rated capacity, tipping load, operating weight and practical UAE jobsite uses.',
		),
		'ar' => array(
			'مواصفات-cat-226b-واستخداماته',
			'مواصفات CAT 226B واستخداماته في مواقع العمل',
			'<p>سكيد ستير لودر CAT 226B آلة مدمجة مناسبة لتنظيف المواقع والتحميل والتمهيد والردم ودعم أعمال التنسيق في الأماكن ذات الدخول المحدود.</p><h2>المواصفات الأساسية</h2><ul><li>نوع الآلة: Caterpillar 226B</li><li>سنة الموديل: 2015</li><li>سعة التشغيل المقدرة: 680 كجم</li><li>حمل الانقلاب: 1360 كجم</li><li>وزن التشغيل: 2641 كجم</li><li>هيدروليك إضافي للملحقات المتوافقة</li></ul><h2>أفضل الأعمال المناسبة</h2><p>الآلة مفيدة عندما تكون اللودر الكبير أكثر من اللازم والعمل اليدوي بطيئا، مثل مواقع الفلل والأعمال الصغيرة وتجهيز الإنترلوك والردم وتحميل المواد السائبة وإزالة المخلفات.</p>',
			'مواصفات CAT 226B وسعة التشغيل والوزن واستخداماته العملية داخل الإمارات.',
		),
	),
	array(
		'key' => 'post_two',
		'cat' => 'rental_guides',
		'en' => array(
			'what-is-a-skid-steer-loader',
			'What Is a Skid Steer Loader?',
			'<p>A skid steer loader is a compact wheeled loader used for jobs such as loading, site cleaning, grading and material movement. In the UAE market, many customers search for this machine as a bobcat.</p><h2>Why contractors choose it</h2><p>The machine turns within a tight footprint, uses a front loader arm, and can work with different tools depending on availability. That makes it practical for compact sites where manoeuvrability matters.</p>',
			'A plain-English guide to skid steer loaders and why UAE customers often call them bobcats.',
		),
		'ar' => array(
			'ما-هو-سكيد-ستير-لودر',
			'ما هو سكيد ستير لودر؟',
			'<p>السكيد ستير لودر هو لودر صغير بعجلات يستخدم في أعمال التحميل وتنظيف المواقع والتمهيد ونقل المواد. في سوق الإمارات، يبحث كثير من العملاء عنه باسم بوبكات.</p><h2>لماذا يختاره المقاولون</h2><p>يدور داخل مساحة صغيرة، ويستخدم ذراع تحميل أمامية، ويمكن تشغيله مع أدوات مختلفة حسب التوفر. لذلك هو عملي للمواقع الصغيرة التي تحتاج مناورة عالية.</p>',
			'شرح مبسط لما هو السكيد ستير ولماذا يسميه العملاء في الإمارات بوبكات.',
		),
	),
	array(
		'key' => 'post_three',
		'cat' => 'pricing',
		'en' => array(
			'bobcat-rental-price-dubai-guide',
			'Bobcat Rental Price Dubai Guide',
			'<p>Bobcat rental price in the UAE depends on machine availability, booking duration, location, attachment requirement and delivery timing. The bobcat is provided with an operator. The fastest way to quote is to share job details on WhatsApp.</p><h2>What affects price</h2><ul><li>Half-day, full-day, weekly or monthly duration</li><li>Bucket, forks, auger or other attachment needs</li><li>Delivery distance and site access</li><li>Urgency and working hours</li><li>Whether the job is near Fujairah or requires long-distance delivery</li></ul>',
			'Factors that affect bobcat rental price in the UAE and what to send for an accurate quote.',
		),
		'ar' => array(
			'دليل-أسعار-تأجير-البوبكات',
			'دليل أسعار تأجير البوبكات في الإمارات',
			'<p>يعتمد سعر تأجير البوبكات في الإمارات على توفر الآلة ومدة الحجز والموقع والملحق المطلوب ووقت التوصيل. يتم توفير البوبكات مع مشغل. أسرع طريقة للحصول على سعر هي إرسال تفاصيل العمل عبر واتساب.</p><h2>ما الذي يؤثر على السعر؟</h2><ul><li>نصف يوم أو يوم كامل أو أسبوعي أو شهري</li><li>الحاجة إلى بكت أو فورك أو أوجر أو ملحق آخر</li><li>مسافة التوصيل ودخول الموقع</li><li>الاستعجال وساعات العمل</li><li>قرب العمل من الفجيرة أو حاجته لتوصيل بعيد</li></ul>',
			'العوامل التي تؤثر على سعر تأجير البوبكات في الإمارات وما يجب إرساله لسعر دقيق.',
		),
	),
	array(
		'key' => 'post_four',
		'cat' => 'use_cases',
		'en' => array(
			'bobcat-for-site-cleaning-dubai',
			'Bobcat for Site Cleaning in Dubai',
			'<p>A CAT 226B skid steer loader can speed up compact site cleaning where loose debris, sand, soil or construction waste needs to be gathered and moved. It is especially useful on villa, landscaping and small construction jobsites.</p><h2>When it fits</h2><p>It fits best when access is tight, material volumes are moderate, and a compact machine can save labour time without bringing in oversized equipment.</p>',
			'How bobcat rental helps with construction site cleaning and debris movement in the UAE.',
		),
		'ar' => array(
			'بوبكات-لتنظيف-المواقع-في-دبي',
			'بوبكات لتنظيف مواقع العمل في دبي',
			'<p>يمكن لسكيد ستير لودر CAT 226B تسريع تنظيف المواقع الصغيرة عندما تكون هناك مخلفات أو رمل أو تربة أو نفايات بناء تحتاج إلى جمع ونقل. وهو مفيد خصوصا في مواقع الفلل والتنسيق وأعمال البناء الصغيرة.</p><h2>متى يكون مناسبا؟</h2><p>يناسب العمل عندما يكون الدخول محدودا، حجم المواد متوسطا، والآلة المدمجة توفر وقت العمال دون الحاجة إلى معدات كبيرة.</p>',
			'كيف يساعد تأجير البوبكات في تنظيف مواقع البناء ونقل المخلفات داخل الإمارات.',
		),
	),
	array(
		'key' => 'post_five',
		'cat' => 'use_cases',
		'en' => array(
			'skid-steer-backfilling-grading-dubai',
			'Skid Steer for Backfilling and Grading in Dubai',
			'<p>Skid steer loaders are practical for backfilling trenches, spreading loose material and preparing compact surfaces. They are not a replacement for every earthmoving machine, but they are efficient for smaller controlled jobs.</p><h2>Useful project types</h2><ul><li>Trench backfilling</li><li>Interlock base preparation</li><li>Landscape grading</li><li>Small plot levelling</li></ul>',
			'Use a skid steer loader for backfilling, grading and compact surface preparation in the UAE.',
		),
		'ar' => array(
			'سكيد-ستير-للردم-والتسوية',
			'سكيد ستير للردم والتسوية في دبي',
			'<p>السكيد ستير عملي لردم الخنادق ونشر المواد السائبة وتجهيز الأسطح الصغيرة. هو ليس بديلا لكل معدات الحفر، لكنه فعال للأعمال الصغيرة والمحددة.</p><h2>أنواع المشاريع المناسبة</h2><ul><li>ردم الخنادق</li><li>تجهيز قاعدة الإنترلوك</li><li>تسوية أعمال التنسيق</li><li>تسوية المساحات الصغيرة</li></ul>',
			'استخدام السكيد ستير للردم والتسوية وتجهيز الأسطح الصغيرة داخل الإمارات.',
		),
	),
	array(
		'key' => 'post_six',
		'cat' => 'rental_guides',
		'en' => array(
			'bobcat-with-operator-what-to-prepare',
			'Bobcat With Operator: What to Prepare Before Booking',
			'<p>Before booking a bobcat with operator, prepare the job location, access notes, expected work type, date, duration, photos if available, and attachment requirement. Clear details help confirm whether the CAT 226B is suitable.</p><h2>Send these details</h2><ul><li>Google Maps location or pin</li><li>Work description</li><li>Site access width and surface</li><li>Preferred date and working hours</li><li>Bucket, fork, auger or other tool needs</li></ul>',
			'Checklist for booking bobcat rental with operator support in the UAE.',
		),
		'ar' => array(
			'بوبكات-مع-مشغل-قبل-الحجز',
			'بوبكات مع مشغل: ماذا تجهز قبل الحجز',
			'<p>قبل حجز بوبكات مع مشغل، جهز موقع العمل، ملاحظات الدخول، نوع العمل المتوقع، التاريخ، المدة، الصور إن وجدت، والملحق المطلوب. التفاصيل الواضحة تساعد على تأكيد مناسبة CAT 226B للعمل.</p><h2>أرسل هذه التفاصيل</h2><ul><li>موقع Google Maps أو pin</li><li>وصف العمل</li><li>عرض دخول الموقع وطبيعة السطح</li><li>التاريخ وساعات العمل المفضلة</li><li>الحاجة إلى بكت أو فورك أو أوجر أو أداة أخرى</li></ul>',
			'قائمة تجهيزات لحجز بوبكات مع مشغل داخل الإمارات.',
		),
	),
	array(
		'key' => 'post_seven',
		'cat' => 'rental_guides',
		'en' => array(
			'bobcat-vs-skid-steer-loader',
			'Bobcat vs Skid Steer Loader: What Is the Difference?',
			'<p>Bobcat is often used as a generic market term, but skid steer loader is the machine category. The available rental machine on this site is a CAT 226B skid steer loader, so the website uses both the search term and the correct technical term.</p>',
			'Explain the difference between the common search term bobcat and the correct machine type skid steer loader.',
		),
		'ar' => array(
			'بوبكات-او-سكيد-ستير-الفرق',
			'بوبكات أم سكيد ستير لودر: ما الفرق؟',
			'<p>تستخدم كلمة بوبكات غالبا كاسم شائع في السوق، بينما سكيد ستير لودر هو تصنيف الآلة. الآلة المتوفرة للتأجير في هذا الموقع هي CAT 226B skid steer loader، لذلك يستخدم الموقع كلمة البحث الشائعة والاسم الفني الصحيح.</p>',
			'شرح الفرق بين كلمة بوبكات الشائعة والاسم الفني الصحيح سكيد ستير لودر.',
		),
	),
	array(
		'key' => 'post_eight',
		'cat' => 'attachments',
		'en' => array(
			'skid-steer-attachments-guide-dubai',
			'Skid Steer Attachments Guide for Dubai Jobs',
			'<p>Skid steer loaders can support different work tools depending on machine compatibility and actual availability. Common attachment categories include buckets, forks, augers, brooms, trenchers and hammers.</p><p>Do not assume every attachment is available on every date. Mention the required tool when requesting a quote.</p>',
			'Common skid steer attachment types and when to request them for Dubai jobs.',
		),
		'ar' => array(
			'دليل-ملحقات-السكيد-ستير',
			'دليل ملحقات السكيد ستير لأعمال دبي',
			'<p>يمكن للسكيد ستير تشغيل أدوات مختلفة حسب توافق الآلة والتوفر الفعلي. تشمل الملحقات الشائعة البكت والفورك والأوجر والمكنسة وحفار الخنادق والهامر.</p><p>لا تفترض أن كل ملحق متوفر في كل تاريخ. اذكر الأداة المطلوبة عند طلب السعر.</p>',
			'أنواع ملحقات السكيد ستير الشائعة ومتى تطلبها لأعمال دبي.',
		),
	),
);

$post_ids = array();
foreach ( $posts as $post ) {
	$post_ids[ $post['key'] ] = array(
		'en' => dbr_upsert_post( $post['en'][0], $post['en'][1], $post['en'][2], $post['en'][3], $categories[ $post['cat'] ]['en'], 'en' ),
		'ar' => dbr_upsert_post( $post['ar'][0], $post['ar'][1], $post['ar'][2], $post['ar'][3], $categories[ $post['cat'] ]['ar'], 'ar' ),
	);
	dbr_link_post_translations( $post_ids[ $post['key'] ]['en'], $post_ids[ $post['key'] ]['ar'] );
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $en['home'] );
update_option( 'page_for_posts', $en['blog'] );
update_option( 'blogname', 'UAE Equipment Rental' );
update_option( 'blogdescription', 'CAT 226B bobcat rental with operator across the UAE' );
update_option( 'permalink_structure', '/%postname%/' );

$en_menu_id = dbr_build_menu(
	'Primary Navigation EN',
	array(
		array( 'id' => $en['home'], 'label' => 'Home' ),
		array( 'id' => $en['bobcat'], 'label' => 'Bobcat Rental UAE' ),
		array( 'id' => $en['machine'], 'label' => 'CAT 226B' ),
		array( 'id' => $en['services'], 'label' => 'Services' ),
		array( 'id' => $en['areas'], 'label' => 'Service Areas' ),
		array( 'id' => $en['blog'], 'label' => 'Blog' ),
		array( 'id' => $en['contact'], 'label' => 'Contact' ),
	),
	'en'
);

$ar_menu_id = dbr_build_menu(
	'Primary Navigation AR',
	array(
		array( 'id' => $ar['home'], 'label' => 'الرئيسية' ),
		array( 'id' => $ar['bobcat'], 'label' => 'تأجير بوبكات' ),
		array( 'id' => $ar['machine'], 'label' => 'CAT 226B' ),
		array( 'id' => $ar['services'], 'label' => 'الخدمات' ),
		array( 'id' => $ar['areas'], 'label' => 'مناطق الخدمة' ),
		array( 'id' => $ar['blog'], 'label' => 'المدونة' ),
		array( 'id' => $ar['contact'], 'label' => 'اتصل بنا' ),
	),
	'ar'
);

dbr_link_term_translations( $en_menu_id, $ar_menu_id );

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $en_menu_id;
$locations['footer']  = $en_menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

$polylang_options = get_option( 'polylang', array() );
$theme            = get_stylesheet();
$polylang_options['nav_menus'][ $theme ] = array(
	'primary' => array(
		'en' => $en_menu_id,
		'ar' => $ar_menu_id,
	),
	'footer' => array(
		'en' => $en_menu_id,
		'ar' => $ar_menu_id,
	),
);
update_option( 'polylang', $polylang_options );

set_theme_mod( 'dbr_business_name', 'UAE Equipment Rental' );
set_theme_mod( 'dbr_legal_name', 'UAE Equipment Rental' );
set_theme_mod( 'dbr_phone', '+971 54 738 8695' );
set_theme_mod( 'dbr_whatsapp', '+971 54 738 8695' );
set_theme_mod( 'dbr_address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' );
set_theme_mod( 'dbr_hours', '24/7' );
set_theme_mod( 'dbr_service_areas', 'Dibba, Fujairah, Dubai, Sharjah, Ajman, Ras Al Khaimah, Umm Al Quwain, Abu Dhabi, Al Ain, UAE' );

$metas = array(
	$en['home'] => array( 'Bobcat Rental UAE | CAT 226B With Operator', 'Need a bobcat or skid steer loader in the UAE? Get CAT 226B rental with operator, 24/7 WhatsApp quotes and delivery discussion across the Emirates.' ),
	$ar['home'] => array( 'تأجير بوبكات في الإمارات | CAT 226B مع مشغل', 'تحتاج بوبكات أو سكيد ستير في الإمارات؟ احصل على تأجير CAT 226B مع مشغل، عروض واتساب 24/7 وتوصيل داخل الإمارات.' ),
	$en['services'] => array( 'Equipment Rental Services UAE | Bobcat and Skid Steer Hire', 'Explore UAE compact equipment rental services, including bobcat rental, skid steer loader rental and CAT 226B machine availability.' ),
	$ar['services'] => array( 'خدمات تأجير المعدات في الإمارات | تأجير بوبكات وسكيد ستير', 'تعرف على خدمات تأجير المعدات المدمجة في الإمارات، بما فيها تأجير البوبكات والسكيد ستير وتوفر CAT 226B.' ),
	$en['bobcat'] => array( 'Bobcat Rental Dubai and UAE with Operator', 'Rent a CAT 226B skid steer loader with operator for site cleaning, levelling, loading and backfilling. Call or WhatsApp now for availability.' ),
	$ar['bobcat'] => array( 'تأجير بوبكات دبي والإمارات مع مشغل', 'استأجر سكيد ستير لودر CAT 226B مع مشغل لتنظيف المواقع والتسوية والتحميل والردم. اتصل أو واتساب لمعرفة التوفر.' ),
	$en['skid'] => array( 'Skid Steer Loader Rental UAE | CAT 226B Hire', 'Hire a CAT 226B skid steer loader in the UAE for compact site cleaning, grading, loading, backfilling and material movement.' ),
	$ar['skid'] => array( 'تأجير سكيد ستير لودر في الإمارات | CAT 226B', 'استأجر سكيد ستير لودر CAT 226B في الإمارات لتنظيف المواقع الصغيرة والتمهيد والتحميل والردم ونقل المواد.' ),
	$en['operator'] => array( 'Bobcat With Operator UAE | CAT 226B Rental', 'Request bobcat rental with operator in the UAE. Send job location, work type, date, duration and attachment requirement for a practical quote.' ),
	$ar['operator'] => array( 'بوبكات مع مشغل في الإمارات | تأجير CAT 226B', 'اطلب تأجير بوبكات مع مشغل في الإمارات. أرسل موقع العمل ونوعه والتاريخ والمدة والملحق المطلوب للحصول على سعر عملي.' ),
	$en['machine'] => array( 'CAT 226B Skid Steer Loader Rental UAE | Specs and Uses', 'View CAT 226B specifications, typical jobs, attachments and rental availability in the UAE. Request price and dispatch time on WhatsApp.' ),
	$ar['machine'] => array( 'تأجير سكيد ستير CAT 226B في الإمارات | المواصفات والاستخدامات', 'شاهد مواصفات CAT 226B والأعمال المناسبة والملحقات وتوفر التأجير داخل الإمارات. اطلب السعر ووقت الإرسال عبر واتساب.' ),
	$en['areas'] => array( 'Bobcat Rental Service Areas UAE | Dispatch Coverage', 'UAE service area hub for CAT 226B bobcat and skid steer loader rental from Dibba, Fujairah with delivery discussion across the Emirates.' ),
	$ar['areas'] => array( 'مناطق خدمة تأجير البوبكات في الإمارات | تغطية التوصيل', 'صفحة مناطق خدمة تأجير بوبكات وسكيد ستير CAT 226B من دبا، الفجيرة مع مناقشة التوصيل داخل الإمارات.' ),
	$en['dubai'] => array( 'Bobcat Rental in Dubai | CAT 226B Skid Steer Hire', 'Need bobcat rental in Dubai? Request CAT 226B skid steer loader with operator for site cleaning, loading, backfilling and compact material handling.' ),
	$ar['dubai'] => array( 'تأجير بوبكات في دبي | سكيد ستير CAT 226B', 'تحتاج تأجير بوبكات في دبي؟ اطلب سكيد ستير لودر CAT 226B مع مشغل لتنظيف المواقع والتحميل والردم ومناولة المواد.' ),
	$en['blog'] => array( 'Bobcat Rental Guides UAE | Skid Steer Loader Advice', 'Read practical guides about bobcat rental price, CAT 226B specs, skid steer loader uses, attachments and UAE jobsite planning.' ),
	$ar['blog'] => array( 'أدلة تأجير البوبكات في الإمارات | نصائح السكيد ستير', 'اقرأ أدلة عملية عن أسعار تأجير البوبكات ومواصفات CAT 226B واستخدامات السكيد ستير والملحقات وتجهيز مواقع العمل.' ),
	$en['contact'] => array( 'Contact UAE Equipment Rental | WhatsApp Quote Request', 'Send your UAE bobcat rental job details by form, call or WhatsApp. Include location, date, duration and attachment requirement.' ),
	$ar['contact'] => array( 'اتصل بـ UAE Equipment Rental | طلب سعر واتساب', 'أرسل تفاصيل تأجير البوبكات في الإمارات عبر النموذج أو الاتصال أو واتساب. اذكر الموقع والتاريخ والمدة والملحق المطلوب.' ),
);

foreach ( $posts as $post ) {
	$metas[ $post_ids[ $post['key'] ]['en'] ] = array( $post['en'][1], $post['en'][3] );
	$metas[ $post_ids[ $post['key'] ]['ar'] ] = array( $post['ar'][1], $post['ar'][3] );
}

foreach ( $metas as $post_id => $meta ) {
	dbr_set_yoast_meta( $post_id, $meta[0], $meta[1] );
}

flush_rewrite_rules();

WP_CLI::success( 'Created bilingual English/Arabic pages, posts, categories, Yoast metadata, Polylang translations and menus.' );
