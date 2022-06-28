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

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

//$byte_code  = required_param('byte_code',  PARAM_ALPHANUMEXT);
$byte_code  = required_param_array('byte_code',  PARAM_INT);
$token  = required_param('token',  PARAM_ALPHANUMEXT);
$itemid = optional_param('itemid', 0, PARAM_INT);

//echo $CFG->dirroot;
echo $OUTPUT->header();


$webservicelib = new webservice();
$authenticationinfo = $webservicelib->authenticate_user($token);
$fileuploaddisabled = empty($authenticationinfo['service']->uploadfiles);
if ($fileuploaddisabled) {
    throw new webservice_access_exception('Web service file upload must be enabled in external service settings');
}

$context = context_user::instance($USER->id);

$fs = get_file_storage();

$totalsize = 0;
$files = array();


$fs = get_file_storage();

if ($itemid <= 0) {
    $itemid = file_get_unused_draft_itemid();
}


//$byte_array = $byte_code;
$image = implode(array_map('chr',$byte_code));
    //file_put_contents('image.png',$rawPNG);

//$image = base64_decode($image);
$image_name = md5(uniqid($itemid, true));
$filename = $image_name . '.' . 'png';
//rename file name with random number
$path = $CFG->dirroot."/local/api/uploads/".$filename;
//image uploading folder path

file_put_contents($path, $image);
// image is bind and upload to respective folder

$results = array();

	
    $file_record = new stdClass;
    $file_record->component = 'user';
    $file_record->contextid = $context->id;
    $file_record->userid    = $USER->id;
    $file_record->filearea  = 'draft';
    $file_record->filename = $filename;
    $file_record->filepath  = '/';
    $file_record->itemid    = $itemid;
    $file_record->license   = $CFG->sitedefaultlicense;
    $file_record->author    = fullname($authenticationinfo['user']);
    $file_record->source    = serialize((object)array('source' => $filename));

	
	
    //Check if the file already exist
    /*$existingfile = $fs->file_exists($file_record->contextid, $file_record->component, $file_record->filearea,
                $file_record->itemid, $file_record->filepath, $file_record->filename);
    if ($existingfile) {
        $file->errortype = 'filenameexist';
        $file->error = get_string('filenameexist', 'webservice', $file->filename);
        $results[] = $file;
    } else {
        $stored_file = $fs->create_file_from_pathname($file_record, $file->filepath);
        $results[] = $file_record;
    }*/
	//$fs = get_file_storage();
	$results[] = $file_record;
	//echo "<pre>";
	//print_r($file_record);
	//echo $path;
$stored_file = $fs->create_file_from_pathname($file_record, $path);

//$context = context_system::instance();
       // self::validate_context($context);

        /*if (!empty($CFG->disableuserimages)) {
            throw new moodle_exception('userimagesdisabled', 'admin');
        }

        if (empty($params['userid']) or $params['userid'] == $USER->id) {
            $user = $USER;
            require_capability('moodle/user:editownprofile', $context);
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            $personalcontext = context_user::instance($user->id);

            require_capability('moodle/user:editprofile', $personalcontext);
            if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
                throw new moodle_exception('useradmineditadmin');
            }
        }*/

        // Load the appropriate auth plugin.
        /*$userauth = get_auth_plugin($user->auth);
        if (is_mnet_remote_user($user) or !$userauth->can_edit_profile() or $userauth->edit_profile_url()) {
            throw new moodle_exception('noprofileedit', 'auth');
        }*/
//$user = core_user::get_user($USER->id, '*', MUST_EXIST);
       //$context = context_system::instance();
        //self::validate_context($context);

        /*if (!empty($CFG->disableuserimages)) {
            throw new moodle_exception('userimagesdisabled', 'admin');
        }*/

        if (empty($params['userid']) or $params['userid'] == $USER->id) {
            $user = $USER;
            require_capability('moodle/user:editownprofile', $context);
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            $personalcontext = context_user::instance($user->id);

            require_capability('moodle/user:editprofile', $personalcontext);
            if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
                throw new moodle_exception('useradmineditadmin');
            }
        }

        // Load the appropriate auth plugin.
        $userauth = get_auth_plugin($user->auth);
        if (is_mnet_remote_user($user) or !$userauth->can_edit_profile() or $userauth->edit_profile_url()) {
            throw new moodle_exception('noprofileedit', 'auth');
        }

        $filemanageroptions = array('maxbytes' => $CFG->maxbytes, 'subdirs' => 0, 'maxfiles' => 1);
        //$user->deletepicture = $params['delete'];
		//$user->id = $USER->id;
        $user->imagefile = $file_record->itemid;		
        $success = core_user::update_picture($user, $filemanageroptions);

        
        if ($success == true) {
		
            //$userpicture = new user_picture(core_user::get_user($USER->id));
            //$userpicture->size = 1; // Size f1.
			
			$userpictures = new user_picture($user);
            $userpictures->size = 1; // Size f1.
			$profileimageurls = $userpictures->get_url($PAGE);
            //$result['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);
			
			
			  $result = [
				'status' => 'NP01',
				'msg' => 'Profile Picture Updated Successfully',
				'profiledata' => [
					'profileimageurl' => $profileimageurls->out(false),
					'profileimageurl2' => $CFG->wwwroot."/local/api/uploads/".$filename,					
					'warnings' => array(),					
					]
						];
        }
		else
		{
       
		        $result = [
				'status' => 'NP00',
				'msg' => 'Profile Picture Not Updated Successfully',
				'profiledata' => [
					'profileimageurl' => $profileimageurls->out(false),
					'profileimageurl2' => $CFG->wwwroot."/local/api/uploads/".$filename,
					'warnings' => $userdata->lastname,					
					]
						];
		
		}

echo json_encode($result);

?>