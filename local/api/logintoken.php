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
require_once($CFG->dirroot . "/local/api/lib.php");

// Allow CORS requests.
header('Access-Control-Allow-Origin: *');

$username = required_param('username', PARAM_USERNAME);
$password = required_param('password', PARAM_RAW);
$serviceshortname  = required_param('service',  PARAM_ALPHANUMEXT);
$device_token  = required_param('device_token',  PARAM_RAW); // For mobile APP
//$group_id  = required_param('group_id',  PARAM_ALPHANUMEXT); // For mobile APP


echo $OUTPUT->header();

if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}
$username = trim(core_text::strtolower($username));
if (is_restored_user($username)) {
    throw new moodle_exception('restoredaccountresetpassword', 'webservice');
}

$systemcontext = context_system::instance();

$reason = null;
$user = authenticate_user_login($username, $password, false, $reason, false);

if (!empty($user)) {
   // print_r($user->id);
    // Cannot authenticate unless maintenance access is granted.
    $hasmaintenanceaccess = has_capability('moodle/site:maintenanceaccess', $systemcontext, $user);
    if (!empty($CFG->maintenance_enabled) and !$hasmaintenanceaccess) {
        throw new moodle_exception('sitemaintenance', 'admin');
    }

    if (isguestuser($user)) {
        throw new moodle_exception('noguest');
    }
    if (empty($user->confirmed)) {
        throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
    }
    // check credential expiry
    $userauth = get_auth_plugin($user->auth);
    if (!empty($userauth->config->expiration) and $userauth->config->expiration == 1) {
        $days2expire = $userauth->password_expire($user->username);
        if (intval($days2expire) < 0 ) {
            throw new moodle_exception('passwordisexpired', 'webservice');
        }
    }

    // let enrol plugins deal with new enrolments if necessary
    enrol_check_plugins($user);

    // setup user session to check capability
    \core\session\manager::set_user($user);

    //check if the service exists and is enabled
    $service = $DB->get_record('external_services', array('shortname' => $serviceshortname, 'enabled' => 1));
    if (empty($service)) {
        // will throw exception if no token found
        throw new moodle_exception('servicenotavailable', 'webservice');
    }

    // Get an existing token or create a new one.
    $token = external_generate_token_for_current_user($service);
    $privatetoken = $token->privatetoken;
    external_log_token_request($token);

    $siteadmin = has_capability('moodle/site:config', $systemcontext, $USER->id);

    $usertoken = new stdClass;
    $usertoken->token = $token->token;
    // Private token, only transmitted to https sites and non-admin users.
    if (is_https() and !$siteadmin) {
        $usertoken->privatetoken = $privatetoken;
    } else {
        $usertoken->privatetoken = null;
    }

    $userpicture = new user_picture($user);
    $userpicture->size = 1; // Size f1.
    $profileimageurl = $userpicture->get_url($PAGE);

    // Site information.
    $userinfo =  array(
        'sitename' => external_format_string($SITE->fullname, $systemcontext),
        'siteurl' => $CFG->wwwroot,
        'username' => $user->username,
		'email' => $user->email,
		'departmentid' => get_user_department_id($user->id),
		'department' => get_user_department_name($user->id),
        'firstname' => $user->firstname,
        'lastname' => $user->lastname,
        'fullname' => fullname($user),
        'lang' => clean_param(current_language(), PARAM_LANG),
        'userid' => (int)$user->id,
        'userpictureurl' => $profileimageurl->out(false),
        'siteid' => SITEID
    );
	
	// ***** Update token and group id
	//echo $device_token;
    $lastaccess = time();
	if($device_token != 'null')
	{
	//echo "UPDATE {user} SET device_token= '{$device_token}' WHERE id = '{$uid}'";
	$uid = $USER->id;
	$DB->execute("UPDATE {user} SET device_token= '{$device_token}', lastaccess =  '{$lastaccess}'  WHERE id = '{$uid}'");
	}
	// Update token and group id
    $usertoken->userinfo = $userinfo;
    $usertoken->statusCode = 'NP01';
    $usertoken->msg = 'get token successfully';
    echo json_encode($usertoken);
} else {

    $usertoken = new stdClass;
    $usertoken->token = 'invalid login';
    
    $usertoken->privatetoken = null;
    
    $usertoken->statusCode = 'NP00';
    $usertoken->msg = 'invalid username and password';
    echo json_encode($usertoken);
}
