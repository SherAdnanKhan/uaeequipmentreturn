<?php
/**
 * Populate the local WordPress install from the SRS.
 *
 * Run with:
 * php /opt/homebrew/bin/wp eval-file setup-wordpress-content.php --path=wordpress
 */

wp_delete_post( 1, true );
wp_delete_post( 2, true );

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

function dbr_upsert_page( $slug, $title, $content, $parent_id = 0, $excerpt = '' ) {
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

	$existing = get_page_by_path( $path, OBJECT, 'page' );
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
		return wp_update_post( $data );
	}

	return wp_insert_post( $data );
}

function dbr_upsert_post( $slug, $title, $content, $excerpt, $category_name ) {
	$existing = get_page_by_path( $slug, OBJECT, 'post' );
	$cat_id   = wp_create_category( $category_name );
	$data     = array(
		'post_type'     => 'post',
		'post_name'     => $slug,
		'post_title'    => $title,
		'post_content'  => $content,
		'post_excerpt'  => $excerpt,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_category' => array( $cat_id ),
	);

	if ( $existing ) {
		$data['ID'] = $existing->ID;
		return wp_update_post( $data );
	}

	return wp_insert_post( $data );
}

function dbr_set_yoast_meta( $post_id, $title, $description ) {
	update_post_meta( $post_id, '_yoast_wpseo_title', $title );
	update_post_meta( $post_id, '_yoast_wpseo_metadesc', $description );
}

dbr_delete_bad_flat_pages();

$home_id = dbr_upsert_page(
	'home',
	'Bobcat Rental UAE',
	'',
	0,
	'CAT 226B bobcat and skid steer loader rental with operator across the UAE.'
);

$services_id = dbr_upsert_page(
	'services',
	'Equipment Rental Services',
	'<p>UAE-focused compact equipment rental services for contractors, site supervisors, villa builders, landscaping teams and small project managers. The launch offer is deliberately narrow: one CAT 226B skid steer loader with operator, clear job-fit guidance, and fast quote paths through phone and WhatsApp.</p>',
	0,
	'Bobcat and skid steer loader rental service hub for UAE jobsites.'
);

$bobcat_id = dbr_upsert_page(
	'bobcat-rental-dubai',
	'Bobcat Rental Dubai with Fast WhatsApp Quotes',
	'<p>If you are looking for bobcat rental in Dubai or anywhere in the UAE, we provide CAT 226B skid steer loader hire with operator for practical site work where access, manoeuvrability and fast loading matter. Customers often call this machine a bobcat, while the correct machine type is a skid steer loader.</p><p>Use this service for site cleaning, debris movement, compact loading, grading, levelling, trench backfilling and short-distance material handling.</p>',
	$services_id,
	'CAT 226B bobcat rental with operator for site cleaning, loading, levelling and backfilling.'
);

$skid_id = dbr_upsert_page(
	'skid-steer-loader-rental-dubai',
	'Skid Steer Loader Rental Dubai',
	'<p>Rent a compact skid steer loader with operator in the UAE for loading, site cleaning, grading, levelling, backfilling and material movement. This page supports buyers who use the technical machine term rather than the market term bobcat.</p>',
	$services_id,
	'Technical skid steer loader rental page for UAE commercial search intent.'
);

$operator_id = dbr_upsert_page(
	'bobcat-with-operator-dubai',
	'Bobcat With Operator Dubai',
	'<p>Request a CAT 226B skid steer loader with operator for UAE site work. Operator is provided with the bobcat, while dispatch and delivery cost depend on the job date, location, attachment requirement, site access and work type.</p>',
	$services_id,
	'Bobcat rental with operator for UAE compact jobsites.'
);

$machines_id = dbr_upsert_page(
	'machines',
	'Machines',
	'<p>Machine pages provide specifications, job-fit guidance, attachment notes and quote paths. The initial fleet page is the CAT 226B skid steer loader.</p>',
	0,
	'Machine hub for rental equipment specifications and availability.'
);

