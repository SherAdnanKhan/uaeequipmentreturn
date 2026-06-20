<?php
/**
 * Single post template.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$asset_uri     = get_template_directory_uri() . '/assets/';
	$categories    = get_the_category();
	$category_name = $categories ? $categories[0]->name : __( 'Guide', 'dubai-bobcat-rental' );
	$word_count    = str_word_count( wp_strip_all_tags( get_the_content() ) );
	$read_time     = max( 2, (int) ceil( $word_count / 180 ) );
	?>

	<main id="main" class="single-guide">
		<?php dbr_breadcrumbs(); ?>

		<section class="guide-hero">
			<div class="guide-hero__content">
				<p class="eyebrow"><?php echo esc_html( $category_name ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="guide-hero__lede"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="guide-meta" aria-label="<?php esc_attr_e( 'Guide details', 'dubai-bobcat-rental' ); ?>">
					<span><?php esc_html_e( 'UAE rental guide', 'dubai-bobcat-rental' ); ?></span>
					<span><?php echo esc_html( sprintf( __( '%d min read', 'dubai-bobcat-rental' ), $read_time ) ); ?></span>
					<span><?php esc_html_e( 'CAT 226B with operator', 'dubai-bobcat-rental' ); ?></span>
				</div>
				<div class="inner-hero__actions">
					<a class="button primary" href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp Quote', 'dubai-bobcat-rental' ); ?></a>
					<a class="button light" href="<?php echo esc_url( dbr_page_url( 'contact', '/contact/' ) ); ?>"><?php esc_html_e( 'Send Job Details', 'dubai-bobcat-rental' ); ?></a>
				</div>
			</div>
			<figure class="guide-hero__media">
				<img src="<?php echo esc_url( $asset_uri . 'bobcat-side.jpg' ); ?>" alt="<?php esc_attr_e( 'CAT 226B skid steer loader with bucket attachment', 'dubai-bobcat-rental' ); ?>">
				<figcaption><?php esc_html_e( 'CAT 226B bobcat supplied with operator for UAE jobsites', 'dubai-bobcat-rental' ); ?></figcaption>
			</figure>
		</section>

		<section class="section guide-layout guide-layout--article">
			<article class="guide-article">
				<div class="article-summary">
					<strong><?php esc_html_e( 'Before you book', 'dubai-bobcat-rental' ); ?></strong>
					<p><?php esc_html_e( 'The fastest quote comes from clear job details: location, access, work type, date, expected duration and attachment requirement.', 'dubai-bobcat-rental' ); ?></p>
				</div>
				<?php the_content(); ?>
			</article>

			<aside class="guide-sidebar-stack">
				<div class="quote-card guide-sidebar">
					<p class="eyebrow"><?php esc_html_e( 'Need this machine?', 'dubai-bobcat-rental' ); ?></p>
					<h2><?php esc_html_e( 'Send the job details for a quote.', 'dubai-bobcat-rental' ); ?></h2>
					<p><?php esc_html_e( 'Operator is provided with the bobcat. Delivery is free near Fujairah and quoted for other UAE locations.', 'dubai-bobcat-rental' ); ?></p>
					<ul class="quote-list">
						<li><?php esc_html_e( 'Location or map pin', 'dubai-bobcat-rental' ); ?></li>
						<li><?php esc_html_e( 'Work type and access notes', 'dubai-bobcat-rental' ); ?></li>
						<li><?php esc_html_e( 'Date and expected duration', 'dubai-bobcat-rental' ); ?></li>
					</ul>
					<a class="button primary" href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp Quote', 'dubai-bobcat-rental' ); ?></a>
				</div>
				<div class="guide-mini-card">
					<strong><?php esc_html_e( 'Quick contact', 'dubai-bobcat-rental' ); ?></strong>
					<a href="<?php echo esc_url( dbr_phone_href() ); ?>"><?php echo esc_html( dbr_get_business_value( 'phone', '+971 54 738 8695' ) ); ?></a>
					<span><?php esc_html_e( '24/7 call or WhatsApp', 'dubai-bobcat-rental' ); ?></span>
				</div>
			</aside>
		</section>

		<section class="section related-guides related-guides--single">
			<div class="section-heading">
				<p class="eyebrow"><?php esc_html_e( 'More rental guides', 'dubai-bobcat-rental' ); ?></p>
				<h2><?php esc_html_e( 'Helpful reading before you book', 'dubai-bobcat-rental' ); ?></h2>
			</div>
			<div class="post-grid">
				<?php
				$related_guides = new WP_Query(
					array(
						'post_type'      => 'post',
						'posts_per_page' => 3,
						'post__not_in'   => array( get_the_ID() ),
						'no_found_rows'  => true,
					)
				);
				while ( $related_guides->have_posts() ) :
					$related_guides->the_post();
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
