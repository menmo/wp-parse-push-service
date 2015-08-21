<?php

function sendPushNotification($message, $channels)
{
	$appID = get_option('simpar_appID');
	$apiKey = get_option('simpar_restApi');

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