<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

  global $listing_loop;
?>
<span class="<?php atbdp_icon_type(true) ?>-eye"></span><?php echo ( ! empty( $listing_loop->get_the_data( 'post_view' ) ) ) ? $listings->loop['post_view'] : 0;