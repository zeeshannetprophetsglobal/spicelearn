<?php 
require_once('curl.php');    
    $token = "eosea8emThSCEG95EQG50d:APA91bEFQHsFoCOWT1EAc5nIUMqgUARlyzcRV1L5CGSEWS3Zwl-kUsUnB0cjeePpGOEe7hL0H_RkaZrThDOhFU0zpkTNEQVMAERTB9xCYUdFCZzayctPTFAHxOB53Nl-F4VBvV-5O-zZ";

$arr_deviceId = array();
$arr_deviceId[] = $token;
/*foreach($arr_general_device_tokens as $arrId) {
$arr_deviceId[] = $arrId->device_id;
}*/

$url = "https://fcm.googleapis.com/fcm/send";

$serverKey = 'AAAAJv9DEKA:APA91bGZdAki8i4bCVFtqD65j73dB2AJyKh0EOgShospx11BKrIudMh9PcxH8ZdE6HHjZyXtXSwriwfnialol6nHxxrm62Cm_6Qtncx4C81YPEAHAjOVJdxmNG5sT2o05rtbM6--IDDc';

$arr_messageInfo['subject'] = "Notification title testing ";
$arr_messageInfo['messageBody'] = "Hello I am from Your php server testing";
$notification = array('title' => $arr_messageInfo['subject'] , 'body' => $arr_messageInfo['messageBody'], 'sound' => 'default'); //, 'badge' => '1'

if(count($arr_deviceId)) {
    $arrayToSend = array('registration_ids' => $arr_deviceId, 'notification' => $notification, 'priority' => 'high');//for multiple device

    $json = json_encode($arrayToSend);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
	
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to 
    //Send the request
    $response = curl_exec($ch);
    //Close request
    if ($response === FALSE) {
    die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
}