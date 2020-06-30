<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

 global $listings;

if ( $listings->get_the_option( 'display_view_count' ) ) { ?>
	<ul class="atbd_content_right">
		<li class="atbd_count"><?php $listings->loop_view_count_template(); ?></li>
	</ul>
	<?php
}