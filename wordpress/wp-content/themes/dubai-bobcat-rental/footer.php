<?php
/**
 * Site footer.
 */
?>
<footer class="site-footer">
	<div class="footer-brand">
		<strong><?php echo esc_html( dbr_get_business_value( 'business_name', 'UAE Equipment Rental' ) ); ?></strong>
		<p><?php esc_html_e( 'CAT 226B bobcat and skid steer loader rental with operator for site cleaning, loading, levelling, backfilling and compact material handling across the UAE.', 'dubai-bobcat-rental' ); ?></p>
		<p class="footer-note"><?php echo esc_html( dbr_get_business_value( 'address', 'Headquarters: Dibba, Fujairah, United Arab Emirates' ) ); ?> · <?php echo esc_html( dbr_get_business_value( 'hours', '24/7' ) ); ?></p>
	</div>
	<div class="footer-columns">
		<div>
			<strong><?php esc_html_e( 'Rental Pages', 'dubai-bobcat-rental' ); ?></strong>
			<a href="<?php echo esc_url( dbr_page_url( 'bobcat-rental-dubai', '/services/bobcat-rental-dubai/' ) ); ?>"><?php esc_html_e( 'Bobcat Rental Dubai', 'dubai-bobcat-rental' ); ?></a>
			<a href="<?php echo esc_url( dbr_page_url( 'cat-226b-skid-steer-loader', '/machines/cat-226b-skid-steer-loader/' ) ); ?>"><?php esc_html_e( 'CAT 226B Specs', 'dubai-bobcat-rental' ); ?></a>
			<a href="<?php echo esc_url( dbr_page_url( 'service-areas', '/service-areas/' ) ); ?>"><?php esc_html_e( 'UAE Service Areas', 'dubai-bobcat-rental' ); ?></a>
		</div>
		<div>
			<strong><?php esc_html_e( 'Contact', 'dubai-bobcat-rental' ); ?></strong>
			<a href="<?php echo esc_url( dbr_page_url( 'contact', '/contact/' ) ); ?>"><?php esc_html_e( 'Quote Form', 'dubai-bobcat-rental' ); ?></a>
			<a href="<?php echo esc_url( dbr_phone_href() ); ?>"><?php esc_html_e( 'Call', 'dubai-bobcat-rental' ); ?></a>
			<a href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp', 'dubai-bobcat-rental' ); ?></a>
		</div>
	</div>
</footer>

<div class="mobile-cta" aria-label="<?php esc_attr_e( 'Mobile contact actions', 'dubai-bobcat-rental' ); ?>">
	<a href="<?php echo esc_url( dbr_phone_href() ); ?>"><?php esc_html_e( 'Call', 'dubai-bobcat-rental' ); ?></a>
	<a href="<?php echo esc_url( dbr_whatsapp_href() ); ?>"><?php esc_html_e( 'WhatsApp', 'dubai-bobcat-rental' ); ?></a>
</div>

<?php wp_footer(); ?>
</body>
</html>
