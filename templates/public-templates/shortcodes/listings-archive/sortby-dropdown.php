<?php
/**
 * @author  AazzTech
 * @since   6.6
 * @version 6.6
 */
?>
<div class="atbd_dropdown">
	<a class="atbd_dropdown-toggle" href="#" id="sortByDropdownMenuLink"><?php echo esc_html( $listings->sort_by_text ); ?><span class="atbd_drop-caret"></span></a>
	<div class="atbd_dropdown-menu atbd_dropdown-menu--lg" aria-labelledby="sortByDropdownMenuLink">
		<?php foreach ($listings->get_sort_by_link_list() as $key => $value) { ?>
			<a class="atbd_dropdown-item<?php echo esc_attr($value['active_class']);?>" href="<?php echo esc_attr($value['link']);?>"><?php echo esc_html($value['label']);?></a>
		<?php } ?>
	</div>
</div>