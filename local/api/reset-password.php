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
$email  = required_param('email',  PARAM_RAW);
require_once($CFG->dirroot . '/login/lib.php');
//$warnings = array();
//echo $CFG->dirroot;
echo $OUTPUT->header();// Check if an alternate forgotten password method is set.
        if (!empty($CFG->forgottenpasswordurl)) {
            throw new moodle_exception('cannotmailconfirm');
        }		
            list($status, $notice, $url) = core_login_process_password_reset(null,$email);
        
		if($status == 'emailpasswordconfirmmaybesent')
		{
        $res = array(
            'statusCode' => "NP01",
            'msg' => strip_tags(str_replace("\n","",$notice))          
        );
		}
		else
		{	
        $res = array(
            'statusCode' => "NP00",
            'msg' => strip_tags(str_replace("\n","",$notice))
            
        );
		}
echo json_encode($res);
?>