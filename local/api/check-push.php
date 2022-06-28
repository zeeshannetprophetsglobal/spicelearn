<?php
    require_once('curl.php');
    $url = "https://fcm.googleapis.com/fcm/send";
    //$token = "f5bVZ8rxSSm0-SB28UaAhI:APA91bFaGfVldVtRm7EPLqoQHM2ps7yu2IiUk0CIwdNAPYkiGbJxONFdLKem_MnLMzQTqsQNlcUsPzyFgT6iDrrhu3q_xD1FFgrp3KzBVAr-AnIVX338aobo9GfWla0Y5_JY5C_W68jf";
	//e0trvn7STZSLF3zCEB7XW_:APA91bFsJFcDK1Cf9eLI02xLChSiDyizufS7-TpPoeLy8X9kMgfslELBeIdnRJhvU7JNHt6vZ26f2u2_1HGr_yT4ErU4Avm3pFpz2D7Z2vYCyNI-zDS_-QjpAbq1vcAl5QBeGE2x_TXg
	
	$token = "djWxnZ3DR_-i7QRFFikhtI:APA91bFyN1lX3erljzva2mvCGqds_ROpAxj6ej2B8FVt-nAXJzgLcgMjEsmwxz64y5wvHEcs67K86OqwDCBsv35mqdFPL5NABKEHr1M9_a9PUkzi9dvp9mW0o6XPZlLWv169SpmePjj8";
	
    $serverKey = 'AAAAJv9DEKA:APA91bGZdAki8i4bCVFtqD65j73dB2AJyKh0EOgShospx11BKrIudMh9PcxH8ZdE6HHjZyXtXSwriwfnialol6nHxxrm62Cm_6Qtncx4C81YPEAHAjOVJdxmNG5sT2o05rtbM6--IDDc';
    $title = "Notification title testing ";
    $body = "Hello I am from Your php server testing";
    $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    //Send the request
    $response = curl_exec($ch);
    //Close request
    if ($response === FALSE) {
    die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
?>