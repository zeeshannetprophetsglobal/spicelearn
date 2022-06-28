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

// defined('MOODLE_INTERNAL') || die();


require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/manager_iltbar/locallib.php');

defined('MOODLE_INTERNAL') || die;

require_login();
$dep_id = optional_param('dep_id',0,PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/blocks/manager_iltbar/view.php', array());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('block_manager_iltbar/iltbar', 'Init');

$PAGE->set_title('Manager ILTBAR');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Manager ILT BAR CHART ');

echo $OUTPUT->header();


/* Department Section */
$departmentList = department();
$html = ' ';
$html .= '<label>Department</label>';
$html .= '<select name="department" id="department">';
$html .= '<option>Select</option>';
foreach($departmentList as $department){
    $selected = ($dep_id == $department->id) ? 'selected' : '';
    $html .= '<option value="'.$department->id.'" '.$selected.'>'.$department->name.'</option>';
    
}
$html .= '</select>';
/* Department Section Ends */


if(!empty($dep_id)){
    $trainingData = get_ilt_training_type($dep_id);
    $trainingName=[];
    foreach($trainingData as $training){
        
        if($training->depth > 3){
            $trainingName[] = get_category_parent_name($training->id);
        }else{
            $trainingName[] = $training->name;
        }
        // $trainingName[] = $training->name;
        $numberOfUsers[] = get_enrol_user($training->id);
    }
    /* Bar Chart Display */
    $tempData = $numberOfUsers;
    $nameData = $trainingName;
    $chart = new core\chart_bar();
    $players_series = new core\chart_series('Number of Enrolled Students', $tempData);
    $chart->add_series($players_series);
    $chart->set_labels($nameData);
    $html .=  $OUTPUT->render($chart);
}

echo $html;
echo $OUTPUT->footer();