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



require_once('../../config.php');
require_once($CFG->dirroot.'/local/sitereport/locallib.php');

defined('MOODLE_INTERNAL') || die;

require_login();

global $USER;

$userid = required_param('userid',PARAM_INT);
$action = optional_param('action',null,PARAM_TEXT);
$roleid = optional_param('roleid',null,PARAM_INT);
$roleback = optional_param('roleback',null,PARAM_RAW);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/local/sitereport/trainee.php', array('userid'=>$userid));

$PAGE->requires->jquery();
//$PAGE->requires->js_call_amd('local_sitereport/sitereport', 'Init');
$PAGE->set_title(get_string('courselist', 'local_sitereport'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading( get_string('courselist', 'local_sitereport'));
if($roleback == 'ceo'){
    $PAGE->navbar->add(get_string('userreport', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/ceo_userreport.php'),null);

}else{
    $PAGE->navbar->add(get_string('allreport', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/'));
    $PAGE->navbar->add(get_string('userreport', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/userreport.php'),null);
}



$PAGE->navbar->add(get_string('courselist', 'local_sitereport'), null);


$renderer = $PAGE->get_renderer('local_sitereport');

// Search form Initialization.
echo $OUTPUT->header();


echo $html;

$userdata = core_user::get_user($userid);
echo html_writer::tag('h3',$userdata->firstname.' '.$userdata->lastname);
echo $renderer->trainee_course_list($userid,$action,$roleid);

echo $OUTPUT->footer();