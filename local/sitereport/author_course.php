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

$roleback = optional_param('roleback',null,PARAM_RAW);
$startdate = optional_param('startdate',null,PARAM_RAW);
$enddate = optional_param('enddate',null,PARAM_RAW);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/local/sitereport/author_course.php', array('userid'=>$userid));

$PAGE->requires->jquery();
//$PAGE->requires->js_call_amd('local_sitereport/sitereport', 'Init');
$PAGE->set_title(get_string('author_course', 'local_sitereport'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading( get_string('author_course', 'local_sitereport'));
if($roleback == 'ceo'){
    $PAGE->navbar->add(get_string('author_report', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/ceo_author_report.php'));
}else{
    $PAGE->navbar->add(get_string('allreport', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/'));
    $PAGE->navbar->add(get_string('author_report', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/author_report.php'));
}
$PAGE->navbar->add(get_string('author_course', 'local_sitereport'), null);


$renderer = $PAGE->get_renderer('local_sitereport');

// Search form Initialization.
echo $OUTPUT->header();


echo $html;

echo $renderer->author_course_list($userid,$startdate,$enddate);

echo $OUTPUT->footer();