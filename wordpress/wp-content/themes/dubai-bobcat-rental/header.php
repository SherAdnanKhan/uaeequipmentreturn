<?php
/**
 * Site header.
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'dubai-bobcat-rental' ); ?></a>

<header class="site-header">
	<a class="brand" href="<?php echo esc_url( dbr_home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<span class="brand-mark">UER</span>
		<span>
			<strong><?php echo esc_html( dbr_get_business_value( 'business_name', 'UAE Equipment Rental' ) ); ?></strong>
			<small><?php esc_html_e( 'CAT 226B bobcat with operator', 'dubai-bobcat-rental' ); ?></small>
		</span>
	</a>
	<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
		<span></span>
		<span></span>
		<span></span>
	</button>
	<nav id="site-nav" class="site-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'dubai-bobcat-rental' ); ?>">
		<ul id="menu-primary-navigation" class="menu dbr-primary-menu">
			<?php foreach ( dbr_primary_nav_items() as $nav_item ) : ?>
				<li class="menu-item">
					<a href="<?php echo esc_url( $nav_item['url'] ); ?>"><?php echo esc_html( $nav_item['label'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php dbr_language_switcher(); ?>
		<a class="button primary nav-cta" href="<?php echo esc_url( dbr_phone_href() ); ?>"><?php esc_html_e( 'Call', 'dubai-bobcat-rental' ); ?></a>
	</nav>
</header>