$machine_id = dbr_upsert_page(
	'cat-226b-skid-steer-loader',
	'CAT 226B Skid Steer Loader',
	'<p>The rental machine is a CAT 226B skid steer loader, commonly searched as a bobcat. Public machine details confirm Caterpillar 226B, model year 2015, yellow colour and American origin.</p><p>Key anchors: rated operating capacity 680 kg, tipping load 1360 kg, operating weight 2641 kg, with auxiliary hydraulics for compatible work tools.</p>',
	$machines_id,
	'CAT 226B skid steer loader specs, uses and rental availability in the UAE.'
);

$areas_id = dbr_upsert_page(
	'service-areas',
	'Service Areas',
	'<p>The business is headquartered in Dibba, Fujairah and can discuss bobcat delivery across the UAE. Delivery can be free near Fujairah, while other locations are quoted by distance, timing and job duration.</p>',
	0,
	'UAE service area hub for bobcat and skid steer rental.'
);

$dubai_id = dbr_upsert_page(
	'dubai',
	'Bobcat Rental in Dubai',
	'<p>Dubai service is available for bobcat and CAT 226B skid steer loader rental with operator. Share your job location, work type, date, duration and attachment requirement by WhatsApp for availability and delivery discussion.</p>',
	$areas_id,
	'Bobcat rental in Dubai with CAT 226B skid steer and operator availability.'
);

$about_id = dbr_upsert_page(
	'about',
	'About UAE Equipment Rental',
	'<p>UAE Equipment Rental is headquartered in Dibba, Fujairah and currently focuses this site on one real machine: a CAT 226B skid steer loader supplied with operator. The business can expand into a broader heavy equipment rental taxonomy later without changing the site structure.</p>',
	0,
	'About the UAE bobcat and skid steer rental business.'
);

$contact_id = dbr_upsert_page(
	'contact',
	'Contact and Quote Request',
	'<p>Call or WhatsApp 24/7 for current machine availability, operator-led bobcat rental and dispatch timing.</p><ul><li>Phone: +971 54 738 8695</li><li>WhatsApp: +971 54 738 8695</li><li>Headquarters: Dibba, Fujairah, United Arab Emirates</li><li>Service areas: all UAE emirates by delivery discussion</li></ul><p>Delivery can be free near Fujairah. Other UAE locations are quoted by distance, date, timing and booking duration.</p>',
	0,
	'Contact and quote request page for bobcat rental in the UAE.'
);

$blog_id = dbr_upsert_page(
	'blog',
	'Bobcat Rental Guides',
	'<p>Guides for skid steer loader rental, CAT 226B use cases, pricing questions and UAE jobsite planning.</p>',
	0,
	'Blog and buying guides for UAE bobcat and skid steer rental searchers.'
);

$privacy_id = dbr_upsert_page(
	'privacy-policy',
	'Privacy Policy',
	'<p>UAE Equipment Rental respects customer privacy. This website collects the details you choose to send through quote forms, phone calls or WhatsApp messages, such as name, mobile number, job location, service requirement, date, duration and attachment needs.</p><h2>How we use information</h2><p>We use submitted information to respond to bobcat rental enquiries, confirm machine availability, discuss delivery, prepare quotes and improve customer service.</p><h2>Contact and WhatsApp</h2><p>When you contact us by phone or WhatsApp, your message is handled through those communication providers and may be subject to their own privacy terms.</p><h2>Website data</h2><p>The site may use basic cookies and security tools to keep the website working, remember consent choices and understand general site performance. Analytics or advertising tools should only be enabled with the correct consent settings.</p><h2>Contact</h2><p>For privacy questions, contact UAE Equipment Rental at +971 54 738 8695.</p>'
);

