<?php
/*
Plugin Name: Parse Push Service
Plugin URI: https://github.com/menmo/wp-parse-push-service
Description: This is a simple implementation for Parse.com Push Service
Version: 1.0.0
Author:
Author URI:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/////////////////////////////////////////////////////////
// fuctions for 'send push notifications on edit' menu //
/////////////////////////////////////////////////////////
function pps_admin_init() {
    
    if ( !function_exists('curl_version') ) {

        function pps_curl_warning() {
            echo "<div id='pps-curl-warning' class='updated fade'><p><strong>".__("cURL is NOT installed on this server. cURL is necessary for 'Simple Parse Push Service' plugin in order to work.", 'pps_context')."</strong> </p></div>";
        }
        add_action('admin_notices', 'pps_curl_warning'); 
        
        return; 
    } else if ( get_option('pps_appID') == null || get_option('pps_restApi') == null) {
        
        function pps_appname_warning() {
            echo "<div id='pps-warning' class='updated fade'><p><strong>".__("Parse Push Service plugin needs to be configured.", 'pps_context') ."</strong> ".sprintf(__('Please go to <a href="%s">Parse Push Service admin menu</a> to configure your Parse Account keys.', 'pps_context'), get_bloginfo('url').'/wp-admin/options-general.php?page=pps')."</p></div>";
        }
        add_action('admin_notices', 'pps_appname_warning'); 
        
        return; 
    } else {
        $savedPostTypes = get_option('pps_metabox_pt', array());

        if ( count( $savedPostTypes ) == 0) {
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
				get_option('pps_metaBoxPriority', 'high')
		    );
        }

        add_action( 'wp_ajax_pps_push_notification', 'pps_send_push_notification' );
    }
}

function pps_boxcontent($post) {
	echo '<p>';
	echo '<label for="pps_alert">' . __('Message') . '</label><br/>';
	echo '<input id="pps_alert" type="text" name="pps_alert" value="" style="width: 100%;">';
	_e('Leave empty to use post title.');
	echo '</p>';

	switch($post->post_status) {
		case 'publish':
			echo '<button class="button button-primary">'.__('Send').'</button>';
			break;
		case 'future':
			echo '<button class="button button-primary">'.__('Send when published').'</button>';
			break;
		default:
			echo '<button class="button button-primary" disabled="disabled">'.__('Send').'</button>';
			echo __('Post is not published');
	}

	echo '<div class="spinner"></div>';
}

function pps_future_to_publish($post) {

	$validPostTypes = get_option('pps_metabox_pt');
	if (!in_array($post->post_type, $validPostTypes)) {
		return;
	}

    if(get_post_meta($post->ID, '_pps_future_notification', true)) {
        pps_send_post_notification($post->ID, get_post_meta($post->ID, '_pps_future_notification_message', true));
        delete_post_meta($post->ID, '_pps_future_notification');
    }
}

function pps_send_post_push_notification() {
	$post_id = @$_POST['post_id'];

	if(is_numeric($post_id)) {

        $status = get_post_status($post_id);
        $alert = @$_POST['message'];

        if($status == 'publish') {
            include('inc/parse-api.php');
            echo pps_send_post_notification($post_id, $alert);
        } else if($status == 'future') {
            echo add_post_meta($post_id, '_pps_future_notification', 1, true);

            if(!empty($alert)) {
                add_post_meta($post_id, '_pps_future_notification_message', $alert, true);
            }
        }
	}

	wp_die();
}

//////////////////////////
// admin, settings menu //
//////////////////////////
function pps_admin() {
	include('inc/admin_settings.php');
}

function pps_submenu() {
	include('inc/admin_pending_notf.php');
}

function pps_admin_actions() {  

    add_menu_page("Parse Push Service", "Parse Push Service", 'manage_options', "pps", "pps_admin");
	add_submenu_page( "pps", "Settings", "Settings", "manage_options", "pps", "pps_admin" );
	$pending_notf_page = add_submenu_page( "pps", "Pending Notifications", "Pending Notifications", "manage_options", "pps_pending_notifications", "pps_submenu" );
	add_action( "admin_head-{$pending_notf_page}", 'pps_pending_notifications_script' );

	add_action( 'admin_print_scripts-post-new.php', 'pps_post_admin_script', 11 );
	add_action( 'admin_print_scripts-post.php', 'pps_post_admin_script', 11 );
} 


/*
 * Additional javascript or css
 * ============================================ */
function pps_pending_notifications_script() {
	wp_enqueue_script( 'admin-pending-notf-js', plugin_dir_url( __FILE__ ).'js/pendingNotificationsAdmin.js' );
}

function pps_post_admin_script() {
	wp_enqueue_script( 'pps-post-actions', plugin_dir_url( __FILE__ ).'js/post-actions.js' );
}

////////////////////////
// register functions //
////////////////////////
add_action('admin_init', 'pps_admin_init', 1);
add_action('admin_menu', 'pps_admin_actions');  
add_action('future_to_publish', 'pps_future_to_publish');

?>