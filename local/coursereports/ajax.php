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
 * @package    local_coursereports
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_once($CFG->dirroot . '/local/coursereports/lib.php');
require_once($CFG->libdir ."/completionlib.php");

defined('MOODLE_INTERNAL') || die();

global $DB,$CFG,$PAGE,$OUTPUT;

$context = context_system::instance();


$PAGE->set_context($context);

$action = optional_param('action',null,PARAM_RAW);
$courseid = optional_param('courseid',0,PARAM_INT);
$page = optional_param('page',0,PARAM_INT);
$cohort = optional_param('cohort',0,PARAM_INT);
$name = optional_param('name',null,PARAM_RAW);
$email = optional_param('email',null,PARAM_RAW);

$renderer = $PAGE->get_renderer('local_coursereports');

if($action == 'leaderboard'){

  $search = array('name'=>$name,'email'=>$email);

    echo $renderer->leaderboard_data($page,$cohort,$search);  


}elseif($action == 'userwisereport'){

  $search = array('name'=>$name,'email'=>$email);

  echo $renderer->userwise_data($page,$cohort,$search);  
  
}elseif($action == 'coursereport'){

    $html = '';
		
		
    // table data

   $table = new html_table();
   /* Styling done using HTML table and CSS */
           $table->attributes['class'] = 'table generaltable';
          // $table->align = array('center', 'center', 'center', 'center','center', 'center', 'center', 'center','center');
   //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
   $table->size = array('5%', '35%', '15%','15%','15%','15%');

           $table->data = array();
          
          
           $table->head = array(
                                   get_string('sn', 'local_coursereports'),
                                   get_string('coursename', 'local_coursereports'), 
                                  // get_string('department', 'local_coursereports'), 
                                   get_string('completed','local_coursereports'),                                      
                                   get_string('completedpercentage', 'local_coursereports'),                                        
                                   get_string('userinprogress','local_coursereports'), 
                                   get_string('totalenrollment', 'local_coursereports')                                        
                                 
                                 );
   
           $row = array();
           if($courseid){
                   $CourseData = $DB->get_records('course',array('id'=>$courseid));
           }else{
           $CourseData = local_coursereports_get_coursedata($page);
           }
           if($page){
               $sn = $page*COURSEREPORTS_PAGE_LIMIT;
           }else{
               $sn = 1;
           }
             foreach($CourseData as $course){
                 
              $context = context_course::instance($course->id);
              
              if($course->id != 1){
                  
                 $is_courseComplete = local_coursereports_get_course_complete_count($course->id);
                  
                   $row[0] = $sn;                       
                   $row[1] = $course->fullname;
                  // $row[2] = '';
                   $row[3] = $is_courseComplete->complete;
                   
                   $completedpercentage = $is_courseComplete->complete/count_enrolled_users($context, $withcapability = '', $groupid = 0)*100;
                   
                   $row[4] =   number_format((float)$completedpercentage, 2, '.', '');
                   $row[5] = $is_courseComplete->notcomplete;
                   $row[6] = count_enrolled_users($context, $withcapability = '', $groupid = 0);
                   
                   $table->data[] = $row;
                   $sn++;
              }
             }
               $html .= html_writer::table($table);
           
               echo $html;

}
	
	