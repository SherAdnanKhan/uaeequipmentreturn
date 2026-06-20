<?php
/**
 * Theme setup for UAE Equipment Rental.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dbr_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'dubai-bobcat-rental' ),
			'footer'  => __( 'Footer Menu', 'dubai-bobcat-rental' ),
		)
	);
}
add_action( 'after_setup_theme', 'dbr_setup' );

function dbr_enqueue_assets() {
	wp_enqueue_style( 'dbr-style', get_stylesheet_uri(), array(), '1.0.5' );
	wp_enqueue_script( 'dbr-script', get_template_directory_uri() . '/assets/site.js', array(), '1.0.2', true );
	wp_localize_script(
		'dbr-script',
		'dbrBusiness',
		array(
				'whatsapp' => dbr_get_business_value( 'whatsapp', '+971547388695' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dbr_enqueue_assets' );

function dbr_get_business_value( $key, $fallback = '' ) {
	$value = get_theme_mod( 'dbr_' . $key, $fallback );
	return is_string( $value ) ? $value : $fallback;
}

function dbr_phone_href() {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', dbr_get_business_value( 'phone', '+971 54 738 8695' ) );
}

function dbr_whatsapp_href( $message = 'Hello, I need a CAT 226B bobcat rental quote in the UAE.' ) {
	$number = preg_replace( '/[^0-9]/', '', dbr_get_business_value( 'whatsapp', '+971 54 738 8695' ) );
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

function dbr_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'dbr_business',
		array(
			'title'       => __( 'Business Details', 'dubai-bobcat-rental' ),
			'description' => __( 'Replace placeholders before production launch. These values power CTAs and schema.', 'dubai-bobcat-rental' ),
			'priority'    => 35,
		)
	);

	$fields = array(
		'business_name' => array( 'Business name', 'UAE Equipment Rental' ),
		'legal_name'    => array( 'Legal / licence name', '[Legal Entity Name]' ),
		'phone'         => array( 'Phone number', '+971 54 738 8695' ),
		'whatsapp'      => array( 'WhatsApp number', '+971 54 738 8695' ),
		'address'       => array( 'Address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' ),
		'hours'         => array( 'Opening hours', '24/7' ),
		'service_areas' => array( 'Service areas', 'Dibba, Fujairah, Dubai, Sharjah, Ajman, Ras Al Khaimah, Umm Al Quwain, Abu Dhabi, Al Ain, UAE' ),
	);

	foreach ( $fields as $key => $data ) {
		$wp_customize->add_setting(
			'dbr_' . $key,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'dbr_' . $key,
			array(
				'label'   => $data[0],
				'section' => 'dbr_business',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'dbr_customize_register' );

function dbr_schema() {
	if ( ! is_front_page() ) {
		return;
	}

	$site_url  = home_url( '/' );
	$image_url = get_template_directory_uri() . '/assets/bobcat-hero.jpg';
	$areas     = array_map( 'trim', explode( ',', dbr_get_business_value( 'service_areas', 'Dubai' ) ) );

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'       => 'LocalBusiness',
				'@id'         => $site_url . '#business',
				'name'        => dbr_get_business_value( 'business_name', 'UAE Equipment Rental' ),
				'legalName'   => dbr_get_business_value( 'legal_name', '[Legal Entity Name]' ),
				'url'         => $site_url,
				'telephone'   => dbr_get_business_value( 'phone', '+971 54 738 8695' ),
				'address'     => dbr_get_business_value( 'address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' ),
				'areaServed'  => $areas,
				'openingHours'=> 'Mo-Su 00:00-23:59',
				'priceRange'  => '$$',
				'image'       => $image_url,
				'description' => 'CAT 226B bobcat and skid steer loader rental with operator across the UAE from Dibba, Fujairah.',
			),
			array(
				'@type'       => 'Service',
				'@id'         => $site_url . '#service',
				'serviceType' => 'Bobcat rental UAE',
				'provider'    => array( '@id' => $site_url . '#business' ),
				'areaServed'  => $areas,
				'description' => 'CAT 226B skid steer loader rental with operator for site cleaning, loading, grading, backfilling and material handling.',
			),
			array(
				'@type'      => 'FAQPage',
				'@id'        => $site_url . '#faq',
				'mainEntity' => array(
					array(
						'@type'          => 'Question',
						'name'           => 'Is this a Bobcat or a skid steer loader?',
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => 'Many customers search for bobcat rental, but the available machine is a CAT 226B skid steer loader.',
						),
					),
					array(
						'@type'          => 'Question',
						'name'           => 'Can I rent the machine with an operator?',
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => 'The bobcat is provided with an operator. Availability and delivery cost depend on date, job location and work type.',
						),
					),
				),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dbr_schema' );

function dbr_get_breadcrumb_items() {
	$items = array(
		array(
			'name' => __( 'Home', 'dubai-bobcat-rental' ),
			'url'  => home_url( '/' ),
		),
	);

	if ( is_home() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$items[]       = array(
			'name' => $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Blog', 'dubai-bobcat-rental' ),
			'url'  => $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' ),
		);
		return $items;
	}

	if ( is_singular( 'post' ) ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$items[]       = array(
			'name' => $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Blog', 'dubai-bobcat-rental' ),
			'url'  => $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' ),
		);
		$items[]       = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
		return $items;
	}

	if ( is_page() && ! is_front_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = array(
				'name' => get_the_title( $ancestor_id ),
				'url'  => get_permalink( $ancestor_id ),
			);
		}
		$items[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	}

	return $items;
}

function dbr_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = dbr_get_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'dubai-bobcat-rental' ); ?>">
		<?php foreach ( $items as $index => $item ) : ?>
			<?php if ( $index > 0 ) : ?>
				<span aria-hidden="true">/</span>
			<?php endif; ?>
			<?php if ( $index + 1 === count( $items ) ) : ?>
				<strong><?php echo esc_html( $item['name'] ); ?></strong>
			<?php else : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
	<?php
}

function dbr_breadcrumb_schema() {
	if ( is_front_page() ) {
		return;
	}

	$items = dbr_get_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}

	$list = array();
	foreach ( $items as $index => $item ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $item['name'],
			'item'     => $item['url'],
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dbr_breadcrumb_schema' );

function dbr_favicon() {
	echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
}
add_action( 'wp_head', 'dbr_favicon' );

function dbr_robots_txt( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}

	$output  = "User-agent: *\n";
	$output .= "Allow: /\n\n";
	$output .= 'Sitemap: ' . home_url( '/sitemap_index.xml' ) . "\n";

	return $output;
}
add_filter( 'robots_txt', 'dbr_robots_txt', 10, 2 );
