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
$dep_id = optional_param('dep_id',0,PARAM_INT);
// if(!empty($dep_id)){echo $dep_id;die;}
$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/blocks/ceo_iltbar/view.php', array());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('block_ceo_iltbar/iltbar', 'Init');

$PAGE->set_title(get_string('pluginname', 'block_ceo_iltbar').' - Trainings Conducted');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('pluginname', 'block_ceo_iltbar').' - Trainings Conducted');

echo $OUTPUT->header();


$filterData = [];
if(!empty($dep_id)){
    $filterData['department_id'] = $dep_id;
}
/* Department Section */
require_once('department_date_filter.php');
$cancel = optional_param('cancel',"", PARAM_RAW);
$department = optional_param('department',"", PARAM_RAW);
$mform = new department_date_filter();

$fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
$enddatetime = strtotime(date('Y-m-d'));

if ($cancel) {
    $filterData['department_id'] = $department;
    $mform->reset();

} else if ($formData = (array)$mform->get_data()) {

    $fromdatetime = $formData['fromdate'];
    $enddatetime = $formData['todate'];
    $filterData['department_id'] = $formData['department'];
}

$html .= $mform->render();
$filterData['start_date'] = $fromdatetime;
$filterData['end_date'] = $enddatetime;

if(!empty($filterData['department_id'])){
    $trainingData = get_ilt_training_type($filterData['department_id']);
    $trainingName=[];
    foreach($trainingData as $training){
        
        $totalCourseData = get_iltcourse($training->id,$filterData);
        if($totalCourseData){
            $numberOfCourses[] = $totalCourseData;
            if($training->depth > 3){
                $trainingName[] = get_category_parent_name($training->id);
            }else{
                $trainingName[] = $training->name;
            }
        }  
        
    }
    /* Bar Chart Display */
    $tempData = $numberOfCourses;
    $nameData = $trainingName;
    if ($tempData) {
        $chart = new core\chart_bar();
        $players_series = new core\chart_series('Number of Courses', $tempData);
        $players_series->set_color('#FFD600');
        $chart->add_series($players_series);
        $chart->set_labels($nameData);
        $html .=  $OUTPUT->render($chart);
    }else{
        $html .= html_writer::tag('h1','No record found',array('align'=>'center'));
    }
}else{
    $html .= html_writer::tag('h1','No record found',array('align'=>'center'));
}

echo $html;
echo $OUTPUT->footer();