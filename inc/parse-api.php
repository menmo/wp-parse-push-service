<?php

function pps_send_post_notification($post_id, $alert = '', $channels = array()) {
	if(empty($alert)) {
		$alert = html_entity_decode( get_the_title($post_id), ENT_QUOTES, 'UTF-8' );
	}
	if(empty($channels)) {
		$all_categories = array();
		$categories = get_the_category($post_id);
		foreach($categories as $cat) {
			$all_categories = array_merge($all_categories, explode(',', trim(get_category_parents($cat->term_id, false, ',', true), ',')));
		}
		$channels = array_values(array_unique($all_categories));
	}
	if(!empty($alert)) {
		add_post_meta($post_id, '_pps_future_notification_timestamp', current_time( 'mysql' ), true);
		return pps_send_push_notification(array(
			'alert' => $alert,
			'badge' => 0,
			'url' => get_permalink($post_id)
		), $channels);
	}
	return false;
}

function pps_send_push_notification($message, $channels = array(""))
{
	$appID = get_option('pps_appID');
	$apiKey = get_option('pps_restApi');

	if(!isset($message['sound']) && get_option('pps_enableSound', false)) {
		$message['sound'] = 'default';
	}

	$url = 'https://api.parse.com/1/push/';
	$data = array(
	    'expiry' => 1451606400,
	    'data' => $message,
		'channels' => $channels
	);

	$_data = json_encode($data);
	$headers = array(
	    'X-Parse-Application-Id: ' . $appID,
	    'X-Parse-REST-API-Key: ' . $apiKey,
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($_data),
	);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($curl);
	if ($result === FALSE) {
		die(curl_error($curl));
	}
	curl_close($curl);

	return $result;
}

?>