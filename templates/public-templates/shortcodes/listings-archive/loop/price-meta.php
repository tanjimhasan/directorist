<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

  global $listings, $listing_loop;
?>
<div class="atbd_listing_meta">

	<?php if ( $listings->get_the_option( 'display_review' ) ): ?>
		<span class="atbd_meta atbd_listing_rating">
			<?php echo esc_html( $listing_loop->get_the_data('average') ) ;?>
			<i class="<?php atbdp_icon_type(true);?>-star"></i>
		</span>
	<?php endif ?>
	
	<?php
	if ( $listings->get_the_option('display_price') && $listings->get_the_option('display_pricing_field' ) ) {
		if ( ! empty( $listing_loop->get_the_data('price_range') ) && ('range' === $listing_loop->get_the_data('atbd_listing_pricing') )) {
			echo $listings->display_price_range();
		}
		else {
			echo apply_filters('atbdp_listing_card_price', $listings->display_price() );
		}
	}

	do_action('atbdp_after_listing_price');

    /**
     * @since 7.0
     * @hooked Directorist_Template_Hooks::list_view_business_hours - 10
     */
    do_action( "directorist_". $listings->get_the_attr( 'view' ) ."_view_listing_meta_end", $listings );
	?>
</div>