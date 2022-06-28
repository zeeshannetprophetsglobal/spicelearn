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
 * @package local_scheduler
 * @author zeeshan khan <zeeshankhan08467@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2017, onwards Poet
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Local scheduler event handler.
 */
use core_completion\progress;

class local_scheduler_observer {

    // public static function local_course_created(\core\event\course_created $event) {
    //     global $CFG, $DB;
    //     $data = $event->get_data();
    //     $category = $DB->get_record_sql("SELECT cc.id,cc.name,cc.path,c.enddate FROM {course} as c JOIN {course_categories} as cc on c.category=cc.id WHERE c.id=?",array($data['courseid']));

    //     $rec = new stdClass();          
    //     $rec->course_id = $data['courseid'];
    //     $rec->course_name = $data['other']['fullname'];
    //     $rec->course_end_date = $category->enddate;
    //     $rec->category_id = $category->id;
    //     $rec->category_name = $category->name;
    //     $rec->category_path = $category->path;
    //     $rec->timecreated = time();
    //     $rec->timemodified = time();
    //     // echo "<pre>";print_r($rec);die;
    //     $run = $DB->insert_record('local_scheduler',$rec);
    // }
    // public static function local_user_enrolment_created(\core\event\user_enrolment_created $event) {
    //     global $DB;
    //     $data = $event->get_data();
    //     // echo "<pre>";print_r($data);die;
    //     $scheduler_check = $DB->get_record_sql("SELECT * FROM {local_scheduler} as ls WHERE course_id=?",array($data['courseid']));

    //     if ($scheduler_check) { 


    //         $userdata = $DB->get_records_sql("SELECT uid.*,uif.shortname,u.username,u.firstname,u.lastname,u.email,ch.name as department FROM {user} as u JOIN {user_info_data} as uid ON u.id=uid.userid JOIN {user_info_field} as uif ON uid.fieldid=uif.id LEFT JOIN {cohort_members} as cm on u.id=cm.userid LEFT JOIN {cohort} as ch ON ch.id=cm.cohortid WHERE u.id=?",array($data['relateduserid']));
    //         $designation = NULL;
    //         $zone = '';
    //         $L1 = '';
    //         $l2 = '';
    //         foreach ($userdata as $key => $value) {

    //             $username = $value->username;
    //             $firstname = $value->firstname;
    //             $lastname = $value->lastname;
    //             $email = $value->email;
    //             $department = $value->department;

    //             if ($value->shortname == 'designation') {
    //                 $designation = $value->data;
    //             }elseif($value->shortname == 'zone'){
    //                 $zone = $value->data;
    //             }elseif($value->shortname == 'L1'){
    //                 $L1 = $value->data;
    //             }elseif($value->shortname == 'l2'){
    //                 $l2 = $value->data;
    //             }
    //         }

    //         $schedulerdata = $DB->get_record_sql("SELECT ls.id,ls.userid FROM {local_scheduler} as ls WHERE course_id=?",array($data['courseid']));

    //         $scheduler_check->userid = $data['relateduserid'];
    //         $scheduler_check->username = $username;
    //         $scheduler_check->fullname = $firstname.' '.$lastname;
    //         $scheduler_check->email = $email;
    //         $scheduler_check->enrollment_date = $data['timecreated'];
    //         $scheduler_check->userstate = 'active';
    //         $formdata->course_status = 'notstarted';
    //         $scheduler_check->rank1 = $designation;
    //         $scheduler_check->department = $department;
    //         $scheduler_check->zone = $zone;
    //         $scheduler_check->l1 = $L1;
    //         $scheduler_check->l2 = $l2;
    //         $scheduler_check->timemodified = time();

    //         $run = $DB->insert_record('local_scheduler',$scheduler_check);

    //     }
    // }

    // public static function local_user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
    //     global $DB;
    //     $data = $event->get_data();
    //     $DB->delete_records('local_scheduler', array('course_id' => $data['courseid'],'userid'=>$data['relateduserid']));
    //     // echo "<pre>";print_r($data);die;
    // }
    // public static function local_course_viewed(\core\event\course_viewed $event) {
    //     global $CFG,$DB;
    //     require_once("{$CFG->libdir}/completionlib.php");
    //     $data = $event->get_data();

    //     $schedulerdata = $DB->get_record_sql("SELECT ls.id,ls.started_date FROM {local_scheduler} as ls WHERE course_id=? AND userid=?",array($data['courseid'],$data['userid']));
    //     if ($schedulerdata) {
    //         $courseobject = get_course($data['courseid']);
    //         $progress = progress::get_course_progress_percentage($courseobject, $data['userid']);

