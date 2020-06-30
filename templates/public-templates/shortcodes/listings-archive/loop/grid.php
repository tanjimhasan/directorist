<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */
global $listings, $listing_loop;
?>
<div class="atbdp_column atbdp-col-<?php echo esc_attr( $listings->get_the_attr( 'columns' ) ); ?>">
	<div class="atbd_single_listing atbd_listing_card">
		<article class="atbd_single_listing_wrapper <?php echo esc_attr( $listing_loop->wrapper_class() ); ?>">
			
			<?php if ( $listings->get_the_attr( 'display_preview_image' ) ): ?>
				<figure class="atbd_listing_thumbnail_area">
					<?php $listings->grid_thumbnail_template();?>
				</figure>
			<?php endif; ?>

			<div class="atbd_listing_info">
				<?php
				$listings->top_content_template();
				$listings->loop_grid_bottom_content_template();
				?>
			</div>
			
		</article>
	</div>
</div>