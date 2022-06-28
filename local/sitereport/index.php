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


$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/local/sitereport/index.php', array());

$PAGE->requires->jquery();
//$PAGE->requires->js_call_amd('local_sitereport/sitereport', 'Init');
$PAGE->set_title(get_string('pluginname', 'local_sitereport'));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading( get_string('pluginname', 'local_sitereport'));

$PAGE->navbar->add(get_string('pluginname', 'local_sitereport'), null);


$renderer = $PAGE->get_renderer('local_sitereport');

// Search form Initialization.
echo $OUTPUT->header();


echo $html;

echo $renderer->report_card_list();

echo $OUTPUT->footer();