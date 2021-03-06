<?php
/**
 * @author AazzTech
 */

class ATBDP_Shortcode {

	public function __construct() {

		$shortcodes = apply_filters( 'atbdp_shortcodes', [
			// Archive
			'directorist_all_listing' => [ $this, 'listing_archive' ],
			'directorist_category'    => [ $this, 'category_archive' ],
			'directorist_tag'         => [ $this, 'tag_archive' ],
			'directorist_location'    => [ $this, 'location_archive' ],
			
			// Taxonomy
			'directorist_all_categories' => [ $this, 'all_categories' ],
			'directorist_all_locations'  => [ $this, 'all_locations' ],

			// Search
			'directorist_search_listing' => [ $this, 'search_listing' ],
			'directorist_search_result'  => [ $this, 'search_result' ],

			// Single
			'directorist_listing_top_area'            => [ $this, 'directorist_listing_header' ],
			'directorist_listing_tags'                => [ $this, 'directorist_tags' ],
			'directorist_listing_custom_fields'       => [ $this, 'directorist_custom_field' ],
			'directorist_listing_video'               => [ $this, 'directorist_listing_video' ],
			'directorist_listing_map'                 => [ $this, 'directorist_listing_map' ],
			'directorist_listing_contact_information' => [ $this, 'directorist_listing_contact_information' ],
			'directorist_listing_author_info'         => [ $this, 'directorist_listing_author_details' ],
			'directorist_listing_contact_owner'       => [ $this, 'directorist_listing_contact_owner' ],
			'directorist_listing_review'              => [ $this, 'directorist_listing_review' ],
			'directorist_related_listings'            => [ $this, 'directorist_related_listings' ],
			
			// Author
			'directorist_author_profile' => [ $this, 'author_profile' ],
			'directorist_user_dashboard' => [ $this, 'user_dashboard' ],
			
			// Forms
			'directorist_add_listing'         => [ $this, 'add_listing' ],
			'directorist_custom_registration' => [ $this, 'user_registration' ],
			'directorist_user_login'          => [ $this, 'custom_user_login' ],
			
			// Checkout
			'directorist_checkout'            => [ new ATBDP_Checkout, 'display_checkout_content' ],
			'directorist_payment_receipt'     => [ new ATBDP_Checkout, 'payment_receipt' ],
			'directorist_transaction_failure' => [ new ATBDP_Checkout, 'transaction_failure' ],

		]);

		// Register Shorcodes
		foreach ( $shortcodes as $shortcode => $callback ) {
			add_shortcode( $shortcode, $callback);
		}

    	// Ajax
		add_action('wp_ajax_atbdp_custom_fields_listings_front',                 array($this, 'ajax_callback_custom_fields'), 10, 2);
		add_action('wp_ajax_nopriv_atbdp_custom_fields_listings_front',          array($this, 'ajax_callback_custom_fields'), 10, 2);
		add_action('wp_ajax_atbdp_custom_fields_listings_front_selected',        array($this, 'ajax_callback_custom_fields'), 10, 2);
		add_action('wp_ajax_nopriv_atbdp_custom_fields_listings_front_selected', array($this, 'ajax_callback_custom_fields'), 10, 2);
	}

	public function listing_archive( $atts ) {
		$listings = new Directorist_Listings( $atts );
		return $listings->render_shortcode();
	}

	public function category_archive( $atts ) {
		$atts             = !empty( $atts ) ? $atts : array();
		$category_slug    = get_query_var('atbdp_category');
		$atts['category'] = sanitize_text_field( $category_slug );
		return $this->listing_archive( $atts );
	}

	public function tag_archive( $atts ) {
		$atts        = !empty( $atts ) ? $atts : array();
		$tag_slug    = get_query_var('atbdp_tag');
		$atts['tag'] = sanitize_text_field( $tag_slug );
		return $this->listing_archive( $atts );
	}

	public function location_archive( $atts ) {
		$atts        = !empty( $atts ) ? $atts : array();
		$tag_slug    = get_query_var('atbdp_location');
		$atts['location'] = sanitize_text_field( $tag_slug );
		return $this->listing_archive( $atts );
	}

	public function all_categories($atts) {
		$taxonomy = new Directorist_Listing_Taxonomy($atts, 'category');
		return $taxonomy->render_shortcode();
	}

