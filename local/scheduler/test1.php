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

// defined('MOODLE_INTERNAL') || die();


require_once('../../config.php');

defined('MOODLE_INTERNAL') || die;

require_login();
global $USER,$CFG,$DB;

use core_completion\progress;

// $context = get_context_instance(CONTEXT_COURSE, 671, true);
// $roles = get_user_roles($context, $USER->id, true);
// echo "<pre>";print_r($roles);
// die;



function get_course_copy_capabilities2(){
	return array('moodle/backup:backupcourse', 'moodle/restore:restorecourse', 'moodle/course:view', 'moodle/course:create');
}

$coursecontext = \context_course::instance(671);

$test = has_all_capabilities(get_course_copy_capabilities2(), $coursecontext);

echo "<pre>";print_r($test);die;

function local_get_quizdata(){
	global $DB;
	$quiz_sql = 'SELECT g.userid,gi.courseid,g.finalgrade,g.rawgrademax 
	FROM mdl_grade_grades g 
	JOIN mdl_grade_items gi ON g.itemid = gi.id 
	WHERE gi.itemtype = "mod" and gi.itemmodule = "quiz" and gi.itemname like "%final%"';
	$quizdata = $DB->get_records_sql($quiz_sql);
	return $quizdata;
}

// $quizdata = local_get_quizdata();
// echo "<pre>";print_r($quizdata);


function get_progressdata(){
	global $DB;
	$sql = "SELECT ra.id,u.id AS userid,
	c.id AS course_id,
	(
		(100 / (SELECT COUNT(*) FROM mdl_course_modules cm WHERE cm.course = c.id) ) * 
		(SELECT COUNT(cmc.id) FROM mdl_course_modules_completion cmc
			LEFT JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
			WHERE cmc.userid = u.id AND cm.course = c.id)
		)AS course_progress
	FROM mdl_user u
	JOIN mdl_user_enrolments ue ON ue.userid=u.id
	JOIN mdl_enrol e ON e.id=ue.enrolid
	JOIN mdl_course c ON c.id = e.courseid
	JOIN mdl_context AS ctx ON ctx.instanceid = c.id
	JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
	JOIN mdl_role AS r ON r.id = e.roleid
	WHERE ra.userid=u.id
	AND ctx.instanceid=c.id
	AND ra.roleid='5'
	AND c.visible='1' GROUP BY u.id,c.id";
	$progressdata = $DB->get_records_sql($sql);
	return $progressdata;
}

// $progressdata = get_progressdata();
// echo "<pre>";print_r($progressdata);

function local_get_userdata(){
	global $DB;
	$sql = "SELECT u.id,uif.shortname,uid.data,ch.name as department FROM mdl_user as u JOIN mdl_user_info_data as uid ON u.id=uid.userid JOIN mdl_user_info_field as uif ON uid.fieldid=uif.id LEFT JOIN mdl_cohort_members as cm on u.id=cm.userid LEFT JOIN mdl_cohort as ch ON ch.id=cm.cohortid ";
	$userdata = $DB->get_records_sql($sql);
	return $userdata;
}

// $userdata = local_get_userdata();
// echo "<pre>";print_r($userdata);

function local_get_cate_data(){
	global $DB;
	$sql = 'SELECT cc.id FROM  {course_categories} as cc WHERE cc.visible=1';
	$categoryData = $DB->get_records_sql($sql,null);
	$cate_data = array();
	foreach ($categoryData as $key => $value) {
		$value->category_path = get_category_parent_name($value->id);
		$cate_data[] = $value;
	}
	return $cate_data;
}
function get_category_parent_name($category){

	global $DB;
	$sql = 'SELECT * FROM  {course_categories} WHERE id = "'.$category.'"';
	$categoryData = $DB->get_records_sql($sql,null);
	$name = $categoryData[$category]->name;
	if($categoryData[$category]->depth < 3){
		return $name;
	}else{
		return $name = get_category_parent_name($categoryData[$category]->parent) . '/'.$name;
	}
}

// $cate_data = local_get_cate_data();
// echo "<pre>";print_r($cate_data);


