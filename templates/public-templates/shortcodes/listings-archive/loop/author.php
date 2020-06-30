<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

  global $listing_loop;
?>
<a 
	href="<?php echo esc_url( $listing_loop->get_the_prop( 'data', 'author_link' ) ); ?>" 
	aria-label="<?php echo esc_attr( $listing_loop->get_the_prop( 'data', 'author_full_name' ) ); ?>" 
	class="<?php echo esc_attr( $listing_loop->get_the_prop( 'data', 'author_link_class' ) ); ?>">
		<?php if ( $listing_loop->get_the_prop('data', 'u_pro_pic') ) { ?>
			<img src="<?php echo esc_url( $listing_loop->get_the_prop('data', 'u_pro_pic', ['index' => 0]) ); ?>" alt="<?php esc_attr_e( 'Author Image', 'directorist' );?>">
			<?php
		}
		else {
			$listing_loop->the_prop( 'data', 'avatar_img' );
		}
		?>
</a>