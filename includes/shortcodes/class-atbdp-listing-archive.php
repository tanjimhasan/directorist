<?php
defined('ABSPATH') || exit;

if ( ! class_exists( 'ATBDP_Listing_Archive' ) ) :
abstract class ATBDP_Listing_Archive {
	/**
     * Data.
     *
     * @since 7.0
     * @var   array
     */
	public $data = [];
	
    /**
     * Attributes.
     *
     * @since 7.0
     * @var   array
     */
    public $attributes = [];

    /**
     * Query args.
     *
     * @since 7.0
     * @var array
     */
    public $query_args = [];

    /**
     * Options
     *
     * @since 7.0
     * @var array
     */
    public $options = [];

    /**
     * Filter Tags
     *
     * @since 7.0
     * @var array
     */
    public $filter_tags = [
        'listings_params'          => 'atbdp_all_listings_params',
        'listings_meta_queries'    => 'atbdp_all_listings_meta_queries',
        'listings_query_arguments' => 'atbdp_all_listings_query_arguments',
	];


    /**
     * Render
     *
     * @return void
     */
    abstract public function render( $atttibutes );

    /**
     * Get Contents
     *
     * @return void
     */
    protected function get_contents() {
		$GLOBALS['listings'] = $this;
		
		// Load Assets
		$this->load_assets();
		$this->prepear_data();
        
        ob_start();

		// $this->get_header();
		$this->load_template();

        $contents = ob_get_clean();
		unset( $GLOBALS['listings'] );

        return $contents;
	}


	/**
	 * Load Template
	 * 
	 * @return void
	 */ 
	 protected function load_template( $return_type = 'echo' ) {
		$template = '';
		ob_start();
		// Extention - Listings with Map //@todo
		if ( 'listings_with_map' == $this->attributes['view'] ) {
			$template_file = "listing-with-map/map-view";
			$extension_file = BDM_TEMPLATES_DIR . '/map-view';

			atbdp_get_shortcode_ext_template( $template_file, $extension_file, null, $this, true );
			$template = ob_get_clean();
		} else {
			$template_file = "listings-archive/listings-" . $this->attributes['view'];
			$template = atbdp_return_shortcode_template( $template_file );
		}

		if ( 'echo' !== $return_type ) {
			return $template;
		}

		echo $template;
	}

	// prepear_data
	public function prepear_data() {
		$listings = $this->get_query_results();
		$this->data['query'] = $listings;
		
		$pagination_meta = [
			'is_paginated' => $this->attributes['show_pagination'],
			'total'        => $listings->total,
			'total_pages'  => $listings->total_pages,
			'per_page'     => $listings->per_page,
			'current_page' => $listings->current_page,
		];

		$GLOBALS['pagination_meta'] = $pagination_meta;
	}

	// loop_template
	public function loop_template( $loop = 'grid' ) {
		$listings = $this->data['query'];

		// Prime caches to reduce future queries.
		if ( is_callable( '_prime_post_caches' ) ) {
			_prime_post_caches( $listings->ids );
		}
		
		$original_post = $GLOBALS['post'];
		if ( count( $listings->ids ) ) {
			foreach ( $listings->ids as $listings_id ) {
				$GLOBALS['post'] = get_post( $listings_id );
				setup_postdata( $GLOBALS['post'] );
				$GLOBALS['listing_loop'] = new ATBDP_Listing_Data_Store_CPT( $listings_id );

				atbdp_get_shortcode_template( "listings-archive/loop/$loop" );
			}
		} else {
			echo '<p class="atbdp_nlf">' . __( 'No listing found.', 'directorist' ) . "</p>";
		}

		unset( $GLOBALS['listing_loop'] );
		unset( $GLOBALS['pagination_meta'] );

		$GLOBALS['post'] = $original_post;
		wp_reset_postdata();
	}

