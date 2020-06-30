<?php

defined('ABSPATH') || exit;

if ( ! class_exists( 'ATBDP_Listing_Data_Store_CPT' ) ) :
class ATBDP_Listing_Data_Store_CPT {
    protected $id = 0;
    protected $listing = null;

    public $data = [];

    // __construct
    public function __construct( $listing = 0 ) {
        if ( is_numeric( $listing ) && $listing > 0  ) {
            $this->id = absint( $listing );
        }

        if ( is_object( $listing ) && ! is_null( $listing ) ) {
            $this->id = $listing->get_id();
        }

        $this->read();
    }

    // read
    public function read() {
        if ( empty( $this->id ) ) { return; }

        $id          =  $this->id;
		$author_id   = get_the_author_meta( 'ID' );
		$author_data = get_userdata( $author_id );
        $u_pro_pic   = get_user_meta( $author_id, 'pro_pic', true );
        $u_pro_pic   = ! empty( $u_pro_pic ) ? wp_get_attachment_image_src( $u_pro_pic, 'thumbnail' ) : '';
        $bdbh        = get_post_meta( $id, '_bdbh', true );

		$data = array(
			'id'                   => $id,
			'permalink'            => get_permalink( $id ),
			'title'                => get_the_title(),
			'cats'                 => get_the_terms( $id, ATBDP_CATEGORY ),
			'locs'                 => get_the_terms( $id, ATBDP_LOCATION ),
			'featured'             => get_post_meta( $id, '_featured', true ),
			'price'                => get_post_meta( $id, '_price', true ),
			'price_range'          => get_post_meta( $id, '_price_range', true ),
			'atbd_listing_pricing' => get_post_meta( $id, '_atbd_listing_pricing', true ),
			'listing_img'          => get_post_meta( $id, '_listing_img', true ),
			'listing_prv_img'      => get_post_meta( $id, '_listing_prv_img', true ),
			'excerpt'              => get_post_meta( $id, '_excerpt', true ),
			'tagline'              => get_post_meta( $id, '_tagline', true ),
			'address'              => get_post_meta( $id, '_address', true ),
			'email'                => get_post_meta( $id, '_email', true ),
			'web'                  => get_post_meta( $id, '_website', true ),
			'phone_number'         => get_post_meta( $id, '_phone', true ),
			'category'             => get_post_meta( $id, '_admin_category_select', true ),
			'post_view'            => get_post_meta( $id, '_atbdp_post_views_count', true ),
			'hide_contact_info'    => get_post_meta( $id, '_hide_contact_info', true ),
			'business_hours'       => ! empty( $bdbh ) ? atbdp_sanitize_array( $bdbh ) : array(),
			'enable247hour'        => get_post_meta( $id, '_enable247hour', true ),
			'disable_bz_hour_listing' => get_post_meta( $id, '_disable_bz_hour_listing', true ),
			'author_id'            => $author_id,
			'author_data'          => $author_data,
			'author_full_name'     => $author_data->first_name . ' ' . $author_data->last_name,
			'author_link'          => ATBDP_Permalink::get_user_profile_page_link( $author_id ),
			'author_link_class'    => ! empty( $author_data->first_name && $author_data->last_name ) ? 'atbd_tooltip' : '',
			'u_pro_pic'            => $u_pro_pic,
			'avatar_img'           => get_avatar( $author_id, apply_filters( 'atbdp_avatar_size', 32 ) ),
		);

		$average = ATBDP()->review->get_average( $id );
		$data['average'] = $average;

		$this->data = $data;
	}
	
	// wrapper_class
	public function wrapper_class() {
        return ( $this->get_the_prop( 'data', 'featured' ) ) ? 'directorist-featured-listings' : '';
	}

	// the_category_permalink
	public function the_category_permalink() {
		echo esc_url( ATBDP_Permalink::atbdp_get_category_page( $this->get_the_data( 'cats' )[0] ) );
	}

    // link_attr
	public function link_attr( string $return_type = 'echo' ) {
        $attr = " " . apply_filters('grid_view_title_link_add_attr', '');
        $attr = trim($attr);

        if ( 'echo' !== $return_type ) {
			return $attr;
		}

		echo $attr;
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

	// get_the_data
	public function get_the_data( $prop, $args = [ 'return' => true ]) {
		return $this->the_prop( 'data', $prop, $args );
	}
}
endif;