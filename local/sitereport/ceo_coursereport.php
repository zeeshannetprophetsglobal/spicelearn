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
$dpt = optional_param('dpt',0,PARAM_INT);
$type = optional_param('type',null,PARAM_RAW);
$subject = optional_param('subject',null,PARAM_RAW);
$timetype = 1;
$c_startdate = optional_param('c_startdate',null,PARAM_RAW);
$c_enddate = optional_param('c_enddate',null,PARAM_RAW);
$m_startdate = optional_param('m_startdate',null,PARAM_RAW);
$m_enddate = optional_param('m_enddate',null,PARAM_RAW);

$url = $CFG->wwwroot.'/local/sitereport/ceo_coursereport.php';

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_sitereport/sitereport', 'Init');
$PAGE->set_title(get_string('coursereport', 'local_sitereport'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading( get_string('coursereport', 'local_sitereport'));
$PAGE->navbar->add(get_string('coursereport', 'local_sitereport'), null);

$renderer = $PAGE->get_renderer('local_sitereport');

$mform = new ceo_course_search_form();
$ceo_coursereport_cancel = optional_param('ceo_coursereport_cancel',null, PARAM_RAW);

if (!$c_startdate) {
    $c_startdate = strtotime( "-1 month", time());
}

if (!$c_enddate) {
    $c_enddate = time();
}

if ($ceo_coursereport_cancel) {
    $mform->reset();
    $searchurl = new moodle_url($CFG->wwwroot.'/local/sitereport/ceo_coursereport.php');
    redirect($searchurl);

} else if ($fromdat = $mform->get_data()) {

    $dpt = $fromdat->dpt;  
    $type = $fromdat->type;
    $subject = $fromdat->subject;
    $timetype = 1;
    $c_startdate = $fromdat->c_startdate;
    $c_enddate = $fromdat->c_enddate;
    $m_startdate = $fromdat->m_startdate;
    $m_enddate = $fromdat->m_enddate;

}

if($format){
  ceo_download_course_report($format,$dpt,$type,$subject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate);
}
echo $OUTPUT->header();

echo  $mform->render();

$html = '';

$html .= html_writer::start_tag('div',array('class'=>'row pt-3','id'=>'course_table'));
$html .= $renderer->ceo_course_report($page,$dpt,$type,$subject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate);
$html .= html_writer::end_tag('div');
echo $html;

echo $OUTPUT->download_dataformat_selector(get_string('downloadreport','local_sitereport'), $url, $name = 'dataformat', array('dpt'=>$dpt,'type'=>$type,'subject'=>$subject,'timetype'=>$timetype,'c_startdate'=>$c_startdate,'c_enddate'=>$c_enddate,'m_startdate'=>$m_startdate,'m_enddate'=>$m_enddate));

echo $OUTPUT->footer();
