<?php
defined('ABSPATH') || exit;

if ( ! class_exists( 'ATBDP_Shortcodes' ) ) :
class ATBDP_Shortcodes {

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->register_shortcodes();
    }


    /**
     * Get Shortcodes
     *
     * @return array
     */
    public function get_shortcodes() {
        return [
            'directorist_all_listing' => ATBDP_All_Listing::class,
        ];
    }

    /**
     * Register Shortcodes
     *
     * @return void
     */
    public function register_shortcodes() {
        $shortcodes = $this->get_shortcodes();

        if ( ! count( $shortcodes ) ) {return;}

        foreach ( $shortcodes as $shortcode_name => $class_name ) {
            if ( class_exists( $class_name ) ) {
                if ( method_exists( $class_name, 'render' ) ) {
                    $shortcode = new $class_name();
                    add_shortcode( $shortcode_name, [$shortcode, 'render'] );
                }
            }
        }
    }
}
endif;