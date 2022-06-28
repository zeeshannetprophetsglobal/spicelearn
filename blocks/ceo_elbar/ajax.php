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
 * Block displaying information about current logged-in user.
 *
 * This block can be used as anti cheating measure, you
 * can easily check the logged-in user matches the person
 * operating the computer.
 *
 * @package    block_ceo_elpiechart
 * @author     Ayush <aayush.yahoo@gmail.com>
 */
global $CFG,$OUTPUT,$DB;
require_once('../../config.php');

defined('MOODLE_INTERNAL') || die;

$category = optional_param('category',null,PARAM_TEXT);
$action = optional_param('action',null,PARAM_TEXT);
$category = optional_param('category',null,PARAM_TEXT);

$subjecthtml = '<option value="">All</option>';
if ($action == 'pie_chart') {

    if ( !empty($category)) {
        
        $sql = 'SELECT cc.* FROM {course_categories} as cc JOIN {course} as c on c.category=cc.id WHERE c.visible=1 AND cc.path LIKE "/'.$category.'/%" AND cc.idnumber LIKE "el%"';
        $trainingType = $DB->get_records_sql($sql,null);

        $CourseArray = array();
        foreach($trainingType as $dpt){
                $subjecthtml .= '<option value="'.$dpt->id.'">'.get_category_parent_name($dpt->id).'</option>';
        }

    }

    echo $subjecthtml;die;
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