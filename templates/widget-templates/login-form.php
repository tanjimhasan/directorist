<?php
if (!is_user_logged_in()) {

    $title = !empty($instance['title']) ? esc_html($instance['title']) : esc_html__('Title', ATBDP_TEXTDOMAIN);
    echo $args['before_widget'];
    echo '<div class="atbd_widget_title">';
    echo $args['before_title'] . esc_html(apply_filters('widget_submit_item_title', $title)) . $args['after_title'];
    echo '</div>';
    ?>
    <div class="directorist">
        <?php
        if (isset($_GET['login']) && $_GET['login'] == 'failed'){
            printf('<p class="alert-danger">  <span class="fa fa-exclamation"></span>%s</p>',__(' Invalid username or password!', ATBDP_TEXTDOMAIN));
        }
        wp_login_form();
        wp_register();
        printf(__('<p>Don\'t have an account? %s</p>', ATBDP_TEXTDOMAIN), "<a href='".ATBDP_Permalink::get_registration_page_link()."'> ". __('Sign up', ATBDP_TEXTDOMAIN)."</a>");
        ?>
    </div>
    <?php
    echo $args['after_widget'];
}
