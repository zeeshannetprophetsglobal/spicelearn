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



require_once('../../config.php');
// require_once("$CFG->libdir/lib/datalib.php");

defined('MOODLE_INTERNAL') || die;

function department(){

    global $DB;
    $sql = 'SELECT * FROM  {course_categories} WHERE visible=1 AND depth = 1';
    $departmentList = $DB->get_records_sql($sql,null);
    return $departmentList;
}

function get_ilt_training_type($categotryid){
    global $DB;
    $sql = 'SELECT cc.* FROM {course_categories} as cc WHERE visible=1 AND
    cc.path LIKE "/'.$categotryid.'/%" /* AND cc.depth = "3" */ AND cc.idnumber LIKE "ILT%"';
    $trainingType = $DB->get_records_sql($sql,null);
    // echo "<pre>"; print_r($trainingType);die;
    return $trainingType;
}

function get_iltcourse($categotryid,$filterData){

    global $DB;

    $sql = 'SELECT c.* FROM {course} as c WHERE c.visible=1 AND c.category = "'.$categotryid.'"'; 
    if(!empty($filterData['start_date'])){
        
        $sql .= ' AND (c.startdate BETWEEN "'.$filterData["start_date"].'" AND "'.$filterData["end_date"].'")';
    }
    // echo $sql;
    $coursedata = $DB->get_records_sql($sql);
    return count($coursedata);
}

/*
* This function is not in use after change request
*/
function get_enrol_user($categoryId,$filterData){

    global $DB;
    $totaluser = [];    
    $count = 0;
    $courseData = get_iltcourse($categoryId,$filterData);
    // echo "<pre>";print_r($courseData);die;
    foreach($courseData as $course){
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $context = CONTEXT_COURSE::instance($course->id);
        $student = get_role_users($role->id, $context);
        foreach ($student as $key => $value) {
            $count ++;
        }
    }
    return  $count;

}

function get_category_parent_name($category){
    
    global $DB;
    $sql = 'SELECT * FROM  {course_categories} WHERE id = "'.$category.'"';
    $categoryData = $DB->get_records_sql($sql,null);
    $name = $categoryData[$category]->name;
    if($categoryData[$category]->depth < 4){
        return $name;
    }else{
        return $name = get_category_parent_name($categoryData[$category]->parent) . '/'.$name;
    }
    
}

