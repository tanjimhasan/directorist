<?php

/**
 * @author  AazzTech
 * @since   6.6
 * @version 6.6
 */
?>
<div class="single_search_field search_category">
	<select name="in_cat" id="<?php echo esc_attr($searchform->category_id); ?>" class="<?php echo esc_attr($searchform->category_class); ?>"<?php echo esc_attr($searchform->cat_required_text); ?>>
		<option value=""><?php echo $searchform->category_placeholder; ?></option>
		<?php echo $searchform->categories_fields; ?>
	</select>
</div>