// $sql = 'SELECT ls.id,ls.userid,ls.course_id,ls.grade FROM {local_scheduler} as ls WHERE ls.userid is not null and ls.grade is null';
// $users = $DB->get_records_sql($sql,null);
// foreach ($users as $key => $userdata) {
//         $checkgrade = $DB->get_record_sql("SELECT gg.finalgrade FROM {grade_items} as gi JOIN {grade_grades} as gg ON gi.id=gg.itemid WHERE gi.itemtype='course' AND gg.userid=$userdata->userid AND  courseid=$userdata->course_id");
//         if ($checkgrade->finalgrade) {
//             $formdata = new stdClass(); 
//             $formdata->id = $userdata->id;
//             $formdata->grade = $checkgrade->finalgrade;
//             $formdata->timemodified = date('d/m/Y',time());
//             $run = $DB->update_record('local_scheduler',$formdata);
//             echo $run;
//             // echo "<pre>";print_r($formdata);die;

//         }
// }


// function get_category_parent_name($category){

//     global $DB;
//     $sql = 'SELECT * FROM  {course_categories} WHERE id = "'.$category.'"';
//     $categoryData = $DB->get_records_sql($sql,null);
//     $name = $categoryData[$category]->name;
//     if($categoryData[$category]->depth < 3){
//         return $name;
//     }else{
//         return $name = get_category_parent_name($categoryData[$category]->parent) . '/'.$name;
//     }
// }


// $check = $DB->get_records_sql("SELECT ls.course_id FROM mdl_local_scheduler as ls JOIN mdl_course as c ON c.id=ls.course_id WHERE ls.course_id NOT IN(SELECT course_id FROM mdl_local_scheduler WHERE userid is not null and course_id is not null) GROUP BY ls.course_id");

// foreach ($check as $key => $value) {

// 	$schedulerdata = $DB->get_record('local_scheduler',array('course_id'=>$value->course_id));
// 	$coursecentext = context_course::instance($value->course_id);
// 	$enrol_users = get_enrolled_users($coursecentext);
// 	if ($enrol_users) {
// 		foreach ($enrol_users as $key => $enrol_user) {
// 			$checkuser = $DB->get_record_sql("SELECT * FROM {local_scheduler} as ls WHERE course_id=? AND userid=?",array($value->course_id,$enrol_user->id));
// 			if (!$checkuser) {
// 				$userdata = $DB->get_records_sql("SELECT uid.*,uif.shortname,u.username,u.firstname,u.lastname,u.email,ch.name as department FROM {user} as u JOIN {user_info_data} as uid ON u.id=uid.userid JOIN {user_info_field} as uif ON uid.fieldid=uif.id LEFT JOIN {cohort_members} as cm on u.id=cm.userid LEFT JOIN {cohort} as ch ON ch.id=cm.cohortid WHERE u.id=?",array($enrol_user->id));
// 				$designation = NULL;
// 				$zone = '';
// 				$L1 = '';
// 				$l2 = '';
// 				foreach ($userdata as $key => $value2) {

// 					$username = $value2->username;
// 					$firstname = $value2->firstname;
// 					$lastname = $value2->lastname;
// 					$email = $value2->email;
// 					$department = $value2->department;

// 					if ($value2->shortname == 'designation') {
// 						$designation = $value2->data;
// 					}elseif($value2->shortname == 'zone'){
// 						$zone = $value2->data;
// 					}elseif($value2->shortname == 'L1'){
// 						$L1 = $value2->data;
// 					}elseif($value2->shortname == 'l2'){
// 						$l2 = $value2->data;
// 					}
// 				}


// 				$is_course_complete = local_is_course_complete($value->course_id,$enrol_user->id);
// 				$isnotstarted = $DB->get_record_sql("SELECT id FROM {user_lastaccess} as ul WHERE courseid=$value->course_id and userid = $enrol_user->id");

// 				if($is_course_complete){
// 					$course_status = 'complete';
// 				}else if($isnotstarted){
// 					$course_status = 'inprogress';
// 				}else{
// 					$course_status = 'notstarted';
// 				}

// 				$courseobject = get_course($value->course_id);
// 				$progress = progress::get_course_progress_percentage($courseobject, $enrol_user->id);

// 				$checkgrade = $DB->get_record_sql("SELECT gg.finalgrade FROM {grade_items} as gi JOIN {grade_grades} as gg ON gi.id=gg.itemid WHERE gi.itemtype='course' AND gg.userid=$enrol_user->id AND  courseid=$value->course_id");

