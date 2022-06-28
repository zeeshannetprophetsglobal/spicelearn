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
 * @package    block_course_search
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_completion\progress;

function block_course_search_get_user_courses_by_category($coursename){

    global $USER,$DB;

    $data = array();
    if ($coursename) {

        $sql =   'SELECT DISTINCT c.*,cc.path,cc.id as "categoryid",cc.name as "category_name"  
        FROM {course} c 
        JOIN {enrol} en ON en.courseid = c.id 
        JOIN {course_categories} cc ON cc.id = c.category
        JOIN {user_enrolments} ue ON ue.enrolid = en.id 
        WHERE ue.userid = '.$USER->id.' AND c.visible = 1 AND c.fullname LIKE "%'.$coursename.'%"';
        $data = $DB->get_records_sql($sql,array());
    }
    return $data;
}

function block_course_search_course_status($courseid){

    global $USER,$DB;

    $is_course_complete = block_course_search_is_course_complete($courseid,$USER->id);
    $isnotstarted = $DB->get_record_sql("SELECT userid FROM {logstore_standard_log} WHERE courseid=$courseid and action='viewed' AND target='course' AND userid = $USER->id ORDER BY id DESC LIMIT 1");

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

function block_course_search_is_course_complete($course_id,$userid){

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


function course_search_layer_of_category_name($path,$type){

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

function block_course_search_course_progress($courseid){

    global $USER;

    $courseobject = get_course($courseid);

    return progress::get_course_progress_percentage($courseobject, $USER->id);


}
?>