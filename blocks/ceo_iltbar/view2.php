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
require_once($CFG->dirroot.'/blocks/ceo_iltbar/locallib.php');

defined('MOODLE_INTERNAL') || die;

require_login();
$training_id = optional_param('training_id',0,PARAM_INT);
// if(!empty($training_id)){ echo $training_id; die; }
$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/blocks/ceo_iltbar/view2.php', array());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('block_ceo_iltbar/iltbar', 'Init');

$PAGE->set_title('CEO ILTBAR');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('CEO ILTBAR DETAILS');

echo $OUTPUT->header();

$departmentList = department();
$html .= '<h4>Pie Chart</h4>';
$html .= '<from action="view2.php" name="filter_form" method="post">';
$html .= '<label>Department:</label>';
$html .= '<select name="department" id="department_piechart">';
$html .= '<option>Select</option>';
foreach($departmentList as $department){
    $html .= '<option value="'.$department->id.'">'.$department->name.'</option>';
}
$html .= '</select></br>';
$html .= '<label>Training Type:</label>';
$html .= '<select name="training" id="trainingType">';
$html .= '<option>Select</option>';
$html .= '</select></br>';

$html .= '<label for="start">
    Start Date:
</label>';
$html .= '<input type="date" name="start_date" 
placeholder="dd-mm-yyyy" value="'.date('Y-m-d').'" min="2021-01-01" max="'.date('Y-m-d').'"></br>';

$html .= '<label for="end">
    End Date:
</label>';
$html .= '<input type="date" name="start_date" 
placeholder="dd-mm-yyyy" value="'.date('Y-m-d').'" min="1997-01-01" max="'.date('Y-m-d').'"></br>';

$html .= '<input type="button" value="Filter" id="filterButton">';
$html .= '</form>';

$piechart = new core\chart_pie();
$seriesDisplay =  new core\chart_series('Total Students', [400, 460, 1120, 540]);
$piechart->add_series($seriesDisplay); // On pie charts we just need to set one series.
$piechart->set_labels(['2004', '2005', '2006', '2007']);
$html .= $OUTPUT->render($piechart);

echo $html;
echo $OUTPUT->footer();