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

/**
 * @package    local_sitereport
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';

defined('MOODLE_INTERNAL') || die;

require_once $CFG->dirroot . '/local/sitereport/locallib.php';
require_once "$CFG->libdir/formslib.php";

class search_form extends moodleform
{
    public function definition()
    {
        $search_form = $this->_form;
        $search_form->addElement('header','local_sitereport_form', get_string('filters', 'local_sitereport'));
        $search_form->setExpanded('local_sitereport_form', true);

        $search_form->addElement('text', 'ecn', get_string('ecn', 'local_sitereport'), array('placeholder' => 'Search by ECN'));
       // $search_form->setDefault('ecn', null);
        $search_form->setType('ecn', PARAM_TEXT);
        $search_form->addElement('text', 'base', get_string('base', 'local_sitereport'), array('placeholder' => 'Search by Base'));
        $search_form->setDefault('base', null);
        $search_form->setType('base', PARAM_TEXT);
        $search_form->addElement('text', 'zone', get_string('zone', 'local_sitereport'), array('placeholder' => 'Search by Zone'));
        $search_form->setDefault('zone', null);
        $search_form->setType('zone', PARAM_TEXT);
        $search_form->addElement('text', 'rank', get_string('rank', 'local_sitereport'), array('placeholder' => 'Search by Rank'));
        $search_form->setDefault('rank', null);
        $search_form->setType('rank', PARAM_TEXT);
        $search_form->addElement('text', 'l1', get_string('l1', 'local_sitereport'), array('placeholder' => 'Search by L1'));
        $search_form->setDefault('l1', null);
        $search_form->setType('l1', PARAM_TEXT);
        $search_form->addElement('text', 'l2', get_string('l2', 'local_sitereport'), array('placeholder' => 'Search by L2'));
        $search_form->setDefault('l2', null);
        $search_form->setType('l2', PARAM_TEXT);

        $buttonarray=array();
        $buttonarray[] =& $search_form->createElement('submit', 'userreport_submit', get_string('search', 'local_sitereport'));
        $buttonarray[] =& $search_form->createElement('submit', 'userreport_cancel', get_string('cancel', 'local_sitereport'));
        $search_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        // $this->add_action_buttons($cancel = true, $submitlabel = 'Search');
    }
}

class course_search_form extends moodleform
{
    public function definition()
    {

        $c_startdate = optional_param('c_startdate',null,PARAM_RAW);
        $c_enddate = optional_param('c_enddate',null,PARAM_RAW);

        $search_form = $this->_form;
        $search_form->addElement('header','local_sitereport_form', get_string('filters', 'local_sitereport'));
        $search_form->setExpanded('local_sitereport_form', true);

        $type = optional_param('type',null,PARAM_RAW);
        $elsubject = optional_param('elsubject',null,PARAM_RAW);

        $typeOption = array(get_string('selecttype','local_sitereport'),get_string('ilt','local_sitereport'),get_string('elearn','local_sitereport'));

        $search_form->addElement('select', 'type', get_string('type', 'local_sitereport'), $typeOption);
        $search_form->setDefault('type', $type);
        $search_form->setType('type', PARAM_RAW);

        $iltsubjectOptionData = subject_option('ilt');
        $iltsubjectOption = array();
        $iltsubjectOption[0] = 'Select subject'; 
       // print_object($subjectOptionData);
        foreach($iltsubjectOptionData as $sdata){
            if(!empty($sdata->name)){
                if($subject = get_subject($sdata->path)){
                    $iltsubjectOption[$sdata->id] = $subject;
                }  
            }
        }

        $search_form->addElement('select', 'iltsubject', get_string('subject', 'local_sitereport'), $iltsubjectOption);
        $search_form->setDefault('iltsubject', null);
        $search_form->hideIf('iltsubject', 'type', 'neq', 1);
        $search_form->setType('iltsubject', PARAM_RAW);

        $elsubjectOptionData = subject_option('el');
        $elsubjectOption = array();
        $elsubjectOption[0] = 'Select subject'; 
       // print_object($subjectOptionData);
        foreach($elsubjectOptionData as $sdata){
            if(!empty($sdata->name)){
                if($subject = get_subject($sdata->path)){
                    $elsubjectOption[$sdata->id] = $subject;
                }
            }
        }

        $search_form->addElement('select', 'elsubject', get_string('subject', 'local_sitereport'), $elsubjectOption);
        $search_form->setDefault('elsubject', $elsubject);
        $search_form->hideIf('elsubject', 'type', 'neq', 2);
        $search_form->setType('elsubject', PARAM_RAW);


        if (!$c_startdate) {
            $c_startdate = strtotime( "-1 month", time());
        }
        if (!$c_enddate) {
            $c_enddate = time();
        }
        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $search_form->addElement('date_selector', 'c_startdate',get_string('from_date', 'block_ceo_elbar'), $opts);
        $search_form->setDefault('c_startdate',  $c_startdate);

        $search_form->addElement('date_selector', 'c_enddate', get_string('to_date', 'block_ceo_elbar'),$opts);
        $search_form->setDefault('c_enddate',  $c_enddate);

        $buttonarray=array();
        $buttonarray[] =& $search_form->createElement('submit', 'coursereport_submit', get_string('search', 'local_sitereport'));
        $buttonarray[] =& $search_form->createElement('submit', 'coursereport_cancel', get_string('cancel', 'local_sitereport'));
        $search_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        // $this->add_action_buttons($cancel = true, $submitlabel = 'Search');

    }
}



class instructor_search_form extends moodleform
{
    public function definition()
    {
        $search_form = $this->_form;
        $search_form->addElement('text', 'date', get_string('date', 'local_sitereport'), array('placeholder' => 'DD-MM-YYYY'));
       // $search_form->setDefault('ecn', null);

        $this->add_action_buttons($cancel = false, $submitlabel = 'Search');
    }
}

class ceo_user_form extends moodleform
{
    public function definition()
    {
        global $DB;

        $base = optional_param('base',null,PARAM_TEXT);
        $zone = optional_param('zone',null,PARAM_TEXT);
        $rank = optional_param('rank',null,PARAM_TEXT);
        $ecn = optional_param('ecn',null,PARAM_TEXT);
        $l1 = optional_param('l1',null,PARAM_TEXT);
        $l2 = optional_param('l2',null,PARAM_TEXT);
        $dpt = optional_param('dpt',null,PARAM_INT); 


        $search_form = $this->_form;
        $search_form->addElement('header','local_sitereport_form', get_string('filters', 'local_sitereport'));
        $search_form->setExpanded('local_sitereport_form', true);

        $dptData = $DB->get_records('cohort',array('visible'=>1));
        $dptOption = array();
        $dptOption[0] = 'Select Departments'; 
        // print_object($subjectOptionData);
        foreach($dptData as $sdata){
           if(!empty($sdata->name)){
               $dptOption[$sdata->id] = $sdata->name;
           }
       }

       $search_form->addElement('select', 'dpt', get_string('departments', 'local_sitereport'), $dptOption);
       $search_form->setDefault('dpt', $dpt);
       $search_form->setType('dpt', PARAM_INT);


       $search_form->addElement('text', 'ecn', get_string('ecn', 'local_sitereport'), array('placeholder' => 'Search by ECN'));
       $search_form->setDefault('ecn', $ecn);
       $search_form->setType('ecn', PARAM_TEXT);
       $search_form->addElement('text', 'base', get_string('base', 'local_sitereport'), array('placeholder' => 'Search by Base'));
       $search_form->setDefault('base', $base);
       $search_form->setType('base', PARAM_TEXT);
       $search_form->addElement('text', 'zone', get_string('zone', 'local_sitereport'), array('placeholder' => 'Search by Zone'));
       $search_form->setDefault('zone', $zone);
       $search_form->setType('zone', PARAM_TEXT);
       $search_form->addElement('text', 'rank', get_string('rank', 'local_sitereport'), array('placeholder' => 'Search by Rank'));
       $search_form->setDefault('rank', $rank);
       $search_form->setType('rank', PARAM_TEXT);
       $search_form->addElement('text', 'l1', get_string('l1', 'local_sitereport'), array('placeholder' => 'Search by L1'));
       $search_form->setDefault('l1', $l1);
       $search_form->setType('l1', PARAM_TEXT);
       $search_form->addElement('text', 'l2', get_string('l2', 'local_sitereport'), array('placeholder' => 'Search by L2'));
       $search_form->setDefault('l2', $l2);
       $search_form->setType('l2', PARAM_TEXT);

       $buttonarray=array();
       $buttonarray[] =& $search_form->createElement('submit', 'ceo_userreport_submit', get_string('search', 'local_sitereport'));
       $buttonarray[] =& $search_form->createElement('submit', 'ceo_userreport_cancel', get_string('cancel', 'local_sitereport'));
       $search_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
       // $this->add_action_buttons($cancel = true, $submitlabel = 'Search');
   }
}


class ceo_course_search_form extends moodleform
{
    public function definition()
    {
        global $DB;
        $dpt = optional_param('dpt',null,PARAM_TEXT);
        $type = optional_param('type',null,PARAM_TEXT);
        $page = optional_param('page',null,PARAM_TEXT);
        $c_startdate = optional_param('c_startdate',null,PARAM_RAW);
        $c_enddate = optional_param('c_enddate',null,PARAM_RAW);

        $search_form = $this->_form;
        $search_form->addElement('header','local_sitereport_form', get_string('filters', 'local_sitereport'));
        $search_form->setExpanded('local_sitereport_form', true);

        $dptData = $DB->get_records('course_categories',array('depth'=>1,'visible'=>1));
        $dptOption = array();
        $dptOption[0] = 'Select Departments'; 
        foreach($dptData as $sdata){
           if(!empty($sdata->name)){
               $dptOption[$sdata->id] = $sdata->name;
           }
       }

       $search_form->addElement('select', 'dpt', get_string('departments', 'local_sitereport'), $dptOption);
       $search_form->setType('dpt', PARAM_INT);
       $search_form->setDefault('dpt', $dpt);

       $typeOption = array(get_string('selecttype','local_sitereport'),get_string('ilt','local_sitereport'),get_string('elearn','local_sitereport'));

       $search_form->addElement('select', 'type', get_string('type', 'local_sitereport'), $typeOption);
       $search_form->setDefault('type', $type);
       $search_form->setType('type', PARAM_RAW);
       $search_form->disabledIf('type', 'dpt', 'eq', 0);


       $iltsubjectOptionData = $this->getsubjects();
       $iltsubjectOption = array();
       $iltsubjectOption[0] = 'Select subject'; 
       if ($iltsubjectOptionData) {
            foreach($iltsubjectOptionData as $sdata){
                if(!empty($sdata->name)){
                    $iltsubjectOption[$sdata->id] = get_subject($sdata->path);
                }
            }
        }

         $search_form->addElement('select', 'subject', get_string('subject', 'local_sitereport'), $iltsubjectOption);
         $search_form->setDefault('subject', null);
         $search_form->setType('iltsubject', PARAM_RAW);
        $search_form->disabledIf('subject', 'type', 'eq', 0);

 if (!$c_startdate) {
    $c_startdate = strtotime( "-1 month", time());
}
if (!$c_enddate) {
    $c_enddate = time();
}
$opts = array(
    'startyear' => 2000,
    'stopyear' => date("Y"),
    'timezone'  => 99,
    'optional'  => false,
);
$search_form->addElement('date_selector', 'c_startdate',get_string('from_date', 'block_ceo_elbar'), $opts);
$search_form->setDefault('c_startdate',  $c_startdate);

$search_form->addElement('date_selector', 'c_enddate', get_string('to_date', 'block_ceo_elbar'),$opts);
$search_form->setDefault('c_enddate',  $c_enddate);

$buttonarray=array();
$buttonarray[] =& $search_form->createElement('submit', 'ceo_coursereport_submit', get_string('search', 'local_sitereport'));
$buttonarray[] =& $search_form->createElement('submit', 'ceo_coursereport_cancel', get_string('cancel', 'local_sitereport'));
$search_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);

     // $this->add_action_buttons($cancel = true, $submitlabel = 'Search');

}

function reset() {
    $this->_form->updateSubmission(null, null);
}
function getsubjects() {
    global $DB;

    $dpt = optional_param('dpt',null,PARAM_TEXT);
    $type = optional_param('type',null,PARAM_TEXT);

    $optiondata = '';
    $subjecthtml = '<option value="">All</option>';
    if ( !empty($type)) {

        if($type == 1){
            $sql = "SELECT * FROM {course_categories} WHERE path LIKE '%".$dpt."%' AND idnumber LIKE '%ILT%'";
        }else{
            $sql = "SELECT * FROM {course_categories} WHERE path LIKE '%".$dpt."%' AND idnumber LIKE '%EL%'";
        }

        $optiondata =  $DB->get_records_sql($sql,null);

    }
    return $optiondata;
}

}

class ceo_author_search_form extends moodleform
{
    public function definition()
    {
        global $DB;

        $startdate = optional_param('startdate',null,PARAM_RAW);
        $enddate = optional_param('enddate',null,PARAM_RAW);
        $search_form = $this->_form;
        $search_form->addElement('header','local_sitereport_form', get_string('filters', 'local_sitereport'));
        $search_form->setExpanded('local_sitereport_form', true);

        $dptData = $DB->get_records('cohort',array('visible'=>1));
        $dptOption = array();
        $dptOption[0] = 'Select Departments'; 
        // print_object($subjectOptionData);
        foreach($dptData as $sdata){
           if(!empty($sdata->name)){
               $dptOption[$sdata->id] = $sdata->name;
           }
       }

       $search_form->addElement('select', 'dpt', get_string('departments', 'local_sitereport'), $dptOption);
         // $search_form->setDefault('ecn', null);
       $search_form->setType('dpt', PARAM_INT);
       $search_form->addElement('text', 'ecn', get_string('ecn', 'local_sitereport'), array('placeholder' => 'ECN'));
       // $search_form->setDefault('ecn', null);
       $search_form->setType('ecn', PARAM_TEXT);


       if (!$startdate) {
        $startdate = strtotime( "-31 days", time());
    }
    if (!$enddate) {
        $enddate = time();
    }
    $opts = array(
        'startyear' => 2000,
        'stopyear' => date("Y"),
        'timezone'  => 99,
        'optional'  => false,
    );
    $search_form->addElement('date_selector', 'startdate',get_string('from_date', 'block_ceo_elbar'), $opts);
    $search_form->setDefault('startdate',  $startdate);

    $search_form->addElement('date_selector', 'enddate', get_string('to_date', 'block_ceo_elbar'),$opts);
    $search_form->setDefault('enddate',  $enddate);

    $buttonarray=array();
    $buttonarray[] =& $search_form->createElement('submit', 'ceo_author_report_submit', get_string('search', 'local_sitereport'));
    $buttonarray[] =& $search_form->createElement('submit', 'ceo_author_report_cancel', get_string('cancel', 'local_sitereport'));
    $search_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
       // $this->add_action_buttons($cancel = true, $submitlabel = 'Search');

}

function validation($data, $files) {
    $errors = parent::validation($data, $files);
    if ($data['startdate'] > time()) {
        $errors['startdate'] = get_string('startdterror', 'local_sitereport');
    }
    if ($data['enddate'] < $data['startdate']) {
        $errors['enddate'] = get_string('todatedterror', 'local_sitereport');
    }
        // print_r($errors);die;
    return $errors;
}
}



class author_search_form extends moodleform
{
    public function definition()
    {


        $startdate = optional_param('startdate',null,PARAM_RAW);
        $enddate = optional_param('enddate',null,PARAM_RAW);

        $search_form = $this->_form;
        $search_form->addElement('header','local_sitereport_form', get_string('filters', 'local_sitereport'));
        $search_form->setExpanded('local_sitereport_form', true);

        $search_form->addElement('text', 'ecn', get_string('ecn', 'local_sitereport'), array('placeholder' => 'ECN'));
        $search_form->setType('ecn', PARAM_TEXT);   
        

        if (!$startdate) {
            $startdate = strtotime( "-31 days", time());
        }
        if (!$enddate) {
            $enddate = time();
        }
        $opts = array(
            'startyear' => 2000,
            'stopyear' => date("Y"),
            'timezone'  => 99,
            'optional'  => false,
        );
        $search_form->addElement('date_selector', 'startdate',get_string('from_date', 'block_ceo_elbar'), $opts);
        $search_form->setDefault('startdate',  $startdate);

        $search_form->addElement('date_selector', 'enddate', get_string('to_date', 'block_ceo_elbar'),$opts);
        $search_form->setDefault('enddate',  $enddate);
        
        $buttonarray=array();
        $buttonarray[] =& $search_form->createElement('submit', 'author_report_submit', get_string('search', 'local_sitereport'));
        $buttonarray[] =& $search_form->createElement('submit', 'author_report_cancel', get_string('cancel', 'local_sitereport'));
        $search_form->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        // $this->add_action_buttons($cancel = true, $submitlabel = 'Search');

    }
}
