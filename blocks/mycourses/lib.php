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
// MERCHANTABIILTY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_mycourses
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_completion\progress;

require_once($CFG->dirroot.'/cohort/lib.php');

defined('MOODLE_INTERNAL') || die;
define('ILT','ILT');
define('Elearn','EL');

function block_mycourses_get_user_courses(){

    global $USER,$DB;
    // enrol_get_all_users_courses($USER->id)
    $cohort =  cohort_get_user_cohorts ($USER->id);
    $cohortdata = array_values($cohort);
    $data = array();
    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='".$cohortdata[0]->idnumber."'");
        if ($parent_cate) {
            $sql =   'SELECT DISTINCT c.*,cc.path,cc.id as "categoryid",cc.name as "category_name"  
            FROM {course} c 
            JOIN {enrol} en ON en.courseid = c.id 
            JOIN {course_categories} cc ON cc.id = c.category
            JOIN {user_enrolments} ue ON ue.enrolid = en.id 
            WHERE ue.userid = '.$USER->id.' AND c.visible = 1 AND cc.visible = 1 AND cc.path LIKE "%'.$parent_cate->id.'%"  AND cc.idnumber LIKE "%ilt%"';

            $data = $DB->get_records_sql($sql,null);
        }
    }
    return $data;
}

function block_mycourses_get_user_courses_by_category($categoryid,$group,$coursename){

    global $USER,$DB;

    
    $cohort =  cohort_get_user_cohorts ($USER->id);
    $cohortdata = array_values($cohort);
    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='".$cohortdata[0]->idnumber."'");
        $parent_cate_id = $parent_cate->id;
    }
    $group = ($group)?$group:'el';

    if ($coursename) {
        $coursename = ' AND c.fullname LIKE "%'.$coursename.'%"';
    }

    if(!empty($categoryid)){

        $sql = 'SELECT DISTINCT c.id, c.fullname, c.summary, c.idnumber,cc.path,cc.id as "categoryid",cc.name as "category_name"  
        FROM {course} c 
        JOIN {enrol} en ON en.courseid = c.id 
        JOIN {course_categories} cc ON cc.id = c.category
        JOIN {user_enrolments} ue ON ue.enrolid = en.id 
        WHERE ue.userid = "'.$USER->id.'" AND c.visible = 1 AND cc.visible = 1 AND cc.path LIKE "%'.$parent_cate_id.'%" AND  c.category='.$categoryid.$coursename;

        $data = $DB->get_records_sql($sql,array());

    }else{

        $sql =   'SELECT DISTINCT c.*,cc.path,cc.id as "categoryid",cc.name as "category_name"  
        FROM {course} c 
        JOIN {enrol} en ON en.courseid = c.id 
        JOIN {course_categories} cc ON cc.id = c.category
        JOIN {user_enrolments} ue ON ue.enrolid = en.id 
        WHERE ue.userid = '.$USER->id.' AND c.visible = 1 AND cc.visible = 1  AND cc.path LIKE "%'.$parent_cate_id.'%"  AND cc.idnumber LIKE "%'.$group.'%"'.$coursename;

        $data = $DB->get_records_sql($sql,array());

    }
    return $data;
}


function block_mycourses_get_user_elearn_courses(){

    global $USER,$DB;

    $cohort =  cohort_get_user_cohorts ($USER->id);
    $cohortdata = array_values($cohort);

    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='".$cohortdata[0]->idnumber."'");
        $sql =   'SELECT DISTINCT c.*,cc.path,cc.id as "categoryid",cc.name as "category_name"  
        FROM {course} c 
        JOIN {enrol} en ON en.courseid = c.id 
        JOIN {course_categories} cc ON cc.id = c.category
        JOIN {user_enrolments} ue ON ue.enrolid = en.id 
        WHERE ue.userid = '.$USER->id.' AND c.visible = 1  AND cc.path LIKE "%'.$parent_cate->id.'%"  AND cc.idnumber LIKE "%el%"';
        // echo $sql;die;
    }
    $data = $DB->get_records_sql($sql,null);

    return $data;
}

function block_mycourses_course_status($courseid){

    global $USER,$DB;

    $is_course_complete = block_mycourses_is_course_complete($courseid,$USER->id);
    // $isnotstarted = $DB->get_record_sql("SELECT userid FROM {logstore_standard_log} WHERE courseid=$courseid and action='viewed' AND target='course' AND userid = $USER->id ORDER BY id DESC LIMIT 1");
    $isnotstarted = $DB->get_record_sql("SELECT id FROM {user_lastaccess} as ul WHERE courseid=$courseid and userid = $USER->id");

        //print_object($isnotstarted);

    $status = 'Inprogress';
    if($is_course_complete){

        $status = 'Completed';

    }else if($isnotstarted){
        $status = 'Inprogress';
    }else{

        $status = 'Not-started';
    }

    return $status;
}

function block_mycourses_is_course_complete($course_id,$userid){

    global $DB, $CFG;
    require_once("{$CFG->libdir}/completionlib.php");
    $course_object = $DB->get_record('course', array('id'=>$course_id));
    $cinfo = new completion_info($course_object);
    $iscomplete = $cinfo->is_course_complete($userid);
    if(!$iscomplete){
        $iscomplete = 0;
    }
    return $iscomplete;
}

function block_mycourses_course_progress($courseid){

    global $USER;

    $courseobject = get_course($courseid);

    return progress::get_course_progress_percentage($courseobject, $USER->id);


}

function block_mycourse_category_filtter_option($type){

    global $USER,$DB;

    $cohort =  cohort_get_user_cohorts ($USER->id);
    $cohortdata = array_values($cohort);
    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='".$cohortdata[0]->idnumber."'");
        $parent_cate_id = 'path LIKE "%'.$parent_cate->id.'%" AND';
    }
    $enrolledcate = $DB->get_records_sql("SELECT cc.id FROM mdl_course_categories as cc JOIN mdl_course as c ON c.category = cc.id JOIN `mdl_context` ct ON ( ct.INSTANCEID = c.ID ) LEFT JOIN `mdl_role_assignments` ra ON ( ra.CONTEXTID = ct.ID ) WHERE ra.userid=$USER->id");

    $categoryids = implode(', ', array_map(function($c) {
        return $c->id;
    }, $enrolledcate));

    $sql = 'SELECT * FROM {course_categories}  WHERE '.$parent_cate_id.'  depth != 2 AND idnumber LIKE "%'.$type.'%"';
    if ($categoryids) {
      $sql = $sql." AND id IN($categoryids)";
    }

    $allcategory = $DB->get_records_sql($sql);
    $option = '';

    foreach($allcategory as $depart){          
        $option .= html_writer::tag('option',layer_of_category_name($depart->path,$type),array('value'=>$depart->id));
    }
    return $option;
}


function layer_of_category_name($path,$type){

    global $DB;   


    $NameExpload = explode("/",$path);
    $subjectdata = array_slice($NameExpload, 3);  


    $count = count($subjectdata);
        // echo $count; 
    $FullNameCategory = '';
    $categorySlash = 0;
    if($count != 0){
        foreach($subjectdata as $data){
            $categorySlash ++;
            $category =  $DB->get_record('course_categories',array('id'=>$data));

            if($count -$categorySlash > 0){
                $FullNameCategory .= $category->name.'/';
            }else{
                $FullNameCategory .= $category->name;
            }

        }
    }
    return $FullNameCategory;
}

?>