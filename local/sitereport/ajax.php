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
require_once($CFG->dirroot.'/local/sitereport/locallib.php');

defined('MOODLE_INTERNAL') || die;

global $DB;

$action = optional_param('action',null,PARAM_TEXT);
$base = optional_param('base',null,PARAM_TEXT);
$zone = optional_param('zone',null,PARAM_TEXT);
$rank = optional_param('rank',null,PARAM_TEXT);
$l1 = optional_param('l1',null,PARAM_TEXT);
$l2 = optional_param('l2',null,PARAM_TEXT);
$page = optional_param('page',0,PARAM_INT);
$dpt_id = optional_param('dpt_id',0,PARAM_INT);
$type = optional_param('type',0,PARAM_INT);


$renderer = $PAGE->get_renderer('local_sitereport');

if($action == 'option'){   
        
    if($type == 1){

        $sql = "SELECT cc.* FROM {course_categories} as cc JOIN {course} as c ON c.category=cc.id WHERE cc.depth NOT IN(1,2) AND cc.path LIKE '%".$dpt_id."%' AND cc.idnumber LIKE '%ILT%'";
    }else{
        $sql = "SELECT cc.* FROM {course_categories} as cc JOIN {course} as c ON c.category=cc.id WHERE cc.depth NOT IN(1,2) AND cc.path LIKE '%".$dpt_id."%' AND cc.idnumber LIKE '%EL%'";
    }

   $optiondata =  $DB->get_records_sql($sql,null);

   //print_object($optiondata);
   $option = '<option value="">Select subject</option>';
   foreach($optiondata as $data){
    if ($data->path) {
        $option .= '<option value="'.$data->id.'">'. get_subject($data->path).'</option>';
    }

   }
   echo $option;
}