	/**
     * Load Assets
     *
     * @return void
     */
	protected function load_assets() {
		wp_enqueue_script('adminmainassets');
		wp_enqueue_script('atbdp-search-listing', ATBDP_PUBLIC_ASSETS . 'js/search-form-listing.js');
		wp_localize_script('atbdp-search-listing', 'atbdp_search', array(
			'ajaxnonce' => wp_create_nonce('bdas_ajax_nonce'),
			'ajax_url' => admin_url('admin-ajax.php'),
			'added_favourite' => __('Added to favorite', 'directorist'),
			'please_login' => __('Please login first', 'directorist')
		));
		wp_enqueue_script('atbdp-range-slider');

		if ( 'kilometers' == $this->get_the_option( 'radius_search_unit' ) ) {
			$miles = __( ' Kilometers', 'directorist' );
		}
		else {
			$miles = __( ' Miles', 'directorist' );
		}

		wp_localize_script( 'atbdp-range-slider', 'atbdp_range_slider', array(
			'Miles'       => $miles,
			'default_val' => $this->get_the_option('default_radius_distance')
		));

		if (!empty($this->redirect_page_url)) {
			$redirect = '<script>window.location="' . esc_url($this->redirect_page_url) . '"</script>';
			return $redirect;
		}
	}

    
    /**
     * Parse Attributes
     *
     * @return void
     */
    protected function parse_attributes( $atttibutes ) {
        $defaults = array(
			'view'                     => get_directorist_option( 'default_listing_view', 'grid' ),
			'_featured'                => 1,
			'filterby'                 => '',
			'orderby'                  => get_directorist_option( 'order_listing_by', 'date' ),
			'order'                    => get_directorist_option( 'sort_listing_by', 'asc' ),
			'listings_per_page'        => get_directorist_option( 'all_listing_page_items', 6 ),
			'show_pagination'          => ! empty( get_directorist_option( 'paginate_all_listings', 1 ) ) ? 'yes' : '',
			'header'                   => ! empty( get_directorist_option( 'display_listings_header', 1 ) ) ? 'yes' : '',
			'header_title'             => get_directorist_option( 'all_listing_header_title', __( 'Items Found', 'directorist' ) ),
			'category'                 => '',
			'location'                 => '',
			'tag'                      => '',
			'ids'                      => '',
			'columns'                  => get_directorist_option( 'all_listing_columns', 3 ),
			'featured_only'            => '',
			'popular_only'             => '',
			'advanced_filter'          => ! empty( get_directorist_option( 'listing_filters_button', 1 ) ) ? 'yes' : '',
			'display_preview_image'    => ! empty( get_directorist_option( 'display_preview_image', 1 ) ) ? 'yes' : '',
			'action_before_after_loop' => 'yes',
			'logged_in_user_only'      => '',
			'redirect_page_url'        => '',
			'map_height'               => get_directorist_option( 'listings_map_height', 350 ),
			'cache'                    => true,
		);

		$defaults = apply_filters( $this->filter_tags['listings_params'], $defaults );
		$atts     = shortcode_atts( $defaults, $atttibutes );
        
        $atts['view'] = atbdp_get_listings_current_view_name( $atts['view'] );

        $atts['listings_per_page']   = (int) $atts['listings_per_page'];
        $atts['columns']             = (int) $atts['columns'];
        $atts['listings_map_height'] = (int) $atts['map_height'];

        $atts['show_pagination']          = atbdp_string_to_bool( $atts['show_pagination'] );
        $atts['header']                   = atbdp_string_to_bool( $atts['header'] );
        $atts['advanced_filter']          = atbdp_string_to_bool( $atts['advanced_filter'] );
        $atts['display_preview_image']    = atbdp_string_to_bool( $atts['display_preview_image'] );
        $atts['action_before_after_loop'] = atbdp_string_to_bool( $atts['action_before_after_loop'] );
        $atts['logged_in_user_only']      = atbdp_string_to_bool( $atts['logged_in_user_only'] );

        $atts['categories'] = ! empty( $atts['category'] ) ? explode( ',', $atts['category'] ) : [];
        $atts['tags']       = ! empty( $atts['tag'] ) ? explode( ',', $atts['tag'] ) : [];
        $atts['locations']  = ! empty( $atts['location'] ) ? explode( ',', $atts['location'] ) : [];
        $atts['ids']        = ! empty( $atts['ids'] ) ? explode( ',', $atts['ids'] ) : [];

        return $atts;
    }


