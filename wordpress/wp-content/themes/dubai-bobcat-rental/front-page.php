<?php
/**
 * Front page template.
 */

get_header();

$hero_image = get_template_directory_uri() . '/assets/bobcat-hero.jpg';
$asset_uri  = get_template_directory_uri() . '/assets/';
$reviews    = array(
	'Excellent bobcat rental service in Dubai. The machine arrived on time, the operator knew the work, and the site cleaning was completed properly. Highly recommended.',
	'We hired their skid steer loader for sand shifting and levelling work. Professional service, fair pricing and a quick response on WhatsApp.',
	'Good experience with UAE Equipment Rental. The CAT bobcat was in good condition and helped us finish construction site work faster.',
	'Reliable bobcat rental service with operator. They handled debris removal and land levelling smoothly, and I would use their service again.',
	'Very helpful team and fast service. We needed a bobcat urgently for a small construction job in Dubai, and they arranged it quickly.',
);
?>

<main id="main">
	<section class="hero">
		<div class="hero-media" aria-hidden="true">
			<img src="<?php echo esc_url( $hero_image ); ?>" alt="">
		</div>
		<div class="hero-overlay"></div>
		<div class="hero-content">
			<p class="eyebrow"><?php esc_html_e( 'CAT 226B bobcat with operator', 'dubai-bobcat-rental' ); ?></p>
			<h1><?php esc_html_e( 'Bobcat Rental UAE', 'dubai-bobcat-rental' ); ?></h1>
			<p class="hero-lede"><?php esc_html_e( 'CAT 226B skid steer loader rental with operator for site cleaning, loading, levelling, trench backfilling and compact material handling across the UAE.', 'dubai-bobcat-rental' ); ?></p>
			<div class="hero-actions">
				<a class="button primary" href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp Quote', 'dubai-bobcat-rental' ); ?></a>
				<a class="button hero-call" href="<?php echo esc_url( dbr_phone_href() ); ?>"><?php esc_html_e( 'Call Now', 'dubai-bobcat-rental' ); ?></a>
			</div>
		</div>
	</section>

	<section class="trust-band" aria-label="<?php esc_attr_e( 'Rental highlights', 'dubai-bobcat-rental' ); ?>">
		<div><strong><?php esc_html_e( '24/7 enquiries', 'dubai-bobcat-rental' ); ?></strong><span><?php esc_html_e( 'Call or WhatsApp any time.', 'dubai-bobcat-rental' ); ?></span></div>
		<div><strong><?php esc_html_e( 'Operator included', 'dubai-bobcat-rental' ); ?></strong><span><?php esc_html_e( 'Bobcat is supplied with operator.', 'dubai-bobcat-rental' ); ?></span></div>
		<div><strong><?php esc_html_e( 'Fujairah HQ', 'dubai-bobcat-rental' ); ?></strong><span><?php esc_html_e( 'Free delivery near Fujairah.', 'dubai-bobcat-rental' ); ?></span></div>
		<div><strong><?php esc_html_e( 'UAE coverage', 'dubai-bobcat-rental' ); ?></strong><span><?php esc_html_e( 'Delivery quoted by location.', 'dubai-bobcat-rental' ); ?></span></div>
	</section>

	<section id="services" class="section split-section">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Primary service', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'Bobcat rental with operator for compact UAE jobsites.', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'Many buyers search for "bobcat rental", while the technical machine type is a skid steer loader. The available machine is a CAT 226B supplied with an operator for practical site work.', 'dubai-bobcat-rental' ); ?></p>
			<p><?php esc_html_e( 'The service is suited to construction, landscaping and maintenance jobs where manoeuvrability, fast loading and tight-area access matter. Minimum booking is flexible, with half-day, full-day and multi-day work discussed by location and scope.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<div class="service-list" aria-label="<?php esc_attr_e( 'Common jobs', 'dubai-bobcat-rental' ); ?>">
			<article>
				<h3><?php esc_html_e( 'Site cleaning', 'dubai-bobcat-rental' ); ?></h3>
				<p><?php esc_html_e( 'Move debris, loose material and construction waste from active or recently completed jobsites.', 'dubai-bobcat-rental' ); ?></p>
			</article>
			<article>
				<h3><?php esc_html_e( 'Loading and handling', 'dubai-bobcat-rental' ); ?></h3>
				<p><?php esc_html_e( 'Support bucket loading, unloading, short-haul material movement and practical site logistics.', 'dubai-bobcat-rental' ); ?></p>
			</article>
			<article>
				<h3><?php esc_html_e( 'Levelling and backfilling', 'dubai-bobcat-rental' ); ?></h3>
				<p><?php esc_html_e( 'Prepare compact surfaces, fill trenches and support interlock or landscaping preparation.', 'dubai-bobcat-rental' ); ?></p>
			</article>
		</div>
	</section>

	<section id="machine" class="section machine-section">
		<div class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Machine page content', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'CAT 226B skid steer loader specifications', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'This machine is backed by supplied photos and registration details. Public specs below help visitors judge job fit without exposing licence dates or private document details.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<div class="spec-grid">
			<div><span><?php esc_html_e( 'Machine type', 'dubai-bobcat-rental' ); ?></span><strong>CAT 226B</strong></div>
			<div><span><?php esc_html_e( 'Model year', 'dubai-bobcat-rental' ); ?></span><strong>2015</strong></div>
			<div><span><?php esc_html_e( 'Rated operating capacity', 'dubai-bobcat-rental' ); ?></span><strong>680 kg</strong></div>
			<div><span><?php esc_html_e( 'Operating weight', 'dubai-bobcat-rental' ); ?></span><strong>2641 kg</strong></div>
		</div>
		<div class="attachment-row" aria-label="<?php esc_attr_e( 'Attachment examples', 'dubai-bobcat-rental' ); ?>">
			<span><strong>Bucket</strong><small><?php esc_html_e( 'Loading and cleaning', 'dubai-bobcat-rental' ); ?></small></span>
			<span><strong>Forks</strong><small><?php esc_html_e( 'Pallet movement', 'dubai-bobcat-rental' ); ?></small></span>
			<span><strong>Auger</strong><small><?php esc_html_e( 'Post holes', 'dubai-bobcat-rental' ); ?></small></span>
			<span><strong>Broom</strong><small><?php esc_html_e( 'Surface sweeping', 'dubai-bobcat-rental' ); ?></small></span>
			<span><strong>Trencher</strong><small><?php esc_html_e( 'Narrow trenching', 'dubai-bobcat-rental' ); ?></small></span>
			<span><strong>Hammer</strong><small><?php esc_html_e( 'Breaking support', 'dubai-bobcat-rental' ); ?></small></span>
		</div>
	</section>

	<section class="section review-section">
		<div class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Customer feedback', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'Recent WhatsApp reviews from bobcat rental jobs.', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'Short customer notes shared after site cleaning, sand shifting, levelling and compact construction support work.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<div class="review-grid">
			<?php foreach ( $reviews as $review ) : ?>
				<article class="review-card">
					<strong>5/5</strong>
					<p><?php echo esc_html( $review ); ?></p>
					<span><?php esc_html_e( 'Customer feedback via WhatsApp', 'dubai-bobcat-rental' ); ?></span>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="section media-section">
		<div class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Real machine media', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'See the CAT 226B before booking.', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'These supplied photos and video help buyers confirm they are requesting the right compact loader before dispatch is discussed.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<div class="media-grid">
			<figure class="media-card media-card--wide">
				<img src="<?php echo esc_url( $asset_uri . 'bobcat-yard-wide.jpg' ); ?>" alt="<?php esc_attr_e( 'CAT 226B skid steer loader with bucket in Fujairah yard', 'dubai-bobcat-rental' ); ?>">
				<figcaption><?php esc_html_e( 'CAT 226B with bucket attachment', 'dubai-bobcat-rental' ); ?></figcaption>
			</figure>
			<figure class="media-card">
				<img src="<?php echo esc_url( $asset_uri . 'bobcat-phone-detail.jpg' ); ?>" alt="<?php esc_attr_e( 'Bobcat rental phone number displayed on the machine', 'dubai-bobcat-rental' ); ?>">
				<figcaption><?php esc_html_e( 'Direct booking number on machine', 'dubai-bobcat-rental' ); ?></figcaption>
			</figure>
			<figure class="media-card media-card--video">
				<video controls preload="metadata" poster="<?php echo esc_url( $asset_uri . 'bobcat-video-poster.jpg' ); ?>">
					<source src="<?php echo esc_url( $asset_uri . 'bobcat-walkaround.mp4' ); ?>" type="video/mp4">
				</video>
				<figcaption><?php esc_html_e( 'Short machine walkaround video', 'dubai-bobcat-rental' ); ?></figcaption>
			</figure>
		</div>
	</section>

	<section class="section quote-section" id="quote">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Get availability', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'Send the job details for a fast quote.', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'Include location, date, duration, work type and attachment requirement. Operator is provided with the bobcat. Delivery is free near Fujairah and quoted for other UAE locations.', 'dubai-bobcat-rental' ); ?></p>
			<ul class="check-list">
				<li><?php esc_html_e( '24/7 enquiry path through call and WhatsApp', 'dubai-bobcat-rental' ); ?></li>
				<li><?php esc_html_e( 'Built for contractors, supervisors and landscaping teams', 'dubai-bobcat-rental' ); ?></li>
				<li><?php esc_html_e( 'Flexible minimum booking: half-day, full-day or longer work by scope', 'dubai-bobcat-rental' ); ?></li>
			</ul>
		</div>
		<form class="quote-form" name="quote" aria-label="<?php esc_attr_e( 'Quote request form', 'dubai-bobcat-rental' ); ?>">
			<label><?php esc_html_e( 'Name', 'dubai-bobcat-rental' ); ?> <input name="name" autocomplete="name" required></label>
			<label><?php esc_html_e( 'Phone', 'dubai-bobcat-rental' ); ?> <input name="phone" autocomplete="tel" required></label>
			<label><?php esc_html_e( 'WhatsApp', 'dubai-bobcat-rental' ); ?> <input name="whatsapp" autocomplete="tel"></label>
			<label><?php esc_html_e( 'Job location', 'dubai-bobcat-rental' ); ?> <input name="location" placeholder="Fujairah, Dubai, Sharjah..." required></label>
			<label><?php esc_html_e( 'Service needed', 'dubai-bobcat-rental' ); ?>
				<select name="service" required>
					<option value=""><?php esc_html_e( 'Choose service', 'dubai-bobcat-rental' ); ?></option>
					<option><?php esc_html_e( 'Site cleaning', 'dubai-bobcat-rental' ); ?></option>
					<option><?php esc_html_e( 'Loading and material handling', 'dubai-bobcat-rental' ); ?></option>
					<option><?php esc_html_e( 'Levelling or grading', 'dubai-bobcat-rental' ); ?></option>
					<option><?php esc_html_e( 'Backfilling', 'dubai-bobcat-rental' ); ?></option>
					<option><?php esc_html_e( 'Other skid steer work', 'dubai-bobcat-rental' ); ?></option>
				</select>
			</label>
			<label><?php esc_html_e( 'Date needed', 'dubai-bobcat-rental' ); ?> <input type="date" name="date"></label>
			<label><?php esc_html_e( 'Operator included', 'dubai-bobcat-rental' ); ?>
				<select name="operator">
					<option><?php esc_html_e( 'Yes, operator required', 'dubai-bobcat-rental' ); ?></option>
					<option><?php esc_html_e( 'Discuss details', 'dubai-bobcat-rental' ); ?></option>
				</select>
			</label>
			<label><?php esc_html_e( 'Attachment required', 'dubai-bobcat-rental' ); ?> <input name="attachment" placeholder="Bucket, forks, auger..."></label>
			<label class="full"><?php esc_html_e( 'Message', 'dubai-bobcat-rental' ); ?> <textarea name="message" rows="4"></textarea></label>
			<button class="button primary full" type="submit"><?php esc_html_e( 'Send Job Details on WhatsApp', 'dubai-bobcat-rental' ); ?></button>
			<p class="form-note" role="status" aria-live="polite"></p>
		</form>
	</section>

	<section id="areas" class="section areas-section">
		<div class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Service areas', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'Real service areas across the UAE.', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'Headquarters are in Dibba, Fujairah. Delivery is free near Fujairah, while other emirates are quoted by distance, timing and job duration.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<div class="area-grid">
			<article><h3>Fujairah & Dibba</h3><p><?php esc_html_e( 'Nearest dispatch area with free local delivery discussion.', 'dubai-bobcat-rental' ); ?></p></article>
			<article><h3>Dubai & Sharjah</h3><p><?php esc_html_e( 'Common contractor and site-cleaning demand areas.', 'dubai-bobcat-rental' ); ?></p></article>
			<article><h3>Ajman & RAK</h3><p><?php esc_html_e( 'Available by job location, timing and duration.', 'dubai-bobcat-rental' ); ?></p></article>
			<article><h3>Abu Dhabi & Al Ain</h3><p><?php esc_html_e( 'Long-distance delivery quoted after job details are shared.', 'dubai-bobcat-rental' ); ?></p></article>
		</div>
	</section>

	<section class="section guide-section">
		<div class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Rental guides', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'Build trust before the quote.', 'dubai-bobcat-rental' ); ?></h2>
			<p><?php esc_html_e( 'Support content helps buyers understand machine fit, price factors, operator requirements and common Dubai jobsite use cases.', 'dubai-bobcat-rental' ); ?></p>
		</div>
		<div class="post-grid">
			<?php
			$home_guides = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 3,
					'no_found_rows'  => true,
				)
			);
			while ( $home_guides->have_posts() ) :
				$home_guides->the_post();
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
		<a class="button light guide-section__link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'View all guides', 'dubai-bobcat-rental' ); ?></a>
	</section>

	<section id="faq" class="section faq-section">
		<div class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Buyer questions', 'dubai-bobcat-rental' ); ?></p>
			<h2><?php esc_html_e( 'Bobcat rental FAQs', 'dubai-bobcat-rental' ); ?></h2>
		</div>
		<div class="faq-list">
			<details><summary><?php esc_html_e( 'Is this a Bobcat or a skid steer loader?', 'dubai-bobcat-rental' ); ?></summary><p><?php esc_html_e( 'Customers often use "bobcat" as a general rental term. The available machine is a CAT 226B skid steer loader.', 'dubai-bobcat-rental' ); ?></p></details>
			<details><summary><?php esc_html_e( 'Can I hire it with an operator?', 'dubai-bobcat-rental' ); ?></summary><p><?php esc_html_e( 'Yes. The bobcat is provided with an operator for rental jobs.', 'dubai-bobcat-rental' ); ?></p></details>
			<details><summary><?php esc_html_e( 'What information is needed for a quote?', 'dubai-bobcat-rental' ); ?></summary><p><?php esc_html_e( 'Send job location, work type, date, expected duration and any attachment requirement. Photos or a map pin help speed up the quote.', 'dubai-bobcat-rental' ); ?></p></details>
			<details><summary><?php esc_html_e( 'Which attachments are possible?', 'dubai-bobcat-rental' ); ?></summary><p><?php esc_html_e( 'Common skid steer tools include buckets, forks, augers, brooms, trenchers and hammers, depending on actual availability.', 'dubai-bobcat-rental' ); ?></p></details>
			<details><summary><?php esc_html_e( 'Is delivery free?', 'dubai-bobcat-rental' ); ?></summary><p><?php esc_html_e( 'Delivery can be free near Fujairah. For Dubai, Sharjah, Abu Dhabi and other UAE locations, delivery cost is discussed after the job location and duration are known.', 'dubai-bobcat-rental' ); ?></p></details>
			<details><summary><?php esc_html_e( 'What is the minimum booking duration?', 'dubai-bobcat-rental' ); ?></summary><p><?php esc_html_e( 'Standard jobs are usually discussed as half-day, full-day or multi-day bookings. If the job is small, send details and we will confirm what is practical.', 'dubai-bobcat-rental' ); ?></p></details>
		</div>
	</section>
</main>

<?php
get_footer();
