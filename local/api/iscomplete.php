<?php
require_once('../../config.php');
global $CFG;
require_once("{$CFG->libdir}/completionlib.php");
require_once("{$CFG->libdir}/filelib.php");
              $course = new stdClass();
              $course->id = 8;
              $cinfo = new completion_info($course);
              $iscomplete = $cinfo->is_course_complete(4);
              var_dump($iscomplete);
			  
			  $user = $DB->get_record('user', array('id' => 4));
			  //echo "<pre>";
			  //print_r($user);
			  //require_once($CFG->libdir.'/filelib.php');


			  echo new moodle_url('/user/pix.php/4/f1.jpg');
			 
?>