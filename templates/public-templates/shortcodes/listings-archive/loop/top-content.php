<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

  global $listings, $listing_loop;
?>

<div class="atbd_content_upper">

	<?php do_action( "atbdp_". $listings->get_the_attr( 'view' ) ."_view_before_title" );?>

	<?php if ( $listings->get_the_option( 'display_title' ) ) { ?>
	<h4 class="atbd_listing_title">
		<?php echo wp_kses_post( $listings->loop_get_title() );?>
	</h4>
	<?php }

	/**
	 * @since 6.2.3
	 */
	do_action( "atbdp_". $listings->get_the_attr( 'view' ) ."_view_after_title" );

	if ( $listings->show_listing_tagline() ) { ?>
		<p class="atbd_listing_tagline">
			<?php echo esc_html(stripslashes( $listing_loop->get_the_data( 'tagline' ) )); ?>
		</p>
	<?php }

	/**
	 * Fires after the title and sub title of the listing is rendered
	 *
	 *
	 * @since 1.0.0
	 */

	do_action('atbdp_after_listing_tagline');

	if ( $listings->show_price_meta() ) {
		$listings->price_meta_template();
	}

	if ( $listings->display_contact_info() ) {
		$listings->data_list_template();
	}

	if ( $listings->display_excerpt() ) { ?>
		<p class="atbd_excerpt_content">
			<?php echo esc_html( wp_trim_words( $listing_loop->get_the_data('excerpt'), $listings->get_the_option( 'excerpt_limit' ) ));

			/**
			* @since 5.0.9
			*/
			do_action('atbdp_listings_after_exerpt');

			if ( $listings->get_the_option( 'display_readmore' ) ) { 
				printf('<a href="%s"> %s</a>', $listing_loop->get_the_data( 'permalink' ), $listings->get_the_option( 'readmore_text' ));
			}
			?>
		</p>
		<?php
	}

	/**
	 * @since 7.0
	 * @hooked Directorist_Template_Hooks::mark_as_favourite_button - 10
	 */
	do_action( "directorist_". $listings->get_the_attr( 'view' ) . "_view_top_content_end", $listings );
	?>
</div>