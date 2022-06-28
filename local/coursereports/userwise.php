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

$url = new moodle_url('/local/coursereports/userwise.php');

$context = context_system::instance();
    
//require_capability('moodle/category:manage', $context);

$page = optional_param('page',0,PARAM_INT);
$courseid = optional_param('courseid',0,PARAM_INT);
$cohort = optional_param('dpt',0,PARAM_INT);

$name = optional_param('name',null,PARAM_RAW);
$email = optional_param('email',null,PARAM_RAW);

$search = array('name'=>$name,'email'=>$email);

$format = optional_param('dataformat',null,PARAM_RAW);



$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('userwisereport', 'local_coursereports'));
$PAGE->set_heading(get_string('userwisereport', 'local_coursereports'));

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_coursereports/coursereports', 'Init');


  $renderer = $PAGE->get_renderer('local_coursereports');

  if($format){
     
    download_report_userwise($format);
  
  }

 
echo $OUTPUT->header();

$dropdwonFiltter = 'Department  <select id="userwisereport-department-filter" class="form-select">';
		$departmentdata = $DB->get_records('cohort', null);
		$dropdwonFiltter .= '<option value=0>All</option>';
		foreach($departmentdata as $data){
		
		$dropdwonFiltter .= '<option value='.$data->id.'>'.$data->name.'</option>';
			
		}

		$dropdwonFiltter .= '</select>';

		$dropdwonFiltter .= ' Name <input type="text" id="userwise-name">';
		//$dropdwonFiltter .= ' Email <input type="email" id="leaderboard-email"> ';
		$dropdwonFiltter .= ' <input type="button" class="btn btn-primary btn-sm" id="submit-userwise-filter" value="Search">';
		
		
echo $filter = html_writer::tag('div',$dropdwonFiltter,array('class'=>'leaderboardFiltter form-group'));


echo $renderer->userwise_data($page,$cohort,$search);


echo $OUTPUT->download_dataformat_selector(get_string('downloaddata','local_coursereports'), $url, $name = 'dataformat', array());

echo $OUTPUT->footer();