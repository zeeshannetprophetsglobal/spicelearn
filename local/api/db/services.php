<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localapi
 * @copyright  Ayush Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_api_test' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'test',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_category_detail' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'category_detail',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return category id, name , image , enroll course count',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_my_course_detail' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'my_course_detail',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_course_content' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'course_content',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_search_mycourse' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'search_mycourse',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_mycourse_by_categoryid' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'mycourse_by_categoryid',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_mycourses_status_data' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'mycourses_status_data',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_get_favourit_data_course' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'get_favourit_course_data',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_get_scorms_by_courses' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'get_scorms_by_courses',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_set_fav_courses' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'set_fav_courses',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return statust code, msg ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_course_completion' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'course_completion',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),		
        'local_api_leaderboard' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'leaderboard',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Leaderboard data ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_leaderboard_by_category' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'leaderboard_by_category',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Leaderboard data ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),

        'local_api_profile' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'profile',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Leaderboard data ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),

        'local_api_profileupdate' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'profileupdate',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Updated Profile Data ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_request_password_reset' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'request_password_reset',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Requests a password reset.',
                'type'        => 'write',
                'ajax'          => true,
                'loginrequired' => false,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_urls' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'urls',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Urls Link',
                'type'        => 'write',
                'ajax'          => true,
                'loginrequired' => false,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),'local_api_game_score' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'game_score',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Update Game score',
                'type'        => 'write',
                'ajax'          => true,
                'loginrequired' => false,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_user_leaderboard' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'user_leaderboard',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'User Leaderboard Game score',
                'type'        => 'write',
                'ajax'          => true,
                'loginrequired' => false,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_user_picture_remove' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'user_picture_remove',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'User Leaderboard Game score',
                'type'        => 'write',
                'ajax'          => true,
                'loginrequired' => false,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_activity_completion_status_manually' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'activity_completion_status_manually',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return true ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_category_typedata' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'category_typedata',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return true ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),

        //added by zeeshan 31-03-2022
        'local_get_certificate' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'custom_get_certificate',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'return certificate data from course',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_category_subtypedata' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'category_subtypedata',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return true ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),

        'local_ilt_course_content' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'ilt_course_content',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_category_maintypedata' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'category_maintypedata',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return course id, fullname ,shortname,visible,shortid,lastaccess,courseprogress image ',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_quiz_questions' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'get_quiz_questions',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Quiz questions',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_quiz_saveanswers' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'submit_quiz_saveanswers',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Return Quiz questions',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),

        'local_api_user_role' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'get_user_role',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Get user role in course',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),

        'local_api_enrolled_user' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'get_enrolled_user',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Get enrolled user in course',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_recourse_view_completion' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'recourse_view_completion',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Recourse view completion',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_mod_attendance_add_session' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'local_mod_attendance_add_session',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Recourse view completion',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_get_courseid' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'local_get_courseid',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Get course id from scrom id',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        'local_api_get_originalcourseid' => array(
                'classname'   => 'local_api_external',
                'methodname'  => 'local_get_originalcourseid',
                'classpath'   => 'local/api/externallib.php',
                'description' => 'Get originalcourseid',
                'type'        => 'read',
                'ajax'          => true,
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
        ),
        


);