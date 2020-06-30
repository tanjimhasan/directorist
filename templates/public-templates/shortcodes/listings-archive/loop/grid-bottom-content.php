<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

 global $listings;

if ( $listings->get_the_option( 'display_category' )  || $listings->get_the_option( 'display_view_count' ) ) { ?>
	<div class="atbd_listing_bottom_content">
		<?php if ( $listings->get_the_option( 'display_category' ) ): ?>
			<div class="atbd_content_left">
				<?php $listings->categories_template();?> 
			</div>
		<?php endif; ?>

		<?php $listings->grid_footer_right_template();?> 
	</div>
	<?php
}