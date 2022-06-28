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
 * Strings for component 'local_scheduler', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_scheduler
 */


/**
 * Adding learning program link in sidebar
 * @param navigation_node $nav navigation node
 */

function local_scheduler_extend_navigation(navigation_node $nav) {
    global $CFG, $PAGE, $COURSE,$USER;

    // Check if users is logged in to extend navigation.
    
    if (!isloggedin() || !is_siteadmin()) {
        return;
    }

    $icon = new pix_icon('i/star', '');
    $node = $nav->add(
        get_string('pluginname', 'local_scheduler'),
        new moodle_url($CFG->wwwroot . '/local/scheduler/view.php'),
        navigation_node::TYPE_CUSTOM,
        'scheduler',
        'scheduler',
        $icon
    );
    $node->showinflatnavigation = true;   
    
} 
