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

$url = new moodle_url('/local/coursereports/index.php');

$context = context_system::instance();
    
//require_capability('moodle/category:manage', $context);

$page = optional_param('page',0,PARAM_INT);
$courseid = optional_param('courseid',0,PARAM_INT);
$format = optional_param('dataformat',null,PARAM_RAW);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_coursereports'));
$PAGE->set_heading(get_string('pluginname', 'local_coursereports'));

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_coursereports/coursereports', 'Init');


  $renderer = $PAGE->get_renderer('local_coursereports');

 
  
if($format){
     
    download_report_coursewise($format);
  
  }
echo $OUTPUT->header();

// $dropdwonFiltter = 'Department  <select id="leaderboard-department-filter" class="form-select">';
// $departmentdata = $DB->get_records('cohort', null);
// $dropdwonFiltter .= '<option value=0>All</option>';
// foreach($departmentdata as $data){

// $dropdwonFiltter .= '<option value='.$data->id.'>'.$data->name.'</option>';
	
// }

// $dropdwonFiltter .= '</select>';


$dropdwonFiltter = ' 	Course  <select id="course-filter" class="form-select">';
		$coursefilterdata = get_courses();
		$dropdwonFiltter .= '<option value=0>All</option>';
		foreach($coursefilterdata as $coursedata){
			if($coursedata->id != 1){
		$dropdwonFiltter .= '<option value='.$coursedata->id.'>'.$coursedata->fullname.'</option>';
			}
		}

		$dropdwonFiltter .= '</select>';


		
echo $filter = html_writer::tag('div',$dropdwonFiltter,array('class'=>'dropdwonFiltter form-group'));

echo $renderer->corusereports_data($page);

echo $OUTPUT->download_dataformat_selector(get_string('downloaddata','local_coursereports'), $url, $name = 'dataformat', array());

echo $OUTPUT->footer();