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
 * Strings for component 'local_resourcescore', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_resourcescore
 */

require_once('../../config.php');

require_once($CFG->dirroot . '/local/resourcescore/lib.php');

require_once($CFG->dirroot.'/local/resourcescore/mod_form.php');


defined('MOODLE_INTERNAL') || die();

require_login();

$courseid = optional_param('courseid', 0,PARAM_INT);

$url = new moodle_url('/local/resourcescore/addscore.php',array('courseid'=>$courseid));

$context = context_system::instance();
    
require_capability('moodle/category:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('addscore', 'local_resourcescore'));
$PAGE->set_heading(get_string('addscore', 'local_resourcescore'));

$mform = new score_form();
 

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
  
    redirect($CFG->wwwroot.'/local/resourcescore/index.php');
    
} else if ($fromdat = $mform->get_data()) {
  
    $result = add_score($fromdat,$courseid);
    
    if($result){
        redirect($CFG->wwwroot.'/local/resourcescore/index.php');
    }
  
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
 
  //Set default data (if any)
  //$mform->set_data($toform);
  //displays the form
  echo $OUTPUT->header();
  $course = get_course($courseid);
  echo  html_writer::tag('h2',$course->fullname,array());
  $mform->display();
  echo $OUTPUT->footer();
}