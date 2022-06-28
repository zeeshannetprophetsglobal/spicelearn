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
$base = optional_param('base',null,PARAM_TEXT);
$zone = optional_param('zone',null,PARAM_TEXT);
$rank = optional_param('rank',null,PARAM_TEXT);
$ecn = optional_param('ecn',null,PARAM_TEXT);
$l1 = optional_param('l1',null,PARAM_TEXT);
$l2 = optional_param('l2',null,PARAM_TEXT);

$url = $CFG->wwwroot.'/local/sitereport/userreport.php';
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_sitereport/sitereport', 'Init');

$PAGE->set_title(get_string('userreport', 'local_sitereport'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading( get_string('userreport', 'local_sitereport'));

$format = optional_param('dataformat',null,PARAM_RAW);

$PAGE->navbar->add(get_string('allreport', 'local_sitereport'), new moodle_url($CFG->wwwroot.'/local/sitereport/'));
$PAGE->navbar->add(get_string('userreport', 'local_sitereport'), null);


$renderer = $PAGE->get_renderer('local_sitereport');

$userdata = local_get_userdata();

$mform = new search_form();

$userreport_cancel = optional_param('userreport_cancel',null, PARAM_RAW);
if ($userreport_cancel) {

    $searchurl = new moodle_url($CFG->wwwroot.'/local/sitereport/userreport.php');
    redirect($searchurl);
    
} else if ($fromdat = $mform->get_data()) {
    
    $parameter = array();
    $ecn = $fromdat->ecn;
    $base = $fromdat->base;
    $zone = $fromdat->zone;
    $rank = $fromdat->rank;
    $l1 = $fromdat->l1;
    $l2 = $fromdat->l2;
    
}

if($format){
   
    download_trainee_report($format,$ecn,$base,$zone,$rank,$l1,$l2,$userdata);
    
}

echo $OUTPUT->header();

echo  $mform->display();

$html = '';

$html .= html_writer::start_tag('div',array('class'=>'row pt-3','id'=>'trainee_table'));
$html .= $renderer->user_report($page,$ecn,$base,$zone,$rank,$l1,$l2);
$html .= html_writer::end_tag('div');
echo $html;

echo $OUTPUT->download_dataformat_selector(get_string('downloadreport','local_sitereport'), $url, $name = 'dataformat', array('ecn'=>$ecn,'base'=>$base,'zone'=>$zone,'rank'=>$rank,'l1'=>$l1,'l2'=>$l2));

echo $OUTPUT->footer();
