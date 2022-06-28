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
 * Strings for component 'local_catimage', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_catimage
 */

require_once('../../config.php');

require_once($CFG->dirroot . '/local/catimage/lib.php');

defined('MOODLE_INTERNAL') || die();

require_login();

$url = new moodle_url('/local/catimage/index.php');

$context = context_system::instance();
    
require_capability('moodle/category:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('categoryimage', 'local_catimage'));
$PAGE->set_heading(get_string('categoryimage', 'local_catimage'));
$renderer = $PAGE->get_renderer('local_catimage');

 $action = optional_param('action', '', PARAM_RAW);
 $recordid = optional_param('recordid', 0, PARAM_INT);

if($action == 'delete' && !empty($recordid)){
    
    delete_record($recordid);
}

echo $OUTPUT->header();
$edit_url = new moodle_url($CFG->wwwroot . '/local/catimage/updatecategory.php');
$edit_link = html_writer::link($edit_url, get_string('addimage', 'local_catimage'), array('class' => 'btn btn-secondary'));
echo html_writer::tag('div', $edit_link, array('class' => 'text-md-right'));
echo $renderer->get_all_category_detail();


echo $OUTPUT->footer();