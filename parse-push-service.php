<?php
/*
Plugin Name: Parse Push Service
Plugin URI: https://github.com/menmo/wp-parse-push-service
Description: This is a simple implementation for Parse.com Push Service
Version: 1.0.0
Author: Menmo AB
Author URI: http://www.menmo.se
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/////////////////////////////////////////////////////////
// fuctions for 'send push notifications on edit' menu //
/////////////////////////////////////////////////////////
function pps_admin_init() {

    if (!function_exists('curl_version')) {

        function pps_curl_warning() {
            echo "<div id='pps-curl-warning' class='updated fade'><p><strong>" . __("cURL is NOT installed on this server. cURL is necessary for 'Simple Parse Push Service' plugin in order to work.", 'pps_context') . "</strong> </p></div>";
        }

        add_action('admin_notices', 'pps_curl_warning');

        return;
    } else if (@$_GET['page'] != 'pps_settings' && (get_option('pps_appID') == null || get_option('pps_restApi') == null)) {

        function pps_appname_warning() {
            echo "<div id='pps-warning' class='updated fade'><p><strong>" . __("Parse Push Service plugin needs to be configured.", 'pps_context') . "</strong> " . sprintf(__('Please go to <a href="%s">Parse Push Service admin menu</a> to configure your Parse Account keys.', 'pps_context'), get_bloginfo('url') . '/wp-admin/admin.php?page=pps_settings') . "</p></div>";
        }

        add_action('admin_notices', 'pps_appname_warning');

        return;
    } else {
        $savedPostTypes = get_option('pps_metabox_pt', array());

        if (count($savedPostTypes) == 0) {
            $savedPostTypes[] = 'post';
            update_option('pps_metabox_pt', $savedPostTypes, false);
        }

        foreach ($savedPostTypes as $postType) {
            add_meta_box(
                'pps_box',
                'Push Notification',
                'pps_boxcontent',
                $postType,
                'side',
                get_option('pps_metaBoxPriority', 'default')
            );
        }

        add_action('wp_ajax_pps_push_notification', 'pps_send_post_push_notification');
    }
}

function pps_boxcontent($post) {
    $pending = get_post_meta($post->ID, '_pps_future_notification', true);
    if ($pending) {
        echo __("Post has pending notification");
        echo '<br/><a href="' . admin_url('admin.php?page=pps') . '">Click here to edit</a>';
    } else {
        echo '<p>';
        echo '<label for="pps_alert">' . __('Message') . '</label><br/>';
        echo '<input id="pps_alert" type="text" name="pps_alert" value="" style="width: 100%;">';
        _e('Leave empty to use post title.');
        echo '</p>';

        $available_channels = get_option('pps_selected_cats');
        $cats = wp_get_post_categories($post->ID);
        $selected_channel = false;
        foreach($cats as $cat) {
            if(in_array($cat, $available_channels)) {
                $selected_channel = $cat;
                break;
            }
        }
        if(!$selected_channel) {
            $selected_channel = get_option('pps_default_cat');
        }

        echo '<p>';
        echo '<label for="pps_channel">' . __('Channel') . '</label><br/>';

        $args = array(
            'order_by' => 'name',
            'value_field' => 'slug',
            'name' => 'pps_channel',
            'include' => $available_channels,
            'selected' => $selected_channel
        );
        wp_dropdown_categories($args);
        echo '</p>';

        switch ($post->post_status) {
            case 'publish':
                echo '<button class="button button-primary">' . __('Send') . '</button>';
                break;
            case 'future':
                echo '<button class="button button-primary">' . __('Send when published') . '</button>';
                break;
            default:
                echo '<button class="button button-primary" disabled="disabled">' . __('Send') . '</button>';
                echo __('Post is not published');
        }

        if($timestamp = get_post_meta($post->ID, '_pps_future_notification_timestamp', true)) {
            $info = "Push sent: " . $timestamp;
        } else {
            $info = '';
        }
        echo '<div class="spinner"></div>';
        echo "<p class=\"info\">$info</p>";
    }
}

function pps_publish_future_post($post_id) {
    if (get_post_meta($post_id, '_pps_future_notification', true)) {
        include('inc/parse-api.php');
        pps_send_post_notification($post_id, get_post_meta($post_id, '_pps_future_notification_message', true), get_post_meta($post_id, '_pps_future_notification_channel', true));
        delete_post_meta($post_id, '_pps_future_notification');
        delete_post_meta($post_id, '_pps_future_notification_message');
        delete_post_meta($post_id, '_pps_future_notification_channel');
    }
}

function pps_send_post_push_notification() {
    $post_id = @$_POST['post_id'];

    if (is_numeric($post_id)) {

        $status = get_post_status($post_id);

        // TODO FIX HERE
        // escape unescape
        $alert = @$_POST['message'];
        $alert = @stripslashes($alert);
        $channel = @$_POST['channel'];

        if(!empty($channel)) {
            $channels = array($channel);
        } else {
            $channels = false;
        }

        if ($status == 'publish') {
            include('inc/parse-api.php');
            echo pps_send_post_notification($post_id, $alert, $channels);
        } else if ($status == 'future') {
            add_post_meta($post_id, '_pps_future_notification', 1, true);

            if (!empty($alert)) {
                add_post_meta($post_id, '_pps_future_notification_message', $alert, true);
            }

            if (!empty($channel)) {
                add_post_meta($post_id, '_pps_future_notification_channel', $channels, true);
            }

            echo "reload";
        }
    }

    wp_die();
}

//////////////////////////
// admin, settings menu //
//////////////////////////
function pps_settings() {
    include('inc/admin_settings.php');
}

function pps_pending() {
    include('inc/admin_pending_notf.php');
}

function pps_admin_actions() {

    add_menu_page("Parse Push Service", "Parse Push", 'manage_options', "pps", "pps_pending", plugin_dir_url(__FILE__) . 'favicon-16x16.png');
    $pending_notf_page = add_submenu_page("pps", "Pending Notifications", "Pending Notifications", "manage_options", "pps", "pps_pending");
    add_submenu_page("pps", "Settings", "Settings", "manage_options", "pps_settings", "pps_settings");
    add_action("admin_head-{$pending_notf_page}", 'pps_pending_notifications_script');

    add_action('admin_print_scripts-post-new.php', 'pps_post_admin_script', 11);
    add_action('admin_print_scripts-post.php', 'pps_post_admin_script', 11);
}


/*
 * Additional javascript or css
 * ============================================ */
function pps_pending_notifications_script() {
    wp_enqueue_script('admin-pending-notf-js', plugin_dir_url(__FILE__) . 'js/pendingNotificationsAdmin.js');
}

function pps_post_admin_script() {
    wp_enqueue_script('pps-post-actions', plugin_dir_url(__FILE__) . 'js/post-actions.js');
}

/*
 * outout available push categories for our nobile apps
 */
function pps_categories_callback() {
    $available_channels = get_option('pps_selected_cats');
    $categories = get_terms('category', array(
        'include' => $available_channels,
    ));
    $output = array();

    foreach($categories as $category) {
        $output[] = array("slug" => $category->slug, "name" => $category->name);
    }

    echo json_encode($output);

    exit;
}

////////////////////////
// register functions //
////////////////////////
add_action('admin_init', 'pps_admin_init', 1);
add_action('admin_menu', 'pps_admin_actions');
add_action('publish_future_post', 'pps_publish_future_post');
add_action( 'wp_ajax_pps_categories', 'pps_categories_callback' );
add_action( 'wp_ajax_nopriv_pps_categories', 'pps_categories_callback' );

?>
