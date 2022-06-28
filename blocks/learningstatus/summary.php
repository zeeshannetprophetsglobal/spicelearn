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
 * Plugin version and other meta-data are defined here.
 *
 * @package     block_learningstatus
 */


require_once(__DIR__ .'/../../config.php');


use core_completion\progress;


global $CFG,$OUTPUT,$USER;


require_once($CFG->libdir."/completionlib.php");

require_login();
$id = required_param('userid',PARAM_INT);
$context = context_system::instance();
$component = "block_learningstatus";

$pageurl = new moodle_url($CFG->wwwroot."/blocks/learningstatus/summary.php",array('userid'=>$id));
$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string("pluginname", "block_learningstatus"));
$PAGE->set_title(get_string("pluginname", "block_learningstatus"));
$PAGE->navbar->add('Summary', null);

$mycourses = enrol_get_my_courses();

echo $OUTPUT->header();

$html = '';

$table = new html_table();
$table->align = array('left', 'left', 'left', 'center');
$table->size = array('45%', '15%', '20%', '20%');
$table->head = array(
     get_string('coursename', 'block_learningstatus'), 
     get_string('status','block_learningstatus'),
     get_string('startdate','block_learningstatus'),
     get_string('completdate','block_learningstatus'),
);

$table->data = array();
foreach($mycourses as $enrolcourse){

     if($enrolcourse->visible == 1) {

     $course = new stdClass();
     $course->id = $enrolcourse->id;
     $cinfo = new completion_info($course);

     $iscomplete = $cinfo->is_course_complete($USER->id);
     $coursedetail = get_course($enrolcourse->id);

     $sql = "SELECT a.id,a.timestart FROM {user_enrolments} as a join {enrol} as b on a.enrolid = b.id WHERE a.userid = ".$USER->id." and courseid =".$enrolcourse->id;

     // print_object($sql);die;
     $courseStart = $DB->get_record_sql($sql,null);
     $startTime = ($courseStart->timestart)?date("Y-m-d H:i",$courseStart->timestart):'--';
     $complteTime = $DB->get_record('course_completions',array('userid'=>$USER->id,'course'=>$enrolcourse->id));
     
     if($complteTime->timecompleted){
        $TimeToComplete = date("Y-m-d H:i",$complteTime->timecompleted);
     }else{
          $TimeToComplete = '--';
     }
     	
     
     if(empty($alldata->timecompleted)){
          $timecompleted = get_string('notcomplete','block_learningstatus');
     }else{
          $timecompleted = $alldata->timecompleted;
     }

     $timedeff =  $coursedetail->enddate - time();
     $row = array();       
     $courselink = new moodle_url($CFG->wwwroot.'/course/view.php',array('id'=>$enrolcourse->id));
     $row[0] = html_writer::link($courselink,$enrolcourse->fullname);

     $lastaccess = $DB->get_record('user_lastaccess', array('userid'=>$USER->id,'courseid' => $coursedetail->id));
     if ($iscomplete) {
          $row[1] ='Completed';    
          $startTime = date("Y-m-d H:i",$complteTime->timestarted);
     }else if($lastaccess){
          $row[1] = 'In progress';
     }elseif(1296000>$timedeff && $iscomplete==false && $coursedetail->enddate){
          $row[1] ='Critical and In progress';
     }else{
          $row[1] = 'Not Started';
          $startTime = '--';
     }

     $row[2] = $startTime;
     $row[3] = $TimeToComplete;
     $table->data[] = $row;
}
}

$html .= html_writer::table($table);

if(empty($table->data)){
     $html .= html_writer::tag('h3',get_string('notcompletecourse','block_learningstatus'),array('align'=>'center'));
}
echo $html;
echo $OUTPUT->footer();