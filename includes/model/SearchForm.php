<?php
/**
 * @author AazzTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Directorist_Listing_Search_Form {

	// Search Shortcode
	public $options = [];
	public $type = '';

	public $atts;
	public $defaults;
	public $params;

	public $show_title_subtitle;
	public $has_search_button;
	public $has_more_filters_button;
	public $logged_in_user_only;
	public $redirect_page_url;
	public $search_bar_title;
	public $search_bar_sub_title;
	public $search_button_text;
	public $more_filters_text;
	public $more_filters_display;
	public $show_connector;
	public $connectors_title;
	public $popular_cat_title;
	public $popular_cat_num;
	public $show_popular_category;

	// Common - Search Shortcode and Listing Header
	public $has_search_text_field;
	public $has_category_field;
	public $has_location_field;
	public $has_price_field;
	public $has_price_range_field;
	public $has_rating_field;
	public $has_radius_search;
	public $has_tag_field;
	public $has_custom_fields;
	public $has_website_field;
	public $has_email_field;
	public $has_phone_field;
	public $has_fax_field;
	public $has_address_field;
	public $has_zip_code_field;
	public $has_reset_filters_button;
	public $has_apply_filters_button;
	public $reset_filters_text;
	public $apply_filters_text;
	public $has_open_now_field;

	public $c_symbol;
	public $tag_label;
	public $website_label;
	public $email_label;
	public $fax_label;
	public $address_label;
	public $zip_label;
	public $default_radius_distance;
	public $tag_terms;
	public $search_text_placeholder;
	public $category_placeholder;
	public $location_placeholder;
	public $search_required_text;
	public $cat_required_text;
	public $loc_required_text;
	public $categories_fields;
	public $locations_fields;
	public $category_id;
	public $category_class;
	public $location_id;
	public $location_class;
	public $location_source;
	public $select_listing_map;

	public function __construct( $type, $atts = array() ) {
		$this->type = $type;
		$this->atts = $atts;

		$this->set_default_options();

		if ( $type == 'search_form' ) {
			$this->update_options_for_search_form();
			$this->prepare_search_data($atts);
		}

		if ( $type == 'search' ) {
			$this->update_options_for_search_result_page();
			$this->prepare_search_data($atts);
		}

		if ( $type == 'listing' ) {
			$this->prepare_listing_data($atts);
		}

		$this->c_symbol           = atbdp_currency_symbol( get_directorist_option( 'g_currency', 'USD' ) );
		$this->tag_label          = get_directorist_option( 'tag_label', __( 'Tag', 'directorist' ) );
		$this->website_label      = get_directorist_option( 'website_label', __( 'Website', 'directorist' ) );
		$this->email_label        = get_directorist_option( 'email_label', __( 'Email', 'directorist' ) );
		$this->fax_label          = get_directorist_option( 'fax_label', __( 'Fax', 'directorist' ) );
		$this->address_label      = get_directorist_option( 'address_label', __( 'Address', 'directorist' ) );
		$this->zip_label          = get_directorist_option( 'zip_label', __( 'Zip', 'directorist' ) );
		$this->categories_fields  = search_category_location_filter( $this->search_category_location_args(), ATBDP_CATEGORY );
		$this->locations_fields   = search_category_location_filter( $this->search_category_location_args(), ATBDP_LOCATION );
		$this->select_listing_map = get_directorist_option( 'select_listing_map', 'google' );
	}
	
	// set_default_options
	public function set_default_options() {
		$this->options['more_filters_fields']     = get_directorist_option( 'listing_filters_fields', array( 'search_text', 'search_category', 'search_location', 'search_price', 'search_price_range', 'search_rating', 'search_tag', 'search_custom_fields', 'radius_search' ) );
		$this->options['search_fields']           = get_directorist_option('search_tsc_fields', array('search_text', 'search_category', 'search_location'));
		$this->options['search_filters']          = get_directorist_option('listings_filters_button', array('search_reset_filters', 'search_apply_filters'));
		$this->options['location_address']        = get_directorist_option( 'listing_location_address', 'map_api' );
		$this->options['search_listing_text']     = get_directorist_option('search_listing_text', __('Search Listing', 'directorist'));
		$this->options['search_more_filter']      = !empty( get_directorist_option( 'search_more_filter', 1 ) ) ? 'yes' : '';
		$this->options['search_more_filters']     = get_directorist_option('search_more_filters', __('More Filters', 'directorist'));
		$this->options['search_button']           = !empty( get_directorist_option( 'search_button', 1 ) ) ? 'yes' : '';
		$this->options['radius_distance']         = get_directorist_option( 'listing_default_radius_distance', 0 );
		$this->options['require_search_text']     = !empty(get_directorist_option('require_search_text')) ? ' required' : '';
		$this->options['require_search_category'] = !empty(get_directorist_option('require_search_category')) ? ' required' : '';
		$this->options['require_search_location'] = !empty(get_directorist_option('require_search_location')) ? ' required' : '';
		$this->options['search_placeholder']      = get_directorist_option('listings_search_text_placeholder', __('What are you looking for?', 'directorist'));
		$this->options['filters_buttons']         = get_directorist_option( 'listings_filters_button', array( 'reset_button', 'apply_button' ) );
		
		$this->options['more_filters_button']        = get_directorist_option( 'listing_filters_button', 1 );
		$this->options['display_more_filter_icon']   = get_directorist_option('listing_filters_icon', 1);
		$this->options['display_search_button_icon'] = get_directorist_option('listing_filters_icon', 1);
		$this->options['open_filter_fields']         = get_directorist_option('listings_display_filter', 'overlapping');
		
		$this->options['reset_filters_text']      = get_directorist_option('listings_reset_text', __('Reset Filters', 'directorist'));
		$this->options['apply_filters_text']      = get_directorist_option( 'listings_apply_text', __( 'Apply Filters', 'directorist' ) );
		$this->options['search_text_placeholder'] = get_directorist_option( 'listings_search_text_placeholder', __( 'What are you looking for?', 'directorist' ) );
		$this->options['category_placeholder']    = get_directorist_option( 'listings_category_placeholder', __( 'Select a category', 'directorist' ) );
		$this->options['location_placeholder']    = get_directorist_option( 'listings_location_placeholder', __( 'Select a location', 'directorist' ) );
	}

	// update_options_for_search_result_page
	public function update_options_for_search_result_page() {
		$this->options['more_filters_fields'] = get_directorist_option('search_result_filters_fields', array('search_price', 'search_price_range', 'search_rating', 'search_tag', 'search_custom_fields', 'radius_search'));
		$this->options['location_address']    = get_directorist_option('sresult_location_address', 'address');
		$this->options['search_filters']      = get_directorist_option('search_result_filters_button', array('search_reset_filters', 'search_apply_filters'));

		$this->options['more_filters_button'] = get_directorist_option( 'search_result_filters_button_display', 1 );
		$this->options['radius_distance']     = get_directorist_option( 'sresult_default_radius_distance', 0 );

		$this->options['reset_filters_text']      = get_directorist_option('sresult_reset_text', __('Reset Filters', 'directorist'));
		$this->options['apply_filters_text']      = get_directorist_option( 'sresult_apply_text', __( 'Apply Filters', 'directorist' ) );
		$this->options['search_text_placeholder'] = get_directorist_option( 'search_result_search_text_placeholder', __( 'What are you looking for?', 'directorist' ) );
		$this->options['category_placeholder']    = get_directorist_option( 'search_result_category_placeholder', __( 'Select a category', 'directorist' ) );
		$this->options['location_placeholder']    = get_directorist_option( 'search_result_location_placeholder', __( 'Select a location', 'directorist' ) );
	}

	// update_options_for_search_form
	public function update_options_for_search_form() {
		$this->options['more_filters_fields'] = get_directorist_option('search_more_filters_fields', array( 'search_price', 'search_price_range', 'search_rating', 'search_tag', 'search_custom_fields', 'radius_search'));
		$this->options['location_address']    = get_directorist_option('search_location_address', 'address');
		
		$this->options['search_filters']             = get_directorist_option('search_filters', array('search_reset_filters', 'search_apply_filters'));
		$this->options['more_filters_button']        = get_directorist_option( 'search_more_filter', 1 );
		$this->options['display_more_filter_icon']   = get_directorist_option('search_more_filter_icon', 1);
		$this->options['display_search_button_icon'] = get_directorist_option('search_button_icon', 1);
		$this->options['open_filter_fields']         = get_directorist_option('home_display_filter', 'overlapping');
		$this->options['radius_distance']            = get_directorist_option('search_default_radius_distance', 0);
		
		$this->options['reset_filters_text']      = get_directorist_option( 'search_reset_text', __('Reset Filters', 'directorist'));
		$this->options['apply_filters_text']      = get_directorist_option( 'search_apply_filter', __( 'Apply Filters', 'directorist' ) );
		$this->options['search_text_placeholder'] = get_directorist_option( 'search_placeholder', __( 'What are you looking for?', 'directorist' ) );
		$this->options['category_placeholder']    = get_directorist_option( 'search_category_placeholder', __( 'Select a category', 'directorist' ) );
		$this->options['location_placeholder']    = get_directorist_option( 'search_location_placeholder', __( 'Select a location', 'directorist' ) );
	}

	// prepare_search_data
	public function prepare_search_data($atts) {
		$search_more_filters_fields = $this->options['more_filters_fields'];
		$search_filters             = $this->options['search_filters'];
		$search_location_address    = $this->options['location_address'];

		$search_fields        = $search_more_filters_fields;
		$reset_filters_button = in_array('reset_button', $search_filters) ? 'yes' : '';
		$apply_filters_button = in_array('apply_button', $search_filters) ? 'yes' : '';

		if ( 'search_form' === $this->type ) {
			$search_fields = $this->options['search_fields'];
			$reset_filters_button = in_array('search_reset_filters', $search_filters) ? 'yes' : '';
			$apply_filters_button = in_array('search_apply_filters', $search_filters) ? 'yes' : '';
		}

		$this->defaults = array(
			'show_title_subtitle'    => 'yes',
			'search_bar_title'       => get_directorist_option('search_title', __("Search here", 'directorist')),
			'search_bar_sub_title'   => get_directorist_option('search_subtitle', __("Find the best match of your interest", 'directorist')),
			'text_field'             => in_array('search_text', $search_fields) ? 'yes' : '',
			'category_field'         => in_array('search_category', $search_fields) ? 'yes' : '',
			'location_field'         => in_array('search_location', $search_fields) ? 'yes' : '',
			'search_button'          => $this->options['search_button'],
			'search_button_text'     => $this->options['search_listing_text'],
			'more_filters_button'    => ( $this->options['more_filters_button'] ) ? 'yes' : '',
			'more_filters_text'      => $this->options['search_more_filters'],
			'price_min_max_field'    => in_array('search_price', $search_more_filters_fields) ? 'yes' : '',
			'price_range_field'      => in_array('search_price_range', $search_more_filters_fields) ? 'yes' : '',
			'rating_field'           => in_array('search_rating', $search_more_filters_fields) ? 'yes' : '',
			'tag_field'              => in_array('search_tag', $search_more_filters_fields) ? 'yes' : '',
			'open_now_field'         => in_array('search_open_now', $search_more_filters_fields) ? 'yes' : '',
			'custom_fields'          => in_array('search_custom_fields', $search_more_filters_fields) ? 'yes' : '',
			'website_field'          => in_array('search_website', $search_more_filters_fields) ? 'yes' : '',
			'email_field'            => in_array('search_email', $search_more_filters_fields) ? 'yes' : '',
			'phone_field'            => in_array('search_phone', $search_more_filters_fields) ? 'yes' : '',
			'fax'                    => in_array('search_fax', $search_more_filters_fields) ? 'yes' : '',
			'address_field'          => in_array('search_address', $search_more_filters_fields) ? 'yes' : '',
			'zip_code_field'         => in_array('search_zip_code', $search_more_filters_fields) ? 'yes' : '',
			'radius_search'          => in_array('radius_search', $search_more_filters_fields) ? 'yes' : '',
			'reset_filters_button'   => $reset_filters_button,
			'apply_filters_button'   => $apply_filters_button,
			'reset_filters_text'     => $this->options['reset_filters_text'],
			'apply_filters_text'     => $this->options['apply_filters_text'],
			'logged_in_user_only'    => '',
			'redirect_page_url'      => '',
			'more_filters_display'   => $this->options['open_filter_fields'],
		);

		$this->params = shortcode_atts( $this->defaults, $this->atts );

		$this->show_title_subtitle      = $this->params['show_title_subtitle'] == 'yes' ? true : false;
		$this->has_search_text_field    = $this->params['text_field'] == 'yes' ? true : false;
		$this->has_category_field       = $this->params['category_field'] == 'yes' ? true : false;
		$this->has_location_field       = $this->params['location_field'] == 'yes' ? true : false;
		$this->has_search_button        = $this->params['search_button'] == 'yes' ? true : false;
		$this->has_more_filters_button  = $this->params['more_filters_button'] == 'yes' ? true : false;
		$this->has_price_field          = $this->params['price_min_max_field'] == 'yes' ? true : false;
		$this->has_price_range_field    = $this->params['price_range_field'] == 'yes' ? true : false;
		$this->has_rating_field         = $this->params['rating_field'] == 'yes' ? true : false;
		$this->has_tag_field            = $this->params['tag_field'] == 'yes' ? true : false;
		$this->has_open_now_field       = $this->params['open_now_field'] == 'yes' ? true : false;
		$this->has_custom_fields        = $this->params['custom_fields'] == 'yes' ? true : false;
		$this->has_website_field        = $this->params['website_field'] == 'yes' ? true : false;
		$this->has_email_field          = $this->params['email_field'] == 'yes' ? true : false;
		$this->has_phone_field          = $this->params['phone_field'] == 'yes' ? true : false;
		$this->has_fax_field            = $this->params['fax'] == 'yes' ? true : false;
		$this->has_address_field        = $this->params['address_field'] == 'yes' ? true : false;
		$this->has_zip_code_field       = $this->params['zip_code_field'] == 'yes' ? true : false;
		$this->has_radius_search        = ($this->params['radius_search'] == 'yes') && ('map_api' == $search_location_address) ? true : false;
		$this->has_reset_filters_button = $this->params['reset_filters_button'] == 'yes' ? true : false;
		$this->has_apply_filters_button = $this->params['apply_filters_button'] == 'yes' ? true : false;
		$this->logged_in_user_only      = $this->params['logged_in_user_only'] == 'yes' ? true : false;
		$this->show_connector           = !empty( get_directorist_option('show_connector', 1) ) ? true : false;
		$this->show_popular_category    = !empty( get_directorist_option('show_popular_category', 1) ) ? true : false;

		$this->search_bar_title     = $this->params['search_bar_title'];
		$this->search_bar_sub_title = $this->params['search_bar_sub_title'];
		$this->search_button_text   = $this->params['search_button_text'];
		$this->more_filters_text    = $this->params['more_filters_text'];
		$this->reset_filters_text   = $this->params['reset_filters_text'];
		$this->apply_filters_text   = $this->params['apply_filters_text'];
		$this->more_filters_display = $this->params['more_filters_display'];
		$this->redirect_page_url    = $this->params['redirect_page_url'];

		$this->default_radius_distance = $this->options['radius_distance'];
		$this->tag_terms               = $this->listing_tag_terms();
		$this->search_text_placeholder = $this->options['search_text_placeholder'];
		$this->category_placeholder    = $this->options['category_placeholder'];
		$this->location_placeholder    = $this->options['location_placeholder'];
		$this->search_required_text    = $this->options['require_search_text'];
		$this->cat_required_text       = $this->options['require_search_category'];
		$this->loc_required_text       = $this->options['require_search_location'];     
		$this->category_id             = 'at_biz_dir-category';
		$this->category_class          = 'search_fields form-control';
		$this->location_id             = 'at_biz_dir-location';
		$this->location_class          = 'search_fields form-control';
		$this->location_source         = ( $search_location_address == 'map_api') ? 'map' : 'address';
		$this->connectors_title        = get_directorist_option('connectors_title', __('Or', 'directorist'));
		$this->popular_cat_title       = get_directorist_option('popular_cat_title', __('Browse by popular categories', 'directorist'));
		$this->popular_cat_num         = get_directorist_option('popular_cat_num', 10);
	}

	public function prepare_listing_data() {
		$search_more_filters_fields = $this->options['more_filters_fields'];
		$listing_location_address   = $this->options['location_address'];
		$filters_buttons            = $this->options['filters_buttons'];

		$this->has_search_text_field    = in_array( 'search_text', $search_more_filters_fields ) ? true : false;
		$this->has_category_field       = in_array( 'search_category', $search_more_filters_fields ) ? true : false;
		$this->has_location_field       = in_array( 'search_location', $search_more_filters_fields ) ? true : false;
		$this->has_price_field          = in_array( 'search_price', $search_more_filters_fields ) ? true : false;
		$this->has_price_range_field    = in_array( 'search_price_range', $search_more_filters_fields ) ? true : false;
		$this->has_rating_field         = in_array( 'search_rating', $search_more_filters_fields ) ? true : false;
		$this->has_radius_search        = ( 'map_api' == $listing_location_address) && in_array( 'radius_search', $search_more_filters_fields ) ? true : false;
		$this->has_tag_field            = in_array( 'search_tag', $search_more_filters_fields ) ? true : false;
		$this->has_custom_fields        = in_array( 'search_custom_fields', $search_more_filters_fields ) ? true : false;
		$this->has_website_field        = in_array( 'search_website', $search_more_filters_fields ) ? true : false;
		$this->has_email_field          = in_array( 'search_email', $search_more_filters_fields ) ? true : false;
		$this->has_phone_field          = in_array( 'search_phone', $search_more_filters_fields ) ? true : false;
		$this->has_fax_field            = in_array( 'search_fax', $search_more_filters_fields ) ? true : false;
		$this->has_address_field        = false;
		$this->has_zip_code_field       = in_array( 'search_zip_code', $search_more_filters_fields ) ? true : false;
		$this->has_open_now_field       = in_array( 'search_open_now', $search_more_filters_fields ) ? true : false;
		$this->has_reset_filters_button = in_array( 'reset_button', $filters_buttons ) ? true : false;
		$this->has_apply_filters_button = in_array( 'apply_button', $filters_buttons ) ? true : false;
		$this->reset_filters_text       = $this->options['reset_filters_text'];
		$this->apply_filters_text       = $this->options['apply_filters_text'];

		$this->default_radius_distance = $this->options['radius_distance'];
		$this->tag_terms               = $this->listing_tag_terms();
		$this->search_text_placeholder = $this->options['search_text_placeholder'];
		$this->category_placeholder    = $this->options['category_placeholder'];
		$this->location_placeholder    = $this->options['location_placeholder'];
		$this->search_required_text    = '';
		$this->cat_required_text       = '';
		$this->loc_required_text       = '';
		$this->category_id             = 'cat-type';
		$this->category_class          = 'form-control directory_field bdas-category-search';
		$this->location_id             = 'loc-type';
		$this->location_class          = 'form-control directory_field bdas-category-location';
		$this->location_source         = ($listing_location_address == 'map_api') ? 'map' : 'address';
	}

	public function price_range_template() {
		if ($this->has_price_field || $this->has_price_range_field) {
			atbdp_get_shortcode_template( 'search/price-range', array('searchform' => $this) );
		}
	}

	public function rating_template() {
		if ($this->has_rating_field) {
			atbdp_get_shortcode_template( 'search/rating', array('searchform' => $this) );
		}
	}

	public function radius_search_template() {
		if ($this->has_radius_search) {
			atbdp_get_shortcode_template( 'search/radius-search', array('searchform' => $this) );
		}
	}

	public function tag_template() {
		if ($this->has_tag_field && !empty($this->tag_terms)) {
			atbdp_get_shortcode_template( 'search/tag', array('searchform' => $this) );
		}
	}

	public function custom_fields_template() {
		if ($this->has_custom_fields) {
			atbdp_get_shortcode_template( 'search/custom-fields', array('searchform' => $this) );
		}
	}

	public function information_template() {
		if ( $this->has_website_field || $this->has_email_field || $this->has_phone_field || $this->has_fax_field || $this->has_address_field || $this->has_zip_code_field ) {
			atbdp_get_shortcode_template( 'search/information', array('searchform' => $this) );
		}
	}

	public function buttons_template() {
		if ($this->has_reset_filters_button || $this->has_apply_filters_button) {
			atbdp_get_shortcode_template( 'search/buttons', array('searchform' => $this) );
		}
	}

	public function open_now_template() {
		if ($this->has_open_now_field && in_array('directorist-business-hours/bd-business-hour.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			atbdp_get_shortcode_template( 'search/open-now', array('searchform' => $this) );
		}
	}

	public function search_text_template() {
		if ($this->has_search_text_field) {
			atbdp_get_shortcode_template( 'search/search-text', array('searchform' => $this) );
		}
	}

	public function category_template() {
		if ($this->has_category_field) {
			atbdp_get_shortcode_template( 'search/category', array('searchform' => $this) );
		}
	}

	public function location_template() {
		if ($this->has_location_field) {
			atbdp_get_shortcode_template( 'search/location-select', array('searchform' => $this) );
		}
	}

	public function location_map_template() {
		if ($this->has_location_field) {

			wp_localize_script( 'atbdp-geolocation', 'adbdp_geolocation', array( 'select_listing_map' => $this->select_listing_map ) );
			wp_enqueue_script( 'atbdp-geolocation' );

			$args = array(
				'searchform' => $this,
				'cityLat'    => isset( $_GET['cityLat'] ) ? $_GET['cityLat'] : '',
				'cityLng'    => isset( $_GET['cityLng'] ) ? $_GET['cityLng'] : '',
				'value'      => isset( $_GET['address'] ) ? $_GET['address'] : '',
			);

			atbdp_get_shortcode_template( 'search/location-geo', $args );
		}
	}

	public function form_top_fields() {
		ob_start();

		$this->search_text_template();
		$this->category_template();

		if ($this->location_source == 'address') {
			$this->location_template();
		}

		if ($this->location_source == 'map') {
			$this->location_map_template();
		}

		/**
		 * @since 5.0
		 */
		echo apply_filters('atbdp_search_form_fields', ob_get_clean());
	}

	public function more_buttons_template() {
		$html = '';

		if ( $this->has_more_filters_button || $this->has_search_button ) {
			$more_filters_icon   = $this->options['display_more_filter_icon']; 
			$search_button_icon  = $this->options['display_search_button_icon'];
			$more_filters_icon   = !empty($more_filters_icon) ? '<span class="' . atbdp_icon_type() . '-filter"></span>' : '';
			$search_button_icon  = !empty($search_button_icon) ? '<span class="fa fa-search"></span>' : '';

			$args = array(
				'searchform'         => $this,
				'more_filters_icon'  => $more_filters_icon,
				'search_button_icon' => $search_button_icon,
			);

			$html = atbdp_return_shortcode_template( 'search/more-buttons', $args );
		}

		/**
		 * @since 5.0
		 * It show the search button
		 */
		echo apply_filters('atbdp_search_listing_button', $html);
	}

	public function advanced_search_form_fields_template() {
		atbdp_get_shortcode_template( 'search/adv-search', array('searchform' => $this) );
	}

	public function top_categories_template() {
		if ( $this->show_popular_category ) {
			$top_categories = $this->top_categories();

			if ( !empty($top_categories) ) {
				$args = array(
					'searchform'      => $this,
					'top_categories'  => $top_categories,
				);
				atbdp_get_shortcode_template( 'search/top-cats', $args );
			}
		}
	}

	public function search_category_location_args() {
		return array(
			'parent'             => 0,
			'term_id'            => 0,
			'hide_empty'         => 0,
			'orderby'            => 'name',
			'order'              => 'asc',
			'show_count'         => 0,
			'single_only'        => 0,
			'pad_counts'         => true,
			'immediate_category' => 0,
			'active_term_id'     => 0,
			'ancestors'          => array(),
		);
	}

	public function price_value($arg) {
		if ( $arg == 'min' ) {
			return isset( $_GET['price'] ) ? $_GET['price'][0] : '';
		}

		if ( $arg == 'max' ) {
			return isset( $_GET['price'] ) ? $_GET['price'][1] : '';
		}

		return '';
	}

	public function the_price_range_input($range) {
		$checked = ! empty( $_GET['price_range'] ) && $_GET['price_range'] == $range ? ' checked="checked"' : '';
		printf('<input type="radio" name="price_range" value="%s"%s>', $range, $checked);
	}

	public function render_search_shortcode() {
		if ( $this->logged_in_user_only && ! atbdp_logged_in_user() ) {
			return ATBDP()->helper->guard( array('type' => 'auth') );
		}
		
		if ($this->redirect_page_url) {
			$redirect = '<script>window.location="' . esc_url($this->redirect_page_url) . '"</script>';
			return $redirect;
		}

		if (is_rtl()) {
			wp_enqueue_style('atbdp-search-style-rtl', ATBDP_PUBLIC_ASSETS . 'css/search-style-rtl.css');
		}
		else {
			wp_enqueue_style('atbdp-search-style', ATBDP_PUBLIC_ASSETS . 'css/search-style.css');
		}
		
		wp_enqueue_script( 'atbdp-search-listing' );
		wp_localize_script('atbdp-search-listing', 'atbdp_search', array(
			'ajaxnonce' => wp_create_nonce('bdas_ajax_nonce'),
			'ajax_url' => admin_url('admin-ajax.php'),
		));
		
		ATBDP()->enquirer->search_listing_scripts_styles();

		$bgimg = get_directorist_option('search_home_bg');
		if ( is_directoria_active() ) {
			$bgimg = $this->directoria_bgimg();
		}

		$container_class = is_directoria_active() ? 'container' : 'container-fluid';
		$container_class = apply_filters('atbdp_search_home_container_fluid', $container_class);
		$search_border = get_directorist_option('search_border', 1);
		
		$args = array(
			'searchform'          => $this,
			'bgimg'               => $bgimg,
			'container_class'     => $container_class,
			'border_inline_style' => empty($search_border) ? 'style="border: none;"' : '',
		);

		return atbdp_return_shortcode_template( 'search/search', $args );
	}

	public function directoria_bgimg() {
		$default = get_template_directory_uri() . '/images/home_page_bg.jpg';
		$theme_home_bg_image = get_theme_mod('directoria_home_bg');
		$search_home_bg = get_directorist_option('search_home_bg');
		$front_bg_image = (!empty($theme_home_bg_image)) ? $theme_home_bg_image : $search_home_bg;
		$search_home_bg_image = !empty($front_bg_image) ? $front_bg_image : $default;
		return $search_home_bg_image;
	}

	public function top_categories() {
		$args = array(
			'type'          => ATBDP_POST_TYPE,
			'parent'        => 0,
			'orderby'       => 'count',
			'order'         => 'desc',
			'hide_empty'    => 1,
			'number'        => (int)$this->popular_cat_num,
			'taxonomy'      => ATBDP_CATEGORY,
			'no_found_rows' => true,
		);
		$top_categories = get_categories(apply_filters('atbdp_top_category_argument', $args));
		return $top_categories;
	}

	public function category_icon_class($cat) {
		$icon = get_cat_icon($cat->term_id);
		$icon_type = substr($icon, 0, 2);
		$icon_class = ('la' === $icon_type) ? $icon_type . ' ' . $icon : 'fa ' . $icon;
		return $icon_class;
	}

	public function rating_field_data() {
		$rating_options = array(
			array(
				'selected' => '',
				'value'    => '',
				'label'    => __( 'Select Ratings', 'directorist' ),
			),
			array(
				'selected' => ( ! empty( $_GET['search_by_rating'] ) && '5' == $_GET['search_by_rating'] ) ? ' selected' : '',
				'value'    => '5',
				'label'    => __( '5 Star', 'directorist' ),
			),
			array(
				'selected' => ( ! empty( $_GET['search_by_rating'] ) && '4' == $_GET['search_by_rating'] ) ? ' selected' : '',
				'value'    => '4',
				'label'    => __( '4 Star & Up', 'directorist' ),
			),
			array(
				'selected' => ( ! empty( $_GET['search_by_rating'] ) && '3' == $_GET['search_by_rating'] ) ? ' selected' : '',
				'value'    => '3',
				'label'    => __( '3 Star & Up', 'directorist' ),
			),
			array(
				'selected' => ( ! empty( $_GET['search_by_rating'] ) && '2' == $_GET['search_by_rating'] ) ? ' selected' : '',
				'value'    => '2',
				'label'    => __( '2 Star & Up', 'directorist' ),
			),
			array(
				'selected' => ( ! empty( $_GET['search_by_rating'] ) && '1' == $_GET['search_by_rating'] ) ? ' selected' : '',
				'value'    => '1',
				'label'    => __( '1 Star & Up', 'directorist' ),
			),
		);

		return $rating_options;
	}

	public function listing_tag_terms() {
		$listing_tags_field = get_directorist_option( 'listing_tags_field', 'all_tags' );
		$category_slug      = get_query_var( 'atbdp_category' );
		$category           = get_term_by( 'slug', $category_slug, ATBDP_CATEGORY );
		$category_id        = ! empty( $category->term_id ) ? $category->term_id : '';
		$tag_args           = array(
			'post_type' => ATBDP_POST_TYPE,
			'tax_query' => array(
				array(
					'taxonomy' => ATBDP_CATEGORY,
					'terms'    => ! empty( $_GET['in_cat'] ) ? $_GET['in_cat'] : $category_id,
				),
			),
		);
		$category_select = ! empty( $_GET['in_cat'] ) ? $_GET['in_cat'] : $category_id;
		$tag_posts       = get_posts( $tag_args );
		if ( ! empty( $tag_posts ) ) {
			foreach ( $tag_posts as $tag_post ) {
				$tag_id[] = $tag_post->ID;
			}
		}
		$tag_id = ! empty( $tag_id ) ? $tag_id : '';
		$terms  = wp_get_object_terms( $tag_id, ATBDP_TAGS );

		if ( 'all_tags' == $listing_tags_field || empty( $category_select ) ) {
			$terms = get_terms( ATBDP_TAGS );
		}

		if ( ! empty( $terms ) ) {
			return $terms;
		}

		return null;
	}
}