$cookie_id = dbr_upsert_page(
	'cookie-policy',
	'Cookie Policy',
	'<p>This website uses cookies to keep the site working, remember cookie choices and support basic security and performance. Some cookies are necessary for the website to function correctly.</p><h2>Optional cookies</h2><p>If analytics, advertising or third-party tracking tools are added, they should be managed through the cookie consent banner before those cookies are stored.</p><h2>Managing cookies</h2><p>You can accept, reject or customise cookie choices through the consent banner where available. You can also clear cookies in your browser settings.</p><h2>Contact</h2><p>For cookie questions, contact UAE Equipment Rental at +971 54 738 8695.</p>'
);

$post_one = dbr_upsert_post(
	'cat-226b-specifications-and-uses',
	'CAT 226B Specifications and Uses for Dubai Jobsites',
	'<p>The CAT 226B skid steer loader is a compact machine suited to site cleaning, loading, grading, backfilling and landscaping support where access is tight.</p><h2>Specification anchors</h2><ul><li>Machine type: Caterpillar 226B</li><li>Model year: 2015</li><li>Rated operating capacity: 680 kg</li><li>Tipping load: 1360 kg</li><li>Operating weight: 2641 kg</li><li>Auxiliary hydraulics for compatible work tools</li></ul><h2>Best-fit jobs</h2><p>The machine is most useful where a full-size loader is excessive but manual labour is too slow: villa plots, compact construction sites, interlock preparation, backfilling, loading loose material and clearing debris.</p>',
	'CAT 226B model year, rated capacity, tipping load, operating weight and practical UAE jobsite uses.',
	'Machine Specs'
);

$post_two = dbr_upsert_post(
	'what-is-a-skid-steer-loader',
	'What Is a Skid Steer Loader?',
	'<p>A skid steer loader is a compact wheeled loader used for jobs such as loading, site cleaning, grading and material movement. In the UAE market, many customers search for this machine as a bobcat.</p><h2>Why contractors choose it</h2><p>The machine turns within a tight footprint, uses a front loader arm, and can work with different tools depending on availability. That makes it practical for compact sites where manoeuvrability matters.</p>',
	'A plain-English guide to skid steer loaders and why UAE customers often call them bobcats.',
	'Rental Guides'
);

$post_three = dbr_upsert_post(
	'bobcat-rental-price-dubai-guide',
	'Bobcat Rental Price Dubai Guide',
	'<p>Bobcat rental price in the UAE depends on machine availability, booking duration, location, attachment requirement and delivery timing. The bobcat is provided with an operator. The fastest way to quote is to share job details on WhatsApp.</p><h2>What affects price</h2><ul><li>Half-day, full-day, weekly or monthly duration</li><li>Bucket, forks, auger or other attachment needs</li><li>Delivery distance and site access</li><li>Urgency and working hours</li><li>Whether the job is near Fujairah or requires long-distance delivery</li></ul>',
	'Factors that affect bobcat rental price in the UAE and what to send for an accurate quote.',
	'Pricing'
);

$post_four = dbr_upsert_post(
	'bobcat-for-site-cleaning-dubai',
	'Bobcat for Site Cleaning in Dubai',
	'<p>A CAT 226B skid steer loader can speed up compact site cleaning where loose debris, sand, soil or construction waste needs to be gathered and moved. It is especially useful on villa, landscaping and small construction jobsites.</p><h2>When it fits</h2><p>It fits best when access is tight, material volumes are moderate, and a compact machine can save labour time without bringing in oversized equipment.</p>',
	'How bobcat rental helps with construction site cleaning and debris movement in the UAE.',
	'Use Cases'
);

$post_five = dbr_upsert_post(
	'skid-steer-backfilling-grading-dubai',
	'Skid Steer for Backfilling and Grading in Dubai',
	'<p>Skid steer loaders are practical for backfilling trenches, spreading loose material and preparing compact surfaces. They are not a replacement for every earthmoving machine, but they are efficient for smaller controlled jobs.</p><h2>Useful project types</h2><ul><li>Trench backfilling</li><li>Interlock base preparation</li><li>Landscape grading</li><li>Small plot levelling</li></ul>',
	'Use a skid steer loader for backfilling, grading and compact surface preparation in the UAE.',
	'Use Cases'
);

