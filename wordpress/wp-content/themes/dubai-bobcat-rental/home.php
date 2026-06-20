<?php
/**
 * Blog index template.
 */

get_header();
?>

<main id="main" class="blog-page">
	<?php dbr_breadcrumbs(); ?>
	<section class="inner-hero blog-hero">
		<div class="inner-hero__content">
			<p class="eyebrow"><?php esc_html_e( 'Rental guides', 'dubai-bobcat-rental' ); ?></p>
			<h1><?php esc_html_e( 'Bobcat Rental Guides for Dubai Jobsites', 'dubai-bobcat-rental' ); ?></h1>
			<p class="inner-hero__lede"><?php esc_html_e( 'Practical articles for contractors and site supervisors comparing bobcat rental, skid steer loader jobs, CAT 226B specifications, attachments, pricing and booking details.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<aside class="inner-hero__panel">
			<strong><?php esc_html_e( 'Content clusters', 'dubai-bobcat-rental' ); ?></strong>
			<ul>
				<li><?php esc_html_e( 'Machine specs and use cases', 'dubai-bobcat-rental' ); ?></li>
				<li><?php esc_html_e( 'Pricing and booking guides', 'dubai-bobcat-rental' ); ?></li>
				<li><?php esc_html_e( 'Dubai jobsite applications', 'dubai-bobcat-rental' ); ?></li>
			</ul>
		</aside>
	</section>

	<section class="section blog-listing">
		<div class="post-grid post-grid--wide">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<article class="post-card post-card--large">
						<span><?php echo esc_html( get_the_category()[0]->name ?? __( 'Guide', 'dubai-bobcat-rental' ) ); ?></span>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						<a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read guide', 'dubai-bobcat-rental' ); ?></a>
					</article>
					<?php
				endwhile;
			else :
				echo '<p>' . esc_html__( 'No guides found yet.', 'dubai-bobcat-rental' ) . '</p>';
			endif;
			?>
		</div>
	</section>
</main>

<?php
get_footer();
