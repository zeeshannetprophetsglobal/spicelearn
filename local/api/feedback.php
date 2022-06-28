<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Return token
 * @package    moodlecore
 * @copyright  2011 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_analytics\output\invalid_analysables;
define('AJAX_SCRIPT', true);
define('REQUIRE_CORRECT_ACCESS', true);
define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once("$CFG->libdir/gdlib.php");
require_once($CFG->dirroot . "/local/api/lib.php");
$userid = required_param('userid',  PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$category = required_param('category',  PARAM_TEXT);
$comment = required_param('comment',  PARAM_TEXT);
$byte_code = optional_param('byte_code', 0 ,PARAM_ALPHANUMEXT);
$itemid = optional_param('itemid', 0, PARAM_INT);

echo $OUTPUT->header();
 //****** 15 APRIL 2021 Feedback
if($courseid != 0)
{
$couse_name = get_course_name($courseid);
}
else{
$couse_name = "N/A";
}



$userdata = $DB->get_record('user', array('id' => $userid));
$context = context_user::instance($userid);
$fs = get_file_storage();
$files = array();
/*)if ($itemid <= 0) {
$itemid = file_get_unused_draft_itemid();
}*/
$image = implode(array_map('chr',$byte_code));
$image_name = md5(uniqid($itemid, true));
$filename = $image_name . '.' . 'png';
$path = $CFG->dirroot."/local/api/feedback/".$filename;
file_put_contents($path, $image);
//*** Mail Body
// Recipient 
$to = 'shrey@gamingcentral.in,spicelearn@spicejet.com,shubham@gamingcentral.in,sanjeev.kumar@netprophetsglobal.com';  
// Sender 
$from = $userdata->email;
$fromName = $userdata->firstname; 
// Email subject 
$subject = 'Feedback From SpiceLearn';  
// Email body content 
$htmlContent = ' 
    <p>Student Name: '.$userdata->firstname.'</p>
	<p>Student Email Id: '.$userdata->email.'</p>
    <p>Feedback Type: '.$category.'</p>
	<p>Feedback Course Name: '.$couse_name.'</p> 
	<p>Comment: '.$comment.'</p>';
 
// Header for sender info 
$headers = "From: $fromName"." <".$from.">"; 
 
// Boundary  
$semi_rand = md5(time());  
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
 
// Headers for attachment  
$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
 
// Multipart boundary  
$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
"Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";  
 
// Preparing attachment 
if(!empty($path) > 0 && $byte_code != 0){ 
    if(is_file($path)){ 
        $message .= "--{$mime_boundary}\n"; 
        $fp =    @fopen($path,"rb"); 
        $data =  @fread($fp,filesize($path));  
        @fclose($fp); 
        $data = chunk_split(base64_encode($data)); 
        $message .= "Content-Type: application/octet-stream; name=\"".basename($path)."\"\n" .  
        "Content-Description: ".basename($path)."\n" . 
        "Content-Disposition: attachment;\n" . " filename=\"".basename($path)."\"; size=".filesize($path).";\n" .  
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
    } 
} 
$message .= "--{$mime_boundary}--"; 
$returnpath = "-f" . $from;  
// Send email 
$response = [];
$mail = @mail($to, $subject, $message, $headers, $returnpath);  
		//****** 15 APRIL 2021
        if($mail == true){
		// Capture Feedback Data.
        $data = new stdClass();
        $data->userid = $userid;
        $data->courseid = $courseid;
        $data->feedback_comment = $comment;
        $data->feedback_category = $category;
        $data->attachment = $filename;      
        $data->id = $DB->insert_record('feedback_mobile', $data);	
			
	    $response = [           
            'statusCode' => 'NP01',
            'msg' => 'Feedback email sent successfully.'
        ];
       
        }else{
	
	    $response = [                    
            'statusCode' => 'NP00',
            'msg' => 'There is something wrong!'
        ];       
    }
echo json_encode($response);
?>