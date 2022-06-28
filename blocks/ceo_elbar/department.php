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
require_once($CFG->dirroot.'/blocks/ceo_elbar/locallib.php');

defined('MOODLE_INTERNAL') || die;

require_login();
$dep_id = optional_param('dep_id',0,PARAM_INT);
$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/blocks/ceo_elbar/department.php', array());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('block_ceo_elbar/ceo_elbar', 'Init');

$PAGE->set_title(get_string('pluginname', 'block_ceo_elbar').' - Course Released');
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('pluginname', 'block_ceo_elbar').' - Course Released');
$cancle = optional_param('pie_chart_form_cancel',"", PARAM_RAW);

echo $OUTPUT->header();


$filterData = [];
if(!empty($dep_id)){
    $filterData['department_id'] = $dep_id;
}
/* Department Section */
require_once('department_form.php');
$mform = new department_form();

$fromdatetime = strtotime( "-31 days", strtotime(date('Y-m-d')));
$todatetime = strtotime(date('Y-m-d'));

if ($cancle) {
    $formData = $mform->get_data();
    $filterData['department_id'] = $formData->category;
    $mform->reset();

} else if ($formData = $mform->get_data()) {

    $filterData['department_id'] = $formData->category;
    $fromdatetime = $formData->fromdate;
    $todatetime = $formData->todate;
}

$html .= $mform->render();

$filterData['start_date'] = $fromdatetime;
$filterData['end_date'] = $todatetime;

if(!empty($filterData['department_id'])){
    $trainingData = get_el_training_type($filterData['department_id']);
    $trainingName=[];
    foreach($trainingData as $training){
        $totalCourseData = get_elcourse($training->id,$filterData);
        if($totalCourseData){
            $numberOfCourses[] = $totalCourseData;
            if($training->depth > 3){
                $trainingName[] = get_category_parent_name($training->id);
            }else{
                $trainingName[] = $training->name;
            }
        }  
       
    }

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