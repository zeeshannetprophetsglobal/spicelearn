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
// MERCHANTABIILTY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_mycourses
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');

require_once($CFG->dirroot . '/blocks/mycourses/lib.php');

defined('MOODLE_INTERNAL') || die;

global $USER;

$action = optional_param('action', null, PARAM_TEXT);
$group = optional_param('group', null, PARAM_TEXT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$status = optional_param('status', 0, PARAM_INT);
$coursename = optional_param('coursename', null, PARAM_TEXT);

$renderer = $PAGE->get_renderer('block_mycourses');

if($action == 'category_filtter'){
    
    echo $renderer->user_courses_by_category($categoryid,$group,$status,$coursename);
}