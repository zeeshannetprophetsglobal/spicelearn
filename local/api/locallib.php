<?php
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
 * External Custom Api
 *
 * @package    localapi
 */
 
require_once(__DIR__ . '/../config.php');
require_once($CFG->wwwwroot . '/local/api/locallib.php');

function get_all_course_module($courseid){
	
	$activity = get_array_of_activities($courseid);
	
	return $activity;
}

