<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */
global $listings, $listing_loop;

if ( ! $listings->get_the_prop( 'options', 'disable_single_listing' ) ) { ?>
	<a href="<?php echo esc_url( $listing_loop->get_the_prop('data', 'permalink') ); ?>" <?php $listing_loop->link_attr(); ?>>
		<?php atbdp_thumbnail_card(); ?>
	</a>
	<?php
}
else {
	atbdp_thumbnail_card();
}