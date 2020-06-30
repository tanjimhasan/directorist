<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

 global $listings, $search_form;
?>
<div class="ads-advanced">
	<form action="<?php atbdp_search_result_page_link(); ?>" class="atbd_ads-form">
		<div class="atbd_seach_fields_wrapper" style="border: none;">
			<div class="row atbdp-search-form">
				<?php if ($search_form->has_search_text_field) { ?>
					<div class="col-md-6 col-sm-12 col-lg-4">
						<?php $search_form->search_text_template();?>
					</div>
				<?php
				}

				if ($search_form->has_category_field) {
					?>
					<div class="col-md-6 col-sm-12 col-lg-4">
						<?php $search_form->category_template();?>
					</div>
				<?php
				}

				if ($listings->location_field_type('listing_location')) {
				?>
					<div class="col-md-12 col-sm-12 col-lg-4">
						<div class="single_search_field search_location">
							<select name="in_loc" id="loc-type" class="form-control directory_field bdas-category-location">
								<option><?php echo $listings->location_placeholder; ?></option>
								<?php echo $listings->locations_fields; ?>
							</select>
						</div>
					</div>
				<?php
				}

				if ( ! $listings->location_field_type('listing_location' ) ) {
					$geodata = $listings->geolocation_field_data();
				?>
					<div class="col-md-6 col-sm-12 col-lg-4">
						<div class="atbdp_map_address_field">
							<div class="atbdp_get_address_field">
								<input type="text" name="address" id="address" value="<?php echo esc_attr( $geodata['value'] ); ?>" placeholder="<?php echo esc_attr($geodata['placeholder'] ); ?>" autocomplete="off" class="form-control location-name">
								<?php echo $geodata['geo_loc']; ?>
							</div>
							<div class="address_result" style="display: none">
							</div>
							<input type="hidden" id="cityLat" name="cityLat" value="<?php echo esc_attr($geodata['cityLat']); ?>" />
							<input type="hidden" id="cityLng" name="cityLng" value="<?php echo esc_attr($geodata['cityLng']); ?>" />
						</div>
					</div>
				<?php
				}
				/**
				 * @since 5.0
				 */
				do_action('atbdp_search_field_after_location');
				?>
			</div>
		</div>

		<?php
		$search_form->price_range_template();
		$search_form->rating_template();
		$search_form->radius_search_template();
		$search_form->open_now_template();
		$search_form->tag_template();
		$search_form->custom_fields_template();
		$search_form->information_template();
		$search_form->buttons_template();
		?>
	</form>
</div>