	public function all_locations($atts) {
		$taxonomy = new Directorist_Listing_Taxonomy($atts, 'location');
		return $taxonomy->render_shortcode();
	}

	public function search_listing($atts) {
		$searchform = new Directorist_Listing_Search_Form( 'search_form', $atts );
		return $searchform->render_search_shortcode();
	}

	public function search_result($atts) {
		$listings = new Directorist_Listings( $atts, 'search' );
		return $listings->render_shortcode();
	}

	public function directorist_listing_header() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_top_area();
	}

	public function directorist_tags() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_tags();
	}

	public function directorist_custom_field() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_custom_fields();
	}

	public function directorist_listing_video() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_video();
	}

	public function directorist_listing_map() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_map();
	}

	public function directorist_listing_contact_information() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_contact_information();
	}

	public function directorist_listing_author_details() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_author_info();
	}

	public function directorist_listing_contact_owner() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_contact_owner();
	}

	public function directorist_listing_review() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_listing_review();
	}
	
	public function directorist_related_listings() {
		$listing = new Directorist_Single_Listing();
		return $listing->render_shortcode_related_listings();
    	// @todo @kowsar filter=atbdp_related_listing_template in "Post Your Need" extention
	}

	public function author_profile($atts) {
		$author = Directorist_Listing_Author::instance();
		return $author->render_shortcode_author_profile($atts);
	}

	public function user_dashboard($atts) {
		$dashboard = Directorist_Listing_Dashboard::instance();
		return $dashboard->render_shortcode_user_dashboard($atts);
	}

	public function add_listing($atts) {
		$forms = Directorist_Listing_Forms::instance();
		return $forms->render_shortcode_add_listing($atts);
	}

	public function user_registration() {
		$forms = Directorist_Listing_Forms::instance();
		return $forms->render_shortcode_custom_registration();
	}

	public function custom_user_login() {
		$forms = Directorist_Listing_Forms::instance();
		return $forms->render_shortcode_user_login();
	}

	public function ajax_callback_custom_fields($post_id = 0, $term_id = array()) {
		$ajax = false;
		if (isset($_POST['term_id'])) {
			$ajax = true;
			$post_ID = !empty($_POST['post_id']) ? (int)$_POST['post_id'] : '';
			$term_id = $_POST['term_id'];
		}
    	// Get custom fields
		$categories = !empty($term_id) ? $term_id : array();
		$args = array(
			'post_type' => ATBDP_CUSTOM_FIELD_POST_TYPE,
			'posts_per_page' => -1,
			'status' => 'published'
		);
		$meta_queries = array();

		if ( ! empty( $categories ) && is_array( $categories )){
			if ( count( $categories ) > 1) {
				$sub_meta_queries = array();
				foreach ($categories as $value) {
					$sub_meta_queries[] = array(
						'key' => 'category_pass',
						'value' => $value,
						'compare' => '='
					);
				}

				$meta_queries[] = array_merge(array('relation' => 'OR'), $sub_meta_queries);
			} else {
				$meta_queries[] = array(
					'key' => 'category_pass',
					'value' => $categories[0],
					'compare' => '='
				);
			}
		}
		$meta_queries[] = array(
			array(
				'relation' => 'OR',
				array(
					'key' => 'admin_use',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key' => 'admin_use',
					'value' => 1,
					'compare' => '!='
				),
			)
		);
		$meta_queries[] = array(
			array(
				'key' => 'associate',
				'value' => 'categories',
				'compare' => 'LIKE',
			),
		);


		$count_meta_queries = count($meta_queries);
		if ($count_meta_queries) {
			$args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
		}

		$atbdp_query = new WP_Query($args);

		if ($atbdp_query->have_posts()) {
      		// Start the Loop
			global $post;
      		// Process output
			ob_start();
			$include = apply_filters('include_style_settings', true);
			include ATBDP_TEMPLATES_DIR . 'admin-templates/listing-form/add-listing-custom-field.php';
            wp_reset_postdata(); // Restore global post data stomped by the_post()
            $output = ob_get_clean();

            print $output;

            if ($ajax) {
            	wp_die();
            }
        }
        else {
        	echo '<div class="custom_field_empty_area"></div>';
        	if ($ajax) {
        		wp_die();
        	}
        }
    }
}