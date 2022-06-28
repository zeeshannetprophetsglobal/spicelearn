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
 * @package    block_mycourses
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');

defined('MOODLE_INTERNAL') || die;


require_login();
global $USER;

$context = context_system::instance();

$search = optional_param('search', null, PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);

$term = optional_param('term', '', PARAM_RAW); 
$program = optional_param('program', '', PARAM_RAW); 
$department = optional_param('department', '', PARAM_RAW); 
$batchyear = optional_param('batchyear', '', PARAM_RAW); 
$semester = optional_param('semester', '', PARAM_RAW);
$enroleid = optional_param('semester', '', PARAM_RAW); 
$section = optional_param('section', '', PARAM_RAW); 
$open_close = optional_param('open_close', 0, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot . '/local/mycourses/helpdesk.php');
$PAGE->set_title($PAGE->course->shortname . ' :' . get_string('elearngroup', 'block_mycourses'));
$PAGE->set_pagelayout('helpdesk');
$PAGE->set_heading(get_string('elearngroup', 'block_mycourses'));
$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('block_mycourses/mycourses', 'Init');
$PAGE->navbar->add(get_string('elearngroup', 'block_mycourses'), null);


echo $OUTPUT->header();


$renderer = $PAGE->get_renderer('block_mycourses');


echo html_writer::tag('div',$renderer->groups_filtter(el),array('class'=>'filtter-box','group'=>'el'));
echo $renderer->user_elearn_courses();

echo $OUTPUT->footer();
