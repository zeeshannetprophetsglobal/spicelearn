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
require_once($CFG->dirroot.'/local/sitereport/searchform.php');

defined('MOODLE_INTERNAL') || die;

require_login();

global $USER;


$format = optional_param('dataformat',null,PARAM_RAW);
$page = optional_param('page',0,PARAM_INT);
$dpt = optional_param('dpt',null,PARAM_RAW);
$ecn = optional_param('ecn',null,PARAM_RAW);
$startdate = optional_param('startdate',null,PARAM_RAW);
$enddate = optional_param('enddate',null,PARAM_RAW);

$url = $CFG->wwwroot.'/local/sitereport/ceo_teacher_report.php';
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);

$PAGE->requires->jquery();
//$PAGE->requires->js_call_amd('local_sitereport/sitereport', 'Init');
$PAGE->set_title(get_string('teacher_report', 'local_sitereport'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading( get_string('teacher_report', 'local_sitereport'));

$PAGE->navbar->add(get_string('teacher_report', 'local_sitereport'), null);

$renderer = $PAGE->get_renderer('local_sitereport');
$mform = new ceo_author_search_form();
$ceo_author_report_cancel = optional_param('ceo_author_report_cancel',null, PARAM_RAW);

if ($ceo_author_report_cancel) {

    $searchurl = new moodle_url($CFG->wwwroot.'/local/sitereport/ceo_teacher_report.php',null);
    redirect($searchurl);
    
} else if ($fromdat = $mform->get_data()) {
   
    $dpt = $fromdat->dpt;
    $ecn = $fromdat->ecn;
    $startdate = $fromdat->startdate;
    $enddate = $fromdat->enddate;
} 

if($format){
    ceo_download_instructor_report($format,$dpt,$ecn,$startdate,$enddate); 
}

// Search form Initialization.
echo $OUTPUT->header();

echo  $mform->display();

echo $renderer->ceo_teacher_report($page,$dpt,$ecn,$startdate,$enddate);

echo $OUTPUT->download_dataformat_selector(get_string('downloadreport','local_sitereport'), $url, $name = 'dataformat', array('dpt'=>$dpt,'ecn'=>$ecn,'startdate'=>$startdate,'enddate'=>$enddate));

echo $OUTPUT->footer();