    /**
     * Get Options
     *
     * @return void
     */
    protected function get_options() {
        $options = [];
        $options['has_featured']               = get_directorist_option( 'enable_featured_listing' );
        $options['has_featured']               = $options['has_featured'] || is_fee_manager_active() ? $this->attributes['_featured']: $options['has_featured'];
        $options['popular_by']                 = get_directorist_option( 'listing_popular_by' );
        $options['average_review_for_popular'] = get_directorist_option( 'average_review_for_popular', 4 );
        $options['view_to_popular']            = get_directorist_option( 'views_for_popular' );
        
        $options['radius_search_unit']         = get_directorist_option( 'radius_search_unit', 'miles' );
        $options['default_radius_distance']    = get_directorist_option( 'listing_default_radius_distance', 0 );
        $options['select_listing_map']         = get_directorist_option( 'select_listing_map', 'google' );
        $options['filters_display']            = get_directorist_option( 'listings_display_filter', 'sliding' );
        $options['search_more_filters_fields'] = get_directorist_option( 'listing_filters_fields', array( 'search_text', 'search_category', 'search_location', 'search_price', 'search_price_range', 'search_rating', 'search_tag', 'search_custom_fields', 'radius_search' ) );
        $options['has_filters_button']         = $this->attributes['advanced_filter'];
        $options['has_filters_icon']           = get_directorist_option( 'listing_filters_icon', 1 ) ? true                                : false;
        $options['filter_button_text']         = get_directorist_option( 'listings_filter_button_text', __( 'Filters', 'directorist' ) );
        $options['paged']                      = atbdp_get_paged_num();
        $options['display_sortby_dropdown']    = get_directorist_option( 'display_sort_by', 1 ) ? true                                     : false;
        $options['display_viewas_dropdown']    = get_directorist_option( 'display_view_as', 1 ) ? true                                     : false;
        $options['sort_by_text']               = get_directorist_option( 'sort_by_text', __( 'Sort By', 'directorist' ) );
        $options['view_as_text']               = get_directorist_option( 'view_as_text', __( 'View As', 'directorist' ) );
        $options['view_as']                    = get_directorist_option( 'grid_view_as', 'normal_grid' );
        $options['sort_by_items']              = get_directorist_option( 'listings_sort_by_items', array( 'a_z', 'z_a', 'latest', 'oldest', 'popular', 'price_low_high', 'price_high_low', 'random' ) );
        
        $view_as_items                         = get_directorist_option( 'listings_view_as_items', array( 'listings_grid', 'listings_list', 'listings_map' ) );
        $options['views']                      = atbdp_get_listings_view_options( $view_as_items );
        $options['category_placeholder']       = get_directorist_option( 'listings_category_placeholder', __( 'Select a category', 'directorist' ) );
        $options['location_placeholder']       = get_directorist_option( 'listings_location_placeholder', __( 'Select a location', 'directorist' ) );
        $options['categories_fields']          = search_category_location_filter( $this->search_category_location_args(), ATBDP_CATEGORY );
        $options['locations_fields']           = search_category_location_filter( $this->search_category_location_args(), ATBDP_LOCATION );
        $options['c_symbol']                   = atbdp_currency_symbol( get_directorist_option( 'g_currency', 'USD' ) );
        $options['popular_badge_text']         = get_directorist_option( 'popular_badge_text', __( 'Popular', 'directorist' ) );
        $options['feature_badge_text']         = get_directorist_option( 'feature_badge_text', __( 'Featured', 'directorist' ) );
        $options['readmore_text']              = get_directorist_option( 'readmore_text', __('Read More', 'directorist'));
        $options['listing_location_address']   = get_directorist_option( 'listing_location_address', 'map_api' );
        $options['is_disable_price']           = get_directorist_option( 'disable_list_price' );
        $options['disable_single_listing']     = get_directorist_option( 'disable_single_listing') ? true                                  : false;
        $options['disable_contact_info']       = get_directorist_option( 'disable_contact_info', 0 ) ? true                                : false;
        $options['display_title']              = get_directorist_option( 'display_title', 1 ) ? true                                       : false;
        $options['display_review']             = get_directorist_option( 'enable_review', 1 ) ? true                                       : false;
        $options['display_price']              = get_directorist_option( 'display_price', 1 ) ? true                                       : false;
        $options['display_email']              = get_directorist_option( 'display_email', 0 ) ? true                                       : false;
        $options['display_web_link']           = get_directorist_option( 'display_web_link', 0 ) ? true                                    : false;
        $options['display_category']           = get_directorist_option( 'display_category', 1 ) ? true                                    : false;
        $options['display_view_count']         = get_directorist_option( 'display_view_count', 1 ) ? true                                  : false;
        $options['display_mark_as_fav']        = get_directorist_option( 'display_mark_as_fav', 1 ) ? true                                 : false;
        $options['display_publish_date']       = get_directorist_option( 'display_tagline_field', 1 ) ? true                               : false;
        $options['display_contact_info']       = get_directorist_option( 'display_contact_info', 1 ) ? true                                : false;
        $options['display_feature_badge_cart'] = get_directorist_option( 'display_feature_badge_cart', 1 ) ? true                          : false;
        $options['display_popular_badge_cart'] = get_directorist_option( 'display_popular_badge_cart', 1 ) ? true                          : false;
        $options['enable_tagline']             = get_directorist_option( 'enable_tagline' ) ? true                                         : false;
        $options['enable_excerpt']             = get_directorist_option( 'enable_excerpt' ) ? true                                         : false;
        $options['display_author_image']       = get_directorist_option( 'display_author_image', 1 ) ? true                                : false;
        $options['display_tagline_field']      = get_directorist_option( 'display_tagline_field', 0 ) ? true                               : false;
        $options['display_pricing_field']      = get_directorist_option( 'display_pricing_field', 1 ) ? true                               : false;
        $options['display_excerpt_field']      = get_directorist_option( 'display_excerpt_field', 0 ) ? true                               : false;
        $options['display_address_field']      = get_directorist_option( 'display_address_field', 1 ) ? true                               : false;
        $options['display_phone_field']        = get_directorist_option( 'display_phone_field', 1 ) ? true                                 : false;
        $options['display_readmore']           = get_directorist_option( 'display_readmore', 0) ? true                                     : false;
        $options['address_location']           = get_directorist_option( 'address_location', 'location' );
        $options['excerpt_limit']              = get_directorist_option( 'excerpt_limit', 20);
        $options['display_map_info']           = get_directorist_option('display_map_info', 1) ? true                                      : false;
        $options['display_image_map']          = get_directorist_option('display_image_map', 1) ? true                                     : false;
        $options['display_title_map']          = get_directorist_option('display_title_map', 1) ? true                                     : false;
        $options['display_address_map']        = get_directorist_option('display_address_map', 1) ? true                                   : false;
        $options['display_direction_map']      = get_directorist_option('display_direction_map', 1) ? true : false;

		return $options;
    }

