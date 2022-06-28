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
 * Strings for component 'local_coursereports', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_coursereports
 */

require_once('../../config.php');

require_once($CFG->dirroot . '/local/coursereports/lib.php');

defined('MOODLE_INTERNAL') || die();

require_login();

$userid = required_param('id',PARAM_INT);
$page = optional_param('page',0,PARAM_INT);
$courseid = optional_param('courseid',0,PARAM_INT);
$status = optional_param('status',null,PARAM_RAW);

$url = new moodle_url('/local/coursereports/userreport.php',array('id'=>$userid));

$context = context_system::instance();
    
//require_capability('moodle/category:manage', $context);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('userreport', 'local_coursereports'));
$PAGE->set_heading(get_string('userreport', 'local_coursereports'));

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_coursereports/coursereports', 'Init');

$PAGE->navbar->add(get_string('userwisereport','local_coursereports'),  new moodle_url('/local/coursereports/userwise.php'));
$PAGE->navbar->add(get_string('userreport','local_coursereports'),  null);


  $renderer = $PAGE->get_renderer('local_coursereports');

 
echo $OUTPUT->header();

$userDetail = core_user::get_user($userid);
echo html_writer::tag('h2',$userDetail->firstname.' '.$userDetail->lastname,array());
$dropdwonFiltter = 'Course  <select id="course-filter" class="form-select">';
		$coursefilterdata = get_courses();
		$dropdwonFiltter .= '<option value=0>All</option>';
		foreach($coursefilterdata as $coursedata){
			if($coursedata->id != 1){
		$dropdwonFiltter .= '<option value='.$coursedata->id.'>'.$coursedata->fullname.'</option>';
			}
		}

		$dropdwonFiltter .= '</select>';
		
//echo $filter = html_writer::tag('div',$dropdwonFiltter,array('class'=>'row dropdwonFiltter form-group'));

echo $renderer->user_report($page,$status,$userid);

echo $OUTPUT->footer();