    //         $formdata->id = $schedulerdata->id;
    //         $formdata->progress = $progress;
    //         $formdata->started_date = $schedulerdata->started_date;
    //         $formdata->course_status = 'inprogress';
    //         $formdata->timemodified = time();
    //         $run = $DB->update_record('local_scheduler',$formdata);
    //     }else{
    //         $schedulerdata = $DB->get_record_sql("SELECT ls.id FROM {local_scheduler} as ls WHERE course_id=?",array($data['courseid']));

    //     }  

    // }
    // public static function local_course_completed(\core\event\course_completed $event) {
    //     global $CFG,$DB;
    //     $data = $event->get_data();

    //     $sql4 = 'SELECT * FROM {course_completions} as cc WHERE cc.course="'.$data['courseid'].'" and cc.userid="'.$data['relateduserid'].'" and timecompleted is not Null';
    //     $iscomplete = $DB->get_record_sql($sql4);

    //     if ($iscomplete) {
    //         $schedulerdata = $DB->get_record_sql("SELECT ls.id FROM {local_scheduler} as ls WHERE course_id=? AND userid=?",array($data['courseid'],$data['relateduserid']));

    //         $formdata = new stdClass(); 
    //         $formdata->id = $schedulerdata->id;
    //         $formdata->completion_date = $iscomplete->timecompleted;
    //         $formdata->course_status = 'completed';
    //         $formdata->timemodified = time();
    //         $run = $DB->update_record('local_scheduler',$formdata);
    //     }
    // }
    // public static function local_user_graded(\core\event\user_graded $event) {
    //     global $CFG, $DB;
    //     $data = $event->get_data();

    //     $schedulerdata = $DB->get_record_sql("SELECT ls.id FROM {local_scheduler} as ls WHERE course_id=? AND userid=?",array($data['courseid'],$data['relateduserid']));
    //     if ($schedulerdata) {
    //         $formdata = new stdClass(); 
    //         $formdata->id = $schedulerdata->id;
    //         $formdata->grade = $data['other']['finalgrade'];
    //         $formdata->timemodified = time();
    //         $run = $DB->update_record('local_scheduler',$formdata);
    //     }
    // }
    // public static function local_attempt_submitted(\mod_quiz\event\attempt_submitted $event) {
    //     global $CFG, $DB;
    //     $data = $event->get_data();
    //     $sql2 = 'SELECT g.userid AS userid,gi.courseid as courseid,g.finalgrade,g.rawgrademax FROM mdl_grade_grades g JOIN mdl_grade_items gi ON g.itemid = gi.id where gi.itemtype = "mod" and gi.itemmodule = "quiz" and gi.courseid="'.$data['courseid'].'" and g.userid="'.$data['relateduserid'].'" and gi.itemname like "%final%"';
    //     $quizdata = $DB->get_record_sql($sql2);

    //     $schedulerdata = $DB->get_record_sql("SELECT ls.id FROM {local_scheduler} as ls WHERE course_id=? AND userid=?",array($data['courseid'],$data['relateduserid']));
    //     if ($quizdata and $schedulerdata) {
    //         $formdata = new stdClass(); 
    //         $formdata->id = $schedulerdata->id;
    //         $formdata->quiz_score = $quizdata->finalgrade;
    //         $formdata->quiz_score_max = $quizdata->rawgrademax;
    //         $formdata->timemodified = time();
    //         $run = $DB->update_record('local_scheduler',$formdata);
    //     }
    // }

    // public static function local_course_deleted(\core\event\course_deleted $event) {
    //     global $CFG, $DB;
    //     $data = $event->get_data();
    //     $DB->delete_records('local_scheduler', array('course_id' => $data['courseid']));
    // }

    // public static function local_course_backup_created(\core\event\course_backup_created $event) {
    //     global $CFG, $DB;
    //     $data = $event->get_data();
    //     $category = $DB->get_record_sql("SELECT cc.id,cc.name,cc.path,c.enddate,c.fullname FROM {course} as c JOIN {course_categories} as cc on c.category=cc.id WHERE c.id=?",array($data['courseid']));

    //     $rec = new stdClass();          
    //     $rec->course_id = $data['courseid'];
    //     $rec->course_name = $category->fullname;
    //     $rec->course_end_date = $category->enddate;
    //     $rec->category_id = $category->id;
    //     $rec->category_name = $category->name;
    //     $rec->category_path = $category->path;
    //     $rec->timecreated = time();
    //     $rec->timemodified = time();
    //     // echo "<pre>";print_r($rec);die;
    //     $run = $DB->insert_record('local_scheduler',$rec);
    // }

    // public static function local_user_updated(\core\event\user_updated $event) {
    //     global $CFG, $DB;
    //     $data = $event->get_data();
    //     // echo "<pre>";print_r($data);die;
    // }
}
