<?php
defined('ABSPATH') || exit;

require_once ATBDP_INC_DIR . 'shortcodes/class-atbdp-listing-archive.php';


if ( ! class_exists( 'ATBDP_ALL_Listing' ) ) :
class ATBDP_ALL_Listing extends ATBDP_Listing_Archive {

    /**
     * Render
     *
     * @return void
     */
    public function render( $atttibutes ) {
        // Set Filter Tags
        $this->filter_tags['listings_params']          = 'atbdp_all_listings_params';
        $this->filter_tags['listings_meta_queries']    = 'atbdp_all_listings_meta_queries';
        $this->filter_tags['listings_query_arguments'] = 'atbdp_all_listings_query_arguments';


        // Parse Attributes
        $this->attributes = $this->parse_attributes( $atttibutes );

        // Set Options
        $this->options = $this->get_options();

        // Parse Query Qrgs
        $this->query_args = $this->parse_query_args();

        // Get Contents
        return $this->get_contents();
    }
}
endif;