    /**
     * Parse Query Args
     *
     * @return void
     */
    protected function parse_query_args() {
        $args = array(
			'post_type'      => ATBDP_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => $this->attributes['listings_per_page'],
        );
        

        if ( 'rand' == $this->attributes['orderby'] ) {
			$current_order = atbdp_get_listings_current_order( $this->attributes['orderby'] );
		}
		else {
			$current_order = atbdp_get_listings_current_order( $this->attributes['orderby'] . '-' . $this->attributes['order'] );
		}

		$this->data['current_order'] = $current_order;


		if ( $this->attributes['show_pagination'] ) {
			$args['paged'] = $this->options['paged'];
		}
		else {
			$args['no_found_rows'] = true;
		}

		if ( $this->attributes['ids'] ) {
			$args['post__in'] = $this->attributes['ids'];
		}

		$tax_queries = array();

        if ( ! empty( $this->attributes['categories'] ) 
            && ! empty( $this->attributes['locations'] ) 
            && ! empty( $this->attributes['tags'] ) ) {

			$tax_queries['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy'         => ATBDP_CATEGORY,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['categories'] ) ? $this->attributes['categories'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
				array(
					'taxonomy'         => ATBDP_LOCATION,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['locations'] ) ? $this->attributes['locations'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
				array(
					'taxonomy'         => ATBDP_TAGS,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['tags'] ) ? $this->attributes['tags'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
			);
		}
		elseif ( ! empty( $this->attributes['categories'] ) && ! empty( $this->attributes['tags'] ) ) {
			$tax_queries['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy'         => ATBDP_CATEGORY,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['categories'] ) ? $this->attributes['categories'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
				array(
					'taxonomy'         => ATBDP_TAGS,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['tags'] ) ? $this->attributes['tags'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
			);
		}
		elseif ( ! empty( $this->attributes['categories'] ) && ! empty( $this->attributes['locations'] ) ) {
			$tax_queries['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy'         => ATBDP_CATEGORY,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['categories'] ) ? $this->attributes['categories'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
				array(
					'taxonomy'         => ATBDP_LOCATION,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['locations'] ) ? $this->attributes['locations'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),

			);
		}
		elseif ( ! empty( $this->attributes['tags'] ) && ! empty( $this->attributes['locations'] ) ) {
			$tax_queries['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy'         => ATBDP_TAGS,
					'field'            => 'slug',
					'terms'            => ! empty( $tags ) ? $this->attributes['tags'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
				array(
					'taxonomy'         => ATBDP_LOCATION,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['locations'] ) ? $this->attributes['locations'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),

			);
		}
		elseif ( ! empty( $this->attributes['categories'] ) ) {
			$tax_queries['tax_query'] = array(
				array(
					'taxonomy'         => ATBDP_CATEGORY,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['categories'] ) ? $this->attributes['categories'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
			);
		}
		elseif ( ! empty( $this->attributes['tags'] ) ) {
			$tax_queries['tax_query'] = array(
				array(
					'taxonomy'         => ATBDP_TAGS,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['tags'] ) ? $this->attributes['tags'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
			);
		}
		elseif ( ! empty( $this->attributes['locations'] ) ) {
			$tax_queries['tax_query'] = array(
				array(
					'taxonomy'         => ATBDP_LOCATION,
					'field'            => 'slug',
					'terms'            => ! empty( $this->attributes['locations'] ) ? $this->attributes['locations'] : array(),
					'include_children' => true, /*@todo; Add option to include children or exclude it*/
				),
			);
		}

		$args['tax_query'] = $tax_queries;
		$meta_queries = array();

		if ( $this->options['has_featured'] ) {
			if ( '_featured' == $this->attributes['filterby'] ) {
				$meta_queries['_featured'] = array(
					'key'     => '_featured',
					'value'   => 1,
					'compare' => '=',
				);
			}
			else {
				$meta_queries['_featured'] = array(
					'key'     => '_featured',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				);
			}
		}

		if ( 'yes' == $this->attributes['featured_only'] ) {
			$meta_queries['_featured'] = array(
				'key'     => '_featured',
				'value'   => 1,
				'compare' => '=',
			);
		}

		$listings = get_atbdp_listings_ids();
		$rated    = array();

		if (  ( 'yes' == $this->attributes['popular_only'] ) || ( 'views-desc' === $current_order ) ) {
			if ( $this->options['has_featured'] ) {
				if ( 'average_rating' === $this->options['popular_by'] ) {
					if ( $listings->have_posts() ) {
						while ( $listings->have_posts() ) {
							$listings->the_post();
							$id = get_the_ID();
							$average    = ATBDP()->review->get_average( $id );
							if ( $this->options['average_review_for_popular'] <= $average ) {
								$rated[] = $id;
							}
						}
						$rating_id = array(
							'post__in' => ! empty( $rated ) ? $rated : array(),
						);
						$args = array_merge( $args, $rating_id );
					}
				}
				elseif ( 'view_count' === $this->options['popular_by'] ) {
					$meta_queriesoptions['views'] = array(
						'key'     => '_atbdp_post_views_count',
						'value'   => $this->options['view_to_popular'],
						'type'    => 'NUMERIC',
						'compare' => '>=',
					);

					$args['orderby'] = array(
						'_featured' => 'DESC',
						'views'     => 'DESC',
					);
				}
				else {
					$meta_queriesoptions['views'] = array(
						'key'     => '_atbdp_post_views_count',
						'value'   => $this->options['view_to_popular'],
						'type'    => 'NUMERIC',
						'compare' => '>=',
					);
					$args['orderby'] = array(
						'_featured' => 'DESC',
						'views'     => 'DESC',
					);

					if ( $listings->have_posts() ) {
						while ( $listings->have_posts() ) {
							$listings->the_post();
							$id = get_the_ID();
							$average          = ATBDP()->review->get_average( $id );
							if ( $this->options['average_review_for_popular'] <= $average ) {
								$rated[] = $id;
							}
						}
						$rating_id = array(
							'post__in' => ! empty( $rated ) ? $rated : array(),
						);
						$args = array_merge( $args, $rating_id );
					}
				}
			}
			else {
				if ( 'average_rating' === $this->options['popular_by'] ) {
					if ( $listings->have_posts() ) {
						while ( $listings->have_posts() ) {
							$listings->the_post();
							$id = get_the_ID();
							$average    = ATBDP()->review->get_average( $id );
							if ( $this->options['average_review_for_popular'] <= $average ) {
								$rated[] = $id;
							}
						}
						$rating_id = array(
							'post__in' => ! empty( $rated ) ? $rated : array(),
						);
						$args = array_merge( $args, $rating_id );
					}
				}
				elseif ( 'view_count' === $this->options['popular_by'] ) {
					$meta_queriesoptions['views'] = array(
						'key'     => '_atbdp_post_views_count',
						'value'   => (int) $this->options['view_to_popular'],
						'type'    => 'NUMERIC',
						'compare' => '>=',
					);
					$args['orderby'] = array(
						'views' => 'DESC',
					);
				}
				else {
					$meta_queriesoptions['views'] = array(
						'key'     => '_atbdp_post_views_count',
						'value'   => (int) $this->options['view_to_popular'],
						'type'    => 'NUMERIC',
						'compare' => '>=',
					);
					$args['orderby'] = array(
						'views' => 'DESC',
					);

					if ( $listings->have_posts() ) {
						while ( $listings->have_posts() ) {
							$listings->the_post();
							$id = get_the_ID();
							$average    = ATBDP()->review->get_average( $id );
							if ( $this->options['average_review_for_popular'] <= $average ) {
								$rated[] = $id;
							}
						}
						$rating_id = array(
							'post__in' => ! empty( $rated ) ? $rated : array(),
						);
						$args = array_merge( $args, $rating_id );
					}
				}
			}
		}

		switch ( $current_order ) {
			case 'title-asc':
			if ( $this->options['has_featured'] ) {
				$args['meta_key'] = '_featured';
				$args['orderby']  = array(
					'meta_value_num' => 'DESC',
					'title'          => 'ASC',
				);
			}
			else {
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
			}
			break;

			case 'title-desc':
			if ( $this->options['has_featured'] ) {
				$args['meta_key'] = '_featured';
				$args['orderby']  = array(
					'meta_value_num' => 'DESC',
					'title'          => 'DESC',
				);
			}
			else {
				$args['orderby'] = 'title';
				$args['order']   = 'DESC';
			}
			break;

			case 'date-asc':
			if ( $this->options['has_featured'] ) {
				$args['meta_key'] = '_featured';
				$args['orderby']  = array(
					'meta_value_num' => 'DESC',
					'date'           => 'ASC',
				);
			}
			else {
				$args['orderby'] = 'date';
				$args['order']   = 'ASC';
			}
			break;

			case 'date-desc':
			if ( $this->options['has_featured'] ) {
				$args['meta_key'] = '_featured';
				$args['orderby']  = array(
					'meta_value_num' => 'DESC',
					'date'           => 'DESC',
				);
			}
			else {
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
			}
			break;

			case 'price-asc':
			if ( $this->options['has_featured'] ) {
				$meta_queries['price'] = array(
					'key'     => '_price',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				);

				$args['orderby'] = array(
					'_featured' => 'DESC',
					'price'     => 'ASC',
				);
			}
			else {
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
			}
			break;

			case 'price-desc':
			if ( $this->options['has_featured'] ) {
				$meta_queries['price'] = array(
					'key'     => '_price',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				);

				$args['orderby'] = array(
					'_featured' => 'DESC',
					'price'     => 'DESC',
				);
			}
			else {
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
			}
			break;

			case 'rand':
			if ( $this->options['has_featured'] ) {
				$args['meta_key'] = '_featured';
				$args['orderby']  = 'meta_value_num rand';
			}
			else {
				$args['orderby'] = 'rand';
			}
			break;
        }
        
		$meta_queries       = apply_filters( $this->filter_tags['listings_meta_queries'], $meta_queries );
		$count_meta_queries = count( $meta_queries );

		if ( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : $meta_queries;
		}

        $args = apply_filters( $this->filter_tags['listings_query_arguments'], $args );
		$args['fields'] = 'ids';
		
		return $args;
    }

    // search_category_location_args
    protected function search_category_location_args() {
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
	
	/**
     * Generate and return the transient name for this shortcode based on the query args.
     *
     * @since 1.0
     * @return string
     */
    protected function get_transient_name() {
		$transient_name = 'atbdp_listings_loop_' . md5( wp_json_encode( $this->query_args ) );
        return $transient_name;
    }
    
    /**
     * Run the query and return an array of data, including queried ids and pagination information.
     *
     * @since  7.0
     * @return object Object with the following props; ids, per_page, found_posts, max_num_pages, current_page
     */
    protected function get_query_results() {
		$transient_name    = $this->get_transient_name();
        $transient_version = ATBDP_Cache_Helper::get_transient_version( 'listings_query' );
        $cache             = atbdp_string_to_bool( $this->attributes['cache'] ) === true;
		$transient_value   = $cache ? get_transient( $transient_name ) : false;

		if ( isset( $transient_value['value'], $transient_value['version'] ) && $transient_value['version'] === $transient_version ) {
			$results = $transient_value['value'];
        } else {
			
            $query = new \WP_Query( $this->query_args );
            $paginated = ! $query->get( 'no_found_rows' );

            $results = (object) [
                'ids'          => wp_parse_id_list( $query->posts ),
                'total'        => $paginated ? (int) $query->found_posts : count( $query->posts ),
                'total_pages'  => $paginated ? (int) $query->max_num_pages : 1,
                'per_page'     => (int) $query->get( 'posts_per_page' ),
                'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
            ];

            if ( $cache ) {
                $transient_value = [
                    'version' => $transient_version,
                    'value'   => $results,
				];
				set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );
            }
		}

		return $results;
	}


	// ======================================================
	// Template Helper Functions
	// ======================================================

	// loop_get_title
	public function loop_get_title() {
		global $listing_loop;

		$title     = $listing_loop->get_the_prop( 'data', 'title' );
		$permalink = $listing_loop->get_the_prop( 'data', 'permalink' );
		$link_attr = $listing_loop->link_attr();

		if ( ! $this->get_the_option( 'disable_single_listing' ) ) {
			$title = sprintf('<a href="%s"%s>%s</a>', $permalink, $link_attr, $title);
		}
		
		return $title;
	}

	// show_listing_tagline
	public function show_listing_tagline() {
		global $listing_loop;
		
		$tagline               = $listing_loop->get_the_prop( 'data', 'tagline' );
		$enable_tagline        = $this->get_the_prop( 'options', 'enable_tagline' );
		$display_tagline_field = $this->get_the_prop( 'options', 'display_tagline_field' );

		if ( ! empty( $tagline ) && $enable_tagline && $display_tagline_field ) {
			return true;
		}

		return false;
	}

	// show_price_meta
	public function show_price_meta() {
		global $listing_loop;

		$display_review = $this->get_the_prop( 'options', 'display_review' );
		$display_price  = $this->get_the_prop( 'options', 'display_price' );
		$price          = $listing_loop->get_the_prop( 'data', 'price' );
		$price_range    = $listing_loop->get_the_prop( 'data', 'price_range' );

		if ( $display_review || $display_price && ( ! empty( $price ) || ! empty( $price_range ) ) ) {
			return true;
		}

		return false;
	}

	// display_contact_info
	public function display_contact_info() {
		$display_contact_info = $this->get_the_option( 'display_contact_info' );
		$display_publish_date = $this->get_the_option( 'display_publish_date' );
		$display_email        = $this->get_the_option( 'display_email' );
		$display_web_link     = $this->get_the_option( 'display_web_link' );

		if ( ! empty( $display_contact_info || $display_publish_date || $display_email || $display_web_link ) ) {
			return true;
		}

		return false;
	}

	// display_excerpt
	public function display_excerpt() {
		global $listing_loop;

		$excerpt               = $listing_loop->get_the_data( 'excerpt' );
		$enable_excerpt        = $this->get_the_option( 'enable_excerpt' );
		$display_excerpt_field = $this->get_the_option( 'display_excerpt_field' );

		if ( ! empty( $excerpt ) && $enable_excerpt && $display_excerpt_field ) {
			return true;
		}

		return false;
	}

	// price_meta_template
	public function price_meta_template() {
		$html = atbdp_return_shortcode_template( 'listings-archive/loop/price-meta' );

		if ( $this->get_the_prop( 'attributes', 'view' ) == 'grid' ) {
			echo apply_filters('atbdp_listings_review_price', $html);
		}
		elseif ( $this->get_the_prop( 'attributes', 'view' ) == 'list' ) {
			echo apply_filters('atbdp_listings_list_review_price', $html);
		}
		else {
			echo $html;
		}
	}

	// display_price_range
	public function display_price_range() {
		global $listing_loop;

		echo atbdp_display_price_range( $listing_loop->get_the_data( 'price_range' ) );
	}

	// display_address_field
	public function display_address_field() {
		global $listing_loop;

		$address               = $listing_loop->get_the_data('address');
		$address_location      = $this->get_the_option( 'address_location' );
		$display_address_field = $this->get_the_option( 'display_address_field' );

		if ( ! empty( $address ) && ( 'contact' === $address_location ) && $display_address_field ) {
			return true;
		}

		return false;
	}

	// data_list_template
	public function data_list_template() {
		atbdp_get_shortcode_template( 'listings-archive/loop/data-list' );
	}

	// display_price
	public function display_price() {
		global $listing_loop;

		$display_price = atbdp_display_price(
			$listing_loop->get_the_data( 'price' ), 
			$this->get_the_option('is_disable_price'), 
			$currency = null, 
			$symbol = null, 
			$c_position = null, 
			$echo = false
		);

		return $display_price;
	}

	// display_email
	public function display_email() {
		global $listing_loop;

		if ( ! empty( $listing_loop->get_the_data( 'email' ) && $this->get_the_option( 'display_email' ) ) ) {
			return true;
		}

		return false;
	}

	// grid_thumbnail_template
	public function grid_thumbnail_template() {
		atbdp_get_shortcode_template( 'listings-archive/loop/grid-thumbnail' );
	}

	// loop_thumb_card_template
	public function loop_thumb_card_template() {
		atbdp_get_shortcode_template( 'listings-archive/loop/thumb-card' );
	}

	// top_content_template
	public function top_content_template() {
		$display_title  = $this->get_the_option( 'display_title' );
		$enable_tagline = $this->get_the_option( 'enable_tagline' );
		$display_review = $this->get_the_option( 'display_review' );
		$display_price  = $this->get_the_option( 'display_price' );

		if ( $display_title || $enable_tagline || $display_review || $display_price ) {
			atbdp_get_shortcode_template( 'listings-archive/loop/top-content' );
		}
	}

	// loop_grid_bottom_content_template
	public function loop_grid_bottom_content_template() {
		$html = atbdp_return_shortcode_template( 'listings-archive/loop/grid-bottom-content' );
		echo apply_filters('atbdp_listings_grid_cat_view_count', $html);
	}

	// categories_template
	public function categories_template() {
		atbdp_get_shortcode_template( 'listings-archive/loop/cats' );
	}

	// grid_footer_right_template
	public function grid_footer_right_template() {
		$html = atbdp_return_shortcode_template( 'listings-archive/loop/grid-footer-right-content' );
		echo apply_filters('atbdp_grid_footer_right_html', $html);
	}

	// loop_author_template
	public function loop_author_template() {
		atbdp_get_shortcode_template( 'listings-archive/loop/author' );
	}

	// loop_view_count_template
	public function loop_view_count_template() {
		atbdp_get_shortcode_template( 'listings-archive/loop/view-count' );
	}

	// get_address_from_locaton
	public function get_address_from_locaton() {
		global $listing_loop;

		$local_names = array();
		foreach ( $listing_loop->get_the_data('locs') as $term) {
			$local_names[$term->term_id] = $term->parent == 0 ? $term->slug : $term->slug;
			ksort($local_names);
			$locals = array_reverse($local_names);
		}
		$output = array();
		$link = array();
		foreach ($locals as $location) {
			$term = get_term_by('slug', $location, ATBDP_LOCATION);
			$link = ATBDP_Permalink::atbdp_get_location_page($term);
			$space = str_repeat(' ', 1);
			$output[] = "<a href='{$link}'>{$term->name}</a>";
		}

		return implode(', ', $output);
	}

	// get_published_date
	public function get_published_date() {
		$publish_date_format = get_directorist_option('publish_date_format', 'time_ago');

		if ('time_ago' === $publish_date_format) {
			$text = sprintf(__('Posted %s ago', 'directorist'), human_time_diff(get_the_time('U'), current_time('timestamp')));
		}
		else {
			$text = get_the_date();
		}
		return $text;
	}
	
	// has_listings_header
	public function has_listings_header() {
		$has_filter_button = ( ! empty( $this->options['has_filters_button'] ) && ! empty( $this->options['search_more_filters_fields'] ) );

		return ( $has_filter_button || ! empty( $this->attributes['header_title'] ) ) ? true : false;
	}
	
	// header_container_class
	public function header_container_class( string $return_type = 'echo' ) {
		$class_name = is_directoria_active() ? 'container' : 'container-fluid';
		$class_name = apply_filters( 'atbdp_listings_class_name', $class_name );
		$class_name = ( ! empty( $class_name ) ) ? $class_name : '';

		if ( 'echo' !== $return_type ) {
			return $class_name;
		}

		echo $class_name;
	}

	// header_title
	public function header_title( string $return_type = 'echo' ) {
		global $pagination_meta;

		$total_posts = ( ! empty ( $pagination_meta ) ) ? $pagination_meta['total'] : 0;
		$title = sprintf('<span>%s</span> %s', $total_posts, $this->get_the_prop( 'attributes', 'header_title' ));
		
		$title = apply_filters('atbdp_total_listings_found_text', "<h3>{$title}</h3>", $title);

		if ( 'echo' !== $return_type ) {
			return $title;
		}

		echo $title;
	}

	// has_header_toolbar
	public function has_header_toolbar() {
        return ( $this->options['display_viewas_dropdown'] || $this->options['display_sortby_dropdown'] ) ? true : false;
    }

	// grid_container_class
	public function grid_container_class( string $return_type = 'echo' ) {
        $class_name = is_directoria_active() ? 'container' : 'container-fluid';
		$class_name = apply_filters( 'atbdp_listings_grid_container_class', $class_name );
		$class_name = esc_attr( $class_name );

		if ( 'echo' !== $return_type ) {
			return $class_name;
		}

		echo $class_name;
	}
	
	// sortby_dropdown_template
	public function sortby_dropdown_template() {
        $html = atbdp_return_shortcode_template( 'listings-archive/sortby-dropdown' );
        echo apply_filters('atbdp_listings_header_sort_by_button', $html);
	}
	
	// sort_by_text
	public function sort_by_text() {
		echo esc_html( $this->get_the_prop( 'options', 'sort_by_text' ) );
	}

	// get_sort_by_link_list
	public function get_sort_by_link_list() {
        $link_list = array();

		$transient_name = 'listings_orderby_option';
		$options = get_transient( $transient_name );

		if ( ! $options ) {
			$options = atbdp_get_listings_orderby_options( $this->get_the_prop( 'options', 'sort_by_items' ) );
			set_transient( $transient_name, $options );
		}

        $current_order = ! empty( $this->get_the_prop('data', 'current_order') ) ? $this->get_the_prop( 'data', 'current_order ') : '';

        foreach ( $options as $value => $label ) {
            $active_class = ( $value == $current_order ) ? ' active' : '';
            $link         = add_query_arg( 'sort', $value );

            $link_item['active_class'] = $active_class;
            $link_item['link']         = $link;
            $link_item['label']        = $label;

            array_push( $link_list, $link_item );
        }

        return $link_list;
    }

	// viewas_dropdown_template
    public function viewas_dropdown_template() {
		$html  = atbdp_return_shortcode_template( 'listings-archive/viewas-dropdown' );
		$view  = $this->get_the_prop('attributes', 'view');
		$views = $this->get_the_prop('options', 'views');
		
        echo apply_filters('atbdp_listings_view_as', $html, $view, $views  );
	}

	// get_view_as_link_list
	public function get_view_as_link_list() {
        $link_list = array();
        $view      = ! empty( $this->get_the_prop( 'attributes', 'view' ) ) ? $this->get_the_prop( 'attributes', 'view' ): '';
		$views     = $this->get_the_prop( 'options', 'views' );

        foreach ( $views as $value => $label ) {
            $active_class = ( $view === $value ) ? ' active' : '';
            $link         = add_query_arg( 'view', $value );
            $link_item    = array();

            $link_item['active_class'] = $active_class;
            $link_item['link']         = $link;
            $link_item['label']        = $label;

            array_push( $link_list, $link_item );
        }

        return $link_list;
    }

	// filter_container_class
	public function filter_container_class() {
        echo ( 'overlapping' === $this->get_the_prop( 'options', 'filters_display' ) ) ? 'ads_float' : 'ads_slide';
	}
	
	// advanced_search_form_template
	public function advanced_search_form_template() {
		$GLOBALS['search_form'] = new Directorist_Listing_Search_Form('listing');
		atbdp_get_shortcode_template( 'listings-archive/advanced-search-form' );

		unset( $GLOBALS['search_form'] );
	}

	// location_field_type
	public function location_field_type( $type ) {
        if ( ! $this->has_location_field() ) {
            return false;
        }

        if ( $type !== $this->get_the_prop( 'options', 'listing_location_address' ) ) {
            return false;
        }

        return true;
	}
	
	// has_location_field
	public function has_location_field() {
        return in_array( 'search_location', $this->get_the_prop( 'options', 'search_more_filters_fields' ) );
	}
	
	// geolocation_field_data
	public function geolocation_field_data() {
		$select_listing_map   = $this->get_the_prop('options', 'select_listing_map');
		$location_placeholder = $this->get_the_prop('options', 'location_placeholder');

        $geo_loc = ( 'google' === $select_listing_map ) ? '<span class="atbd_get_loc la la-crosshairs"></span>' : '<span class="atbd_get_loc la la-crosshairs"></span>';

        $value       = ! empty( $_GET['address'] ) ? $_GET['address'] : '';
        $placeholder = ! empty( $location_placeholder ) ? sanitize_text_field( $location_placeholder ) : __( 'location', 'directorist' );
        $cityLat     = ( isset( $_GET['cityLat'] ) ) ? esc_attr( $_GET['cityLat'] ) : '';
        $cityLng     = ( isset( $_GET['cityLng'] ) ) ? esc_attr( $_GET['cityLng'] ) : '';

        wp_localize_script( 'atbdp-geolocation', 'adbdp_geolocation', array( 'select_listing_map' => $select_listing_map ) );
        wp_enqueue_script( 'atbdp-geolocation' );

        $data = array(
            'select_listing_map' => $select_listing_map,
            'geo_loc'            => $geo_loc,
            'value'              => $value,
            'placeholder'        => $placeholder,
            'cityLat'            => $cityLat,
            'cityLng'            => $cityLng,
        );

        return $data;
    }

	// the_prop
	public function the_prop( $group = '', $key = '', $args = 'echo' ) {
		$prop = '';

		if ( isset( $this->$group, $this->$group[$key] )  ) {
			$prop = $this->$group[$key];
		}

		// If arg is array
		if ( is_array( $args ) ) {
			if ( isset( $args['index']  ) ) {
				$index = $args['index'];
				$prop = $prop[$index];
			}

			if ( ! empty( $args['return'] ) ) {
				return $prop;
			}

			echo $prop;

			return;
		}

		// If arg is string
		if ( is_string( $args && 'return' === $args ) ) {
			return $prop;
		}

		echo $prop;
	}

	// get_the_prop
	public function get_the_prop( $group = '', $key = '', $args = [ 'return' => true ] ) {
		return $this->the_prop(  $group, $key, $args );
	}

	// get_the_attr
	public function get_the_attr( $prop, $args = [ 'return' => true ] ) {
		return $this->the_prop( 'attributes', $prop, $args );
	}

	// get_the_arg
	public function get_the_arg( $prop, $args = [ 'return' => true ] ) {
		return $this->the_prop( 'query_args', $prop, $args );
	}

	// get_the_data
	public function get_the_data( $prop, $args = [ 'return' => true ] ) {
		return $this->the_prop( 'data', $prop, $args );
	}

	// get_the_option
	public function get_the_option( $prop, $args = [ 'return' => true ] ) {
		return $this->the_prop( 'options', $prop, $args );
	}
}
endif;