<?php
/**
 * Rich page template for service, machine, area and contact pages.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$page_id     = get_the_ID();
	$slug        = get_post_field( 'post_name', $page_id );
	$parent_id   = (int) wp_get_post_parent_id( $page_id );
	$parent_slug = $parent_id ? get_post_field( 'post_name', $parent_id ) : '';
	$is_service  = 'services' === $parent_slug || in_array( $slug, array( 'services' ), true );
	$is_machine  = 'machines' === $parent_slug || in_array( $slug, array( 'machines' ), true );
	$is_area     = 'service-areas' === $parent_slug || in_array( $slug, array( 'service-areas' ), true );
	$is_contact  = 'contact' === $slug;
	?>

	<main id="main" class="inner-page">
		<?php dbr_breadcrumbs(); ?>
		<section class="inner-hero">
			<div class="inner-hero__content">
				<p class="eyebrow">
					<?php
					if ( $is_service ) {
						esc_html_e( 'Rental service', 'dubai-bobcat-rental' );
					} elseif ( $is_machine ) {
						esc_html_e( 'Machine profile', 'dubai-bobcat-rental' );
					} elseif ( $is_area ) {
						esc_html_e( 'Local dispatch', 'dubai-bobcat-rental' );
					} elseif ( $is_contact ) {
						esc_html_e( 'Quote request', 'dubai-bobcat-rental' );
					} else {
						esc_html_e( 'UAE equipment rental', 'dubai-bobcat-rental' );
					}
					?>
				</p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="inner-hero__lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="inner-hero__actions">
					<a class="button primary" href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp Quote', 'dubai-bobcat-rental' ); ?></a>
					<a class="button light" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Send Job Details', 'dubai-bobcat-rental' ); ?></a>
				</div>
			</div>
			<aside class="inner-hero__panel" aria-label="<?php esc_attr_e( 'Quote checklist', 'dubai-bobcat-rental' ); ?>">
				<strong><?php esc_html_e( 'For a useful quote, send:', 'dubai-bobcat-rental' ); ?></strong>
				<ul>
					<li><?php esc_html_e( 'Job location or map pin', 'dubai-bobcat-rental' ); ?></li>
					<li><?php esc_html_e( 'Work type and site access', 'dubai-bobcat-rental' ); ?></li>
					<li><?php esc_html_e( 'Date, duration and delivery timing', 'dubai-bobcat-rental' ); ?></li>
					<li><?php esc_html_e( 'Attachment requirement if known', 'dubai-bobcat-rental' ); ?></li>
				</ul>
			</aside>
		</section>

		<?php if ( $is_service ) : ?>
			<section class="section page-section">
				<div class="content-layout">
					<article class="lead-panel">
						<?php the_content(); ?>
					</article>
					<aside class="quote-card">
						<p class="eyebrow"><?php esc_html_e( 'Fast path', 'dubai-bobcat-rental' ); ?></p>
						<h2><?php esc_html_e( 'Confirm machine fit before booking.', 'dubai-bobcat-rental' ); ?></h2>
						<p><?php esc_html_e( 'The CAT 226B is best for compact-site support. Share the job details and we will confirm if it suits the work before dispatch is promised.', 'dubai-bobcat-rental' ); ?></p>
						<a class="button primary" href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'Check Availability', 'dubai-bobcat-rental' ); ?></a>
					</aside>
				</div>
				<div class="info-grid">
					<article><span><?php esc_html_e( 'Use case', 'dubai-bobcat-rental' ); ?></span><h3><?php esc_html_e( 'Site cleaning', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Move loose debris, sand, soil and site waste faster than manual labour.', 'dubai-bobcat-rental' ); ?></p></article>
					<article><span><?php esc_html_e( 'Use case', 'dubai-bobcat-rental' ); ?></span><h3><?php esc_html_e( 'Loading support', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Support bucket loading, short-distance material movement and practical site logistics.', 'dubai-bobcat-rental' ); ?></p></article>
					<article><span><?php esc_html_e( 'Use case', 'dubai-bobcat-rental' ); ?></span><h3><?php esc_html_e( 'Backfilling and grading', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Handle compact trench backfilling, surface preparation and small plot grading.', 'dubai-bobcat-rental' ); ?></p></article>
				</div>
			</section>
		<?php elseif ( $is_machine ) : ?>
			<section class="section page-section">
				<div class="content-layout">
					<article class="lead-panel">
						<?php the_content(); ?>
					</article>
					<aside class="quote-card">
						<p class="eyebrow"><?php esc_html_e( 'Machine proof', 'dubai-bobcat-rental' ); ?></p>
						<h2><?php esc_html_e( 'Real CAT 226B photos are now used on the website.', 'dubai-bobcat-rental' ); ?></h2>
						<p><?php esc_html_e( 'The machine is a Caterpillar 226B. Public listing details show model year 2015, yellow colour and American origin.', 'dubai-bobcat-rental' ); ?></p>
					</aside>
				</div>
				<div class="spec-grid page-specs">
					<div><span><?php esc_html_e( 'Machine type', 'dubai-bobcat-rental' ); ?></span><strong>CAT 226B</strong></div>
					<div><span><?php esc_html_e( 'Model year', 'dubai-bobcat-rental' ); ?></span><strong>2015</strong></div>
					<div><span><?php esc_html_e( 'Rated operating capacity', 'dubai-bobcat-rental' ); ?></span><strong>680 kg</strong></div>
					<div><span><?php esc_html_e( 'Operating weight', 'dubai-bobcat-rental' ); ?></span><strong>2641 kg</strong></div>
				</div>
			</section>
		<?php elseif ( $is_area ) : ?>
			<section class="section page-section">
				<div class="content-layout">
					<article class="lead-panel">
						<?php the_content(); ?>
					</article>
					<aside class="quote-card">
						<p class="eyebrow"><?php esc_html_e( 'Local SEO rule', 'dubai-bobcat-rental' ); ?></p>
						<h2><?php esc_html_e( 'UAE-wide service with Fujairah headquarters.', 'dubai-bobcat-rental' ); ?></h2>
						<p><?php esc_html_e( 'The machine can be delivered across the UAE. Delivery is free near Fujairah and quoted for other locations.', 'dubai-bobcat-rental' ); ?></p>
					</aside>
				</div>
				<div class="area-grid page-area-grid">
					<article><h3><?php esc_html_e( 'Fujairah & Dibba', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Headquarters area and best delivery zone.', 'dubai-bobcat-rental' ); ?></p></article>
					<article><h3><?php esc_html_e( 'Dubai & Sharjah', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Available for contractor and construction support jobs.', 'dubai-bobcat-rental' ); ?></p><a href="<?php echo esc_url( home_url( '/service-areas/dubai/' ) ); ?>"><?php esc_html_e( 'View Dubai page', 'dubai-bobcat-rental' ); ?></a></article>
					<article><h3><?php esc_html_e( 'Ajman, RAK & UAQ', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Delivery quoted by exact location and working duration.', 'dubai-bobcat-rental' ); ?></p></article>
					<article><h3><?php esc_html_e( 'Abu Dhabi & Al Ain', 'dubai-bobcat-rental' ); ?></h3><p><?php esc_html_e( 'Long-distance booking available after details are discussed.', 'dubai-bobcat-rental' ); ?></p></article>
				</div>
			</section>
		<?php elseif ( $is_contact ) : ?>
			<section class="section page-section contact-layout">
				<article class="lead-panel">
					<?php the_content(); ?>
				</article>
				<aside class="quote-card contact-card">
					<p class="eyebrow"><?php esc_html_e( 'Direct contact', 'dubai-bobcat-rental' ); ?></p>
					<h2><?php esc_html_e( 'Call or WhatsApp for urgent availability.', 'dubai-bobcat-rental' ); ?></h2>
					<p><?php echo esc_html( dbr_get_business_value( 'phone', '+971 54 738 8695' ) ); ?></p>
					<div class="hero-actions">
						<a class="button primary" href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp', 'dubai-bobcat-rental' ); ?></a>
						<a class="button light" href="<?php echo esc_url( dbr_phone_href() ); ?>"><?php esc_html_e( 'Call', 'dubai-bobcat-rental' ); ?></a>
					</div>
				</aside>
			</section>
		<?php else : ?>
			<section class="section page-section">
				<article class="lead-panel">
					<?php the_content(); ?>
				</article>
			</section>
		<?php endif; ?>

		<section class="section related-guides">
			<div class="section-heading">
				<p class="eyebrow"><?php esc_html_e( 'Rental guides', 'dubai-bobcat-rental' ); ?></p>
				<h2><?php esc_html_e( 'Helpful reading before you book', 'dubai-bobcat-rental' ); ?></h2>
			</div>
			<div class="post-grid">
				<?php
				$guide_query = new WP_Query(
					array(
						'post_type'      => 'post',
						'posts_per_page' => 3,
						'no_found_rows'  => true,
					)
				);
				while ( $guide_query->have_posts() ) :
					$guide_query->the_post();
					?>
					<article class="post-card">
						<span><?php echo esc_html( get_the_category()[0]->name ?? __( 'Guide', 'dubai-bobcat-rental' ) ); ?></span>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	</main>
	<?php
endwhile;

get_footer();