$post_six = dbr_upsert_post(
	'bobcat-with-operator-what-to-prepare',
	'Bobcat With Operator: What to Prepare Before Booking',
	'<p>Before booking a bobcat with operator, prepare the job location, access notes, expected work type, date, duration, photos if available, and attachment requirement. Clear details help confirm whether the CAT 226B is suitable.</p><h2>Send these details</h2><ul><li>Google Maps location or pin</li><li>Work description</li><li>Site access width and surface</li><li>Preferred date and working hours</li><li>Bucket, fork, auger or other tool needs</li></ul>',
	'Checklist for booking bobcat rental with operator support in the UAE.',
	'Rental Guides'
);

$post_seven = dbr_upsert_post(
	'bobcat-vs-skid-steer-loader',
	'Bobcat vs Skid Steer Loader: What Is the Difference?',
	'<p>Bobcat is often used as a generic market term, but skid steer loader is the machine category. The available rental machine on this site is a CAT 226B skid steer loader, so the website uses both the search term and the correct technical term.</p>',
	'Explain the difference between the common search term bobcat and the correct machine type skid steer loader.',
	'Rental Guides'
);

$post_eight = dbr_upsert_post(
	'skid-steer-attachments-guide-dubai',
	'Skid Steer Attachments Guide for Dubai Jobs',
	'<p>Skid steer loaders can support different work tools depending on machine compatibility and actual availability. Common attachment categories include buckets, forks, augers, brooms, trenchers and hammers.</p><p>Do not assume every attachment is available on every date. Mention the required tool when requesting a quote.</p>',
	'Common skid steer attachment types and when to request them for Dubai jobs.',
	'Attachments'
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
update_option( 'page_for_posts', $blog_id );
update_option( 'blogname', 'UAE Equipment Rental' );
update_option( 'blogdescription', 'CAT 226B bobcat rental with operator across the UAE' );
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules();

$menu_name = 'Primary Navigation';
$menu      = wp_get_nav_menu_object( $menu_name );
$menu_id   = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );

foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $item ) {
	wp_delete_post( $item->ID, true );
}

$menu_items = array(
	$home_id     => 'Home',
	$bobcat_id   => 'Bobcat Rental UAE',
	$machine_id  => 'CAT 226B',
	$services_id => 'Services',
	$areas_id    => 'Service Areas',
	$blog_id     => 'Blog',
	$contact_id  => 'Contact',
);

foreach ( $menu_items as $item_id => $label ) {
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $label,
			'menu-item-object-id' => $item_id,
			'menu-item-object'    => 'page',
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		)
	);
}

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
$locations['footer']  = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

set_theme_mod( 'dbr_business_name', 'UAE Equipment Rental' );
set_theme_mod( 'dbr_legal_name', '[Legal Entity Name]' );
set_theme_mod( 'dbr_phone', '+971 54 738 8695' );
set_theme_mod( 'dbr_whatsapp', '+971 54 738 8695' );
set_theme_mod( 'dbr_address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' );
set_theme_mod( 'dbr_hours', '24/7' );
set_theme_mod( 'dbr_service_areas', 'Dibba, Fujairah, Dubai, Sharjah, Ajman, Ras Al Khaimah, Umm Al Quwain, Abu Dhabi, Al Ain, UAE' );

