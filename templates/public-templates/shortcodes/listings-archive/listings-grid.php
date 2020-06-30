<?php
/**
 * @author  AazzTech
 * @since   7.0
 * @version 7.0
 */

/**
 * @param WP_Query $listings It contains all the queried listings by a user
 * @since 5.5.1
 * @package Directorist
 */

global $listings;

do_action('atbdp_before_all_listings_grid', $listings);
?>

<div id="directorist" class="atbd_wrapper ads-advaced--wrapper">

    <?php
    /**
     * @since 7.0
     * @hooked Directorist_Template_Hooks::archive_header - 10
     */
    do_action( 'directorist_archive_header', $listings );
    ?>

    <div class="<?php $listings->grid_container_class(); ?>">
        <?php
        /**
         * @since 5.0
         * It fires before the listings columns
         * It only fires if the parameter [directorist_all_listing action_before_after_loop="yes"]
         */
        if ( $listings->get_the_prop( 'attributes', 'action_before_after_loop' ) ) {
            do_action('atbdp_before_grid_listings_loop');
        }

        $row_container = ( $listings->get_the_option('view_as') !== 'masonry_grid' ) ? '' : ' data-uk-grid';
        ?>

        <div class="row<?php echo esc_attr($row_container); ?>">
        	<?php $listings->loop_template(); ?>
        </div>

        <?php return;  ?>
        <div class="row">
            <div class="col-lg-12">
                <?php
                /**
                 * @since 5.0
                 */
                do_action('atbdp_before_listings_pagination');

                if ($listings->show_pagination) {
                    echo atbdp_pagination($listings->query, $listings->paged);
                }
                ?>
            </div>
        </div>

        <?php
        /**
         * @since 5.0
         * to add custom html
         * It only fires if the parameter [directorist_all_listing action_before_after_loop="yes"]
         */
        if ($listings->action_before_after_loop) {
            do_action('atbdp_after_grid_listings_loop');
        }
        ?>
    </div>
</div>