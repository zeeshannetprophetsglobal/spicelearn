<?php
require_once('../../config.php');


require_once($CFG->dirroot.'/local/api/lib.php');

//course_start_notification();
 //course_complete_notification();
 //enrolled_notification();
 

// 				// $url = "https://infiniterunner2.firebaseio.com/Data/4.json";		
// 				// $data =  file_get_contents($url);
// 				// $arrayData = json_decode($data);
					
// 					//print_r($arrayData->user_score);
				

// global $DB;    

// 	$url = "https://fcm.googleapis.com/fcm/send";
   
//    $coursedata = get_courses();
// 	print_object($coursedata);die;
// 	foreach($coursedata as $courseid){
	
// 	 $tokensql = "SELECT DISTINCT a.id,a.userid,b.device_token,c.courseid 
// 	FROM {user_enrolments} as a join {user} AS b ON a.userid=b.id join {enrol} as c on c.id=a.enrolid
// 	where  b.device_token != 'null' and b.device_token != '' and c.courseid=". $courseid->id ." and a.timestart = 0 and a.userid NOT IN (SELECT CONCAT(userid) FROM {local_check_puss_notification} WHERE courseid = ".$courseid->id. " and msgid = 3 )";
// 		$getuser = $DB->get_records_sql($tokensql,null);
		
		
// 		print_object($getuser);
		
		
// 		$arr_deviceId = array();
// 		$arr_deviceId[] = 'fPO3BQafRAyGjLY8m0_3Iu:APA91bHi91B9ZreFl_Oa2LHcO9OnVI1zUaYZO1-k0XP9wlGNL4QM0tWesvtsi8A2FS4yI9TLkLxkznG3hFJ9_7WzI7U1TFtsvULbdiBhbzIXmovFDXKhmusvyAYbjDQRfFQZ5xdfoy95';
			
// 		foreach($getuser as $user){
// 			//var_dump($user->device_token);
// 			if($user->device_token != 'null' && $user->device_token != ''){
				
// 			$arr_deviceId[] = $user->device_token;
// 			$arr_deviceId[] = 'fPO3BQafRAyGjLY8m0_3Iu:APA91bHi91B9ZreFl_Oa2LHcO9OnVI1zUaYZO1-k0XP9wlGNL4QM0tWesvtsi8A2FS4yI9TLkLxkznG3hFJ9_7WzI7U1TFtsvULbdiBhbzIXmovFDXKhmusvyAYbjDQRfFQZ5xdfoy95';
			
// 			$insertdata = new stdclass;
// 			$insertdata->userid = $user->userid;
// 			$insertdata->courseid = $user->courseid;
// 			$insertdata->msgid = 3;
// 			$insertdata->timecreated = time();
			
// 			$DB->insert_record('local_check_puss_notification', $insertdata, $returnid=true, $bulk=false);
			
// 			}
// 		}
// //print_object($arr_deviceId);
	
//  		 $serverKey = 'AAAAzN4I1kg:APA91bH9436jQGZmgNdEarQD3pvj1wBtoREl1WAWKYMuElHyCNqTusywO0nt6DC0UNU5EOfj64b72Q4fAwkd9_q-_pk9byYLVCCyUZhPS39gcpUU0MvI2b2EC3PYBcoHru2_CHT9L2pY';

// 		$arr_messageInfo['subject'] = "Course Completed Alert";
// 		$arr_messageInfo['messageBody'] = " You have not started ".$courseid->fullname. "this course please do !";
// 		$notification = array('title' => $arr_messageInfo['subject'] , 'body' => $arr_messageInfo['messageBody'], 'sound' => 'default'); //, 'badge' => '1'

// 				if(count($arr_deviceId)) {
// 					$arrayToSend = array('registration_ids' => $arr_deviceId, 'notification' => $notification, 'priority' => 'high');//for multiple device

// 					$json = json_encode($arrayToSend);

// 					$headers = array();
// 					$headers[] = 'Content-Type: application/json';
// 					$headers[] = 'Authorization: key='. $serverKey;

// 					$ch = curl_init();
// 					curl_setopt($ch, CURLOPT_URL, $url);
// 					curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
// 					curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
// 					curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
					
// 					//curl_setopt($ch, CURLOPT_VERBOSE, 0);
// 					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// 					//curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to 
// 					//Send the request
// 					$response = curl_exec($ch);
// 					//Close request
// 					if ($response === FALSE) {
// 					die('FCM Send Error: ' . curl_error($ch));
// 					}
// 					curl_close($ch);
// 				}
	
	
// 	}
	


?>