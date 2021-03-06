<?php
/**
 * @author  AazzTech
 * @since   6.6
 * @version 6.6
 */
?>
<div class="atbd_listing_meta">

	<?php if ($listings->display_review): ?>
		<span class="atbd_meta atbd_listing_rating"><?php echo esc_html( ATBDP()->review->get_average($listings->loop['id']) );?><i class="<?php atbdp_icon_type(true);?>-star"></i></span>
	<?php endif ?>
	
	<?php
	if ($listings->display_price && $listings->display_pricing_field) {
		if (!empty($listings->loop['price_range']) && ('range' === $listings->loop['atbd_listing_pricing'])) {
			echo atbdp_display_price_range($listings->loop['price_range']);
		}
		else {
			echo apply_filters('atbdp_listing_card_price', atbdp_display_price($listings->loop['price'], $listings->is_disable_price, $currency = null, $symbol = null, $c_position = null, $echo = false));
		}
	}

	do_action('atbdp_after_listing_price');

    /**
     * @since 6.6
     * @hooked Directorist_Listings::list_view_business_hours - 10
     */
    do_action( "directorist_{$listings->view}_view_listing_meta_end", $listings );
	?>
</div>