// 				$sql2 = 'SELECT g.userid AS userid,gi.courseid as courseid,g.finalgrade,g.rawgrademax FROM mdl_grade_grades g JOIN mdl_grade_items gi ON g.itemid = gi.id where gi.itemtype = "mod" and gi.itemmodule = "quiz" and gi.courseid="'.$value->course_id.'" and g.userid="'.$enrol_user->id.'" and gi.itemname like "%final%"';
// 				$quizdata = $DB->get_record_sql($sql2);

// 				$schedulerdata->userid = $enrol_user->id;
// 				$schedulerdata->username = $username;
// 				$schedulerdata->fullname = $firstname.' '.$lastname;
// 				$schedulerdata->email = $email;
// 				$schedulerdata->enrollment_date = $enrol_user->timecreated;
// 				$schedulerdata->userstate = 'active';
// 				$schedulerdata->course_status = $course_status;
// 				$schedulerdata->progress = $progress;
// 				$schedulerdata->grade = $checkgrade->finalgrade;
// 				$schedulerdata->quiz_score = $quizdata->finalgrade;
// 				$schedulerdata->quiz_score_max = $quizdata->rawgrademax;
// 				$schedulerdata->rank1 = $designation;
// 				$schedulerdata->department = $department;
// 				$schedulerdata->zone = $zone;
// 				$schedulerdata->l1 = $L1;
// 				$schedulerdata->l2 = $l2;
// 				$schedulerdata->timemodified = time();
// 				// echo "<pre>";print_r($schedulerdata);die;
// 				$run = $DB->insert_record('local_scheduler',$schedulerdata);
// 				echo $run;
// 			}
// 		}
// 	}
// }

// function local_is_course_complete($course_id,$userid){

// 	global $DB, $CFG;
// 	require_once("{$CFG->libdir}/completionlib.php");
// 	$course_object = $DB->get_record('course', array('id'=>$course_id));
// 	$cinfo = new completion_info($course_object);
// 	$iscomplete = $cinfo->is_course_complete($userid);
// 	if(!$iscomplete){
// 		$iscomplete = 0;
// 	}
// 	return $iscomplete;
// }


// $courses = $DB->get_records_sql("SELECT id FROM mdl_course");

// foreach ($courses as $key => $value) {
// 		if ($value->id !=1) {
// 	        $category = $DB->get_record_sql("SELECT cc.id,cc.name,cc.path,c.enddate,c.startdate,c.fullname FROM {course} as c JOIN {course_categories} as cc on c.category=cc.id WHERE c.id=?",array($value->id));

// 	        $rec = new stdClass();          
// 	        $rec->course_id = $value->id;
// 	        $rec->course_name = $category->fullname;
// 	        $rec->course_startdate = $category->startdate;
// 	        $rec->course_end_date = $category->enddate;
// 	        $rec->category_id = $category->id;
// 	        $rec->category_name = $category->name;
// 	        $rec->category_path = $category->path;
// 	        $rec->timecreated = time();
// 	        $rec->timemodified = time();
// 	        // echo "<pre>";print_r($rec);die;
// 	        $run = $DB->insert_record('local_scheduler',$rec);
// 	        echo $run;
// 		}
// }



//update date format in mysql 

// $schedulerdata = $DB->get_records_sql("SELECT * FROM mdl_local_scheduler");
// foreach ($schedulerdata as $key => $value) {
// 	$course = $DB->get_record('course',array('id'=>$value->course_id),'startdate,enddate');

// 	$enrollment_date = str_replace('/', '-', $value->enrollment_date);
// 	$completion_date = str_replace('/', '-', $value->completion_date);
// 	$timecreated = str_replace('/', '-', $value->timecreated);
// 	$timemodified = str_replace('/', '-', $value->timemodified);

// 	$formdata = new stdClass(); 
// 	$formdata->id = $value->id;
// 	$formdata->course_startdate = $course->startdate;
// 	$formdata->course_end_date = $course->enddate;
// 	$formdata->enrollment_date = strtotime($enrollment_date);
// 	$formdata->completion_date = strtotime($completion_date);
// 	$formdata->timecreated = strtotime($timecreated);
// 	$formdata->timemodified = strtotime($timemodified);
// 	$run = $DB->update_record('local_scheduler',$formdata);
// 	echo $run;
// 	// echo "<pre>";print_r($value);print_r($formdata); die;
// }