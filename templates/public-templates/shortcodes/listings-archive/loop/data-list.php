<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

 global $listings, $listing_loop;
?>
<div class="atbd_listing_data_list">
	<ul>
		<?php
		/**
		 * @since 4.7.6
		 */
		do_action('atbdp_listings_before_location');
		?>

		<?php if ( $listings->get_the_option( 'display_contact_info' ) ): ?>
			<?php if ( $listings->display_address_field() ) : ?>
				<li>
					<p>
						<span class="<?php atbdp_icon_type(true); ?>-map-marker"></span>
						<?php echo esc_html( $listing_loop->get_the_data( 'address' ) ); ?>
					</p>
				</li>
			<?php elseif ( ! empty( $listing_loop->get_the_data( 'locs' ) ) && 'location' === $listings->get_the_option( 'address_location' ) ): ?>
				<li>
					<p>
						<span class="<?php atbdp_icon_type(true); ?>-map-marker"></span>
						<?php echo $listings->get_address_from_locaton(); ?></span>
					</p>
				</li>
			<?php endif; ?>

			<?php
			/**
			* @since 4.7.6
			*/
			do_action('atbdp_listings_before_phone');
			?>

			<?php if ( ! empty( $listing_loop->get_the_data( 'phone_number' ) ) && $listings->get_the_option( 'display_phone_field' ) ): ?>
			<li>
				<p>
					<span class="<?php atbdp_icon_type(true); ?>-phone"></span>
					<a href="tel:<?php ATBDP_Helper::sanitize_tel_attr( $listing_loop->get_the_data( 'phone_number' ) ); ?>">
						<?php ATBDP_Helper::sanitize_html( $listing_loop->get_the_data( 'phone_number' ) ); ?>
					</a>
				</p>
			</li>
			<?php endif; ?>

		<?php endif; ?>
		<?php
		/**
		 * @since 4.7.6
		 */
		do_action('atbdp_listings_before_post_date');
		?>

		<?php if ( $listings->get_the_option( 'display_publish_date' ) ): ?>
		<li>
			<p>
				<span class="<?php atbdp_icon_type(true); ?>-clock-o"></span>
				<?php echo esc_html( $listings->get_published_date() );?>
			</p>
		</li>
		<?php endif; ?>

		<?php
		/**
		 * @since 4.7.6
		 */
		do_action('atbdp_listings_after_post_date');
		?>

		<?php if ( $listings->display_email() ): ?>
			<li>
				<p>
					<span class="<?php echo atbdp_icon_type();?>-envelope"></span>
					<a target="_top" href="mailto:<?php echo $listing_loop->get_the_data( 'email' );?>">
						<?php echo $listing_loop->get_the_data( 'email' );?>
					</a>
				</p>
			</li>
		<?php endif; ?> 

		<?php if ( ! empty( $listing_loop->get_the_data( 'web' ) && $listing_loop->get_the_data( 'display_web_link' ) )): ?>
			<li>
				<p>
					<span class="<?php atbdp_icon_type(true); ?>-globe"></span>
					<a target="_blank" href="<?php echo esc_url( $listing_loop->get_the_data( 'web' ) ); ?>">
						<?php echo esc_html( $listing_loop->get_the_data( 'web' ) ); ?>
					</a>
				</p>
			</li>
		<?php endif; ?>

	    <?php
	    /**
	     * @since 7.0
	     */
	    do_action( 'directorist_loop_data_list_end', $listings );
	    ?>
	</ul>
</div>