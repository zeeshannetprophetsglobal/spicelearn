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
require_once($CFG->dirroot.'/local/scheduler/locallib.php');

defined('MOODLE_INTERNAL') || die;

require_login();
global $USER;

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/local/scheduler/view.php', array());

$PAGE->set_title(get_string('pluginname', 'local_scheduler'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('pluginname', 'local_scheduler'));

$format = optional_param('dataformat',null,PARAM_RAW);
$department = optional_param('department',null,PARAM_RAW);
$subject = optional_param('subject',null,PARAM_RAW);
$startdate = optional_param('startdate',null,PARAM_RAW);
$enddate = optional_param('enddate',null,PARAM_RAW);
$cancle = optional_param('scheduler_form_cancel',"", PARAM_RAW);

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_scheduler/scheduler','Init');


require_once('view_form.php');
$mform = new scheduler_form();

$fromdatetime = strtotime( "-1 month", time());
$todatetime = time();

$quizdata = local_get_quizdata();
$progressdata = get_progressdata();
$userdata = local_get_userdata();
$cate_data = local_get_cate_data();
$departments = local_get_departments();

if ($cancle) {

    $mform->reset();

} else if ($formData = $mform->get_data()) {

    $parameter = array();
    $parameter['department'] = $formData->department;
    $parameter['subject'] = $_POST['subject'];
    $parameter['fromdate'] = $formData->fromdate;
    $parameter['enddate'] = $formData->todate;
    scheduler_data_report('csv',$parameter['fromdate'],$parameter['enddate'],$parameter['department'],$parameter['subject'],$quizdata,$progressdata,$userdata,$cate_data,$departments); 

}
$html .= $mform->render();


echo $OUTPUT->header();

echo $html;

echo $OUTPUT->footer();