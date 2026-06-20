<?php
/**
 * Fallback template.
 */

get_header();
?>

<main id="main" class="blog-page">
	<section class="inner-hero blog-hero">
		<div class="inner-hero__content">
			<p class="eyebrow"><?php esc_html_e( 'Rental guides', 'dubai-bobcat-rental' ); ?></p>
			<h1><?php single_post_title(); ?></h1>
			<p class="inner-hero__lede"><?php esc_html_e( 'Practical content for Dubai bobcat rental, CAT 226B skid steer jobs, pricing, attachments and service-area planning.', 'dubai-bobcat-rental' ); ?></p>
		</div>
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
			echo '<p>' . esc_html__( 'No content found.', 'dubai-bobcat-rental' ) . '</p>';
		endif;
		?>
		</div>
	</section>
</main>

<?php
get_footer();
