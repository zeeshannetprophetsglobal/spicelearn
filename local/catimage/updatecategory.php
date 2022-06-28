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
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/local/catimage/addimage.php');

defined('MOODLE_INTERNAL') || die();

require_login();

$url = new moodle_url('/local/catimage/updatecategory.php');

$context = context_system::instance();

require_capability('moodle/category:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('updateimage', 'local_catimage'));
$PAGE->set_heading(get_string('updateimage', 'local_catimage'));
$context = context_system::instance();
$mform = new addimage_form();
$manageurl = new moodle_url('/local/catimage/');

if ($mform->is_cancelled()) {   
    redirect($manageurl);
} else if ($data = $mform->get_data()) {

    
    $result = insert_image($data);
    
   if($result){
    redirect($manageurl);
    }else{
        redirect($url); 
    }
}

$mform->set_data($mform->get_data());
echo $OUTPUT->header();

$mform->display();
echo $OUTPUT->footer();