$metas = array(
	$home_id     => array( 'Bobcat Rental UAE | CAT 226B With Operator', 'Need a bobcat or skid steer loader in the UAE? Get CAT 226B rental with operator, 24/7 WhatsApp quotes and delivery discussion across the Emirates.' ),
	$services_id => array( 'Equipment Rental Services UAE | Bobcat and Skid Steer Hire', 'Explore UAE compact equipment rental services, including bobcat rental, skid steer loader rental and CAT 226B machine availability.' ),
	$bobcat_id   => array( 'Bobcat Rental Dubai and UAE with Operator', 'Rent a CAT 226B skid steer loader with operator for site cleaning, levelling, loading and backfilling. Call or WhatsApp now for availability.' ),
	$skid_id     => array( 'Skid Steer Loader Rental UAE | CAT 226B Hire', 'Hire a CAT 226B skid steer loader in the UAE for compact site cleaning, grading, loading, backfilling and material movement.' ),
	$operator_id => array( 'Bobcat With Operator UAE | CAT 226B Rental', 'Request bobcat rental with operator in the UAE. Send job location, work type, date, duration and attachment requirement for a practical quote.' ),
	$machine_id  => array( 'CAT 226B Skid Steer Loader Rental UAE | Specs and Uses', 'View CAT 226B specifications, typical jobs, attachments and rental availability in the UAE. Request price and dispatch time on WhatsApp.' ),
	$areas_id    => array( 'Bobcat Rental Service Areas UAE | Dispatch Coverage', 'UAE service area hub for CAT 226B bobcat and skid steer loader rental from Dibba, Fujairah with delivery discussion across the Emirates.' ),
	$dubai_id    => array( 'Bobcat Rental in Dubai | CAT 226B Skid Steer Hire', 'Need bobcat rental in Dubai? Request CAT 226B skid steer loader with operator for site cleaning, loading, backfilling and compact material handling.' ),
	$blog_id     => array( 'Bobcat Rental Guides UAE | Skid Steer Loader Advice', 'Read practical guides about bobcat rental price, CAT 226B specs, skid steer loader uses, attachments and UAE jobsite planning.' ),
	$contact_id  => array( 'Contact UAE Equipment Rental | WhatsApp Quote Request', 'Send your UAE bobcat rental job details by form, call or WhatsApp. Include location, date, duration and attachment requirement.' ),
	$post_one    => array( 'CAT 226B Specifications and Uses for UAE Jobsites', 'Learn the CAT 226B model year, rated operating capacity, tipping load, work-tool support and best use cases for UAE construction and landscaping work.' ),
	$post_two    => array( 'What Is a Skid Steer Loader? | Bobcat Rental UAE Guide', 'Learn what a skid steer loader is, why customers call it a bobcat, and which compact UAE jobsites it can support.' ),
	$post_three  => array( 'Bobcat Rental Price UAE Guide | Quote Factors', 'Understand what affects bobcat rental price in the UAE, including duration, location, attachments and delivery timing.' ),
	$post_four   => array( 'Bobcat for Site Cleaning UAE | Compact Loader Use Case', 'See when a CAT 226B skid steer loader can help with site cleaning, debris movement and compact construction support in the UAE.' ),
	$post_five   => array( 'Skid Steer Backfilling and Grading UAE | CAT 226B Uses', 'Use a skid steer loader for trench backfilling, grading, interlock preparation and compact surface work in the UAE.' ),
	$post_six    => array( 'Bobcat With Operator UAE | Booking Checklist', 'Prepare the right details before booking bobcat rental with operator support: location, access, work type, date, duration and attachment needs.' ),
	$post_seven  => array( 'Bobcat vs Skid Steer Loader | UAE Rental Guide', 'Understand the difference between the common term bobcat and the technical machine category skid steer loader.' ),
	$post_eight  => array( 'Skid Steer Attachments Guide UAE | Buckets, Forks, Augers', 'Learn common skid steer attachment types and why you should mention required tools when requesting UAE rental availability.' ),
);

foreach ( $metas as $post_id => $meta ) {
	dbr_set_yoast_meta( $post_id, $meta[0], $meta[1] );
}

WP_CLI::success( 'Created SRS pages, child URLs, blog posts, Yoast metadata and menus.' );
