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
 * @package    block_course_search
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_tag\output\tag;

require_once($CFG->dirroot . '/blocks/course_search/lib.php');

defined('MOODLE_INTERNAL') || die();
class block_course_search_renderer extends plugin_renderer_base
{

    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

    public function user_courses_by_name($coursename){

        global $CFG;
        $html ='';
        $courses = block_course_search_get_user_courses_by_category($coursename);

        $table = new html_table();
        $table->attributes['class'] = 'table generaltable table-dark-header';
        $table->id = 'darkheader';
        $table->head = array(
            get_string('category', 'block_course_search'),
            get_string('coursename', 'block_course_search'),

        );

        if(!empty($courses)){


            $i = 0;
            foreach ($courses as $course) {

                $courselink = new moodle_url($CFG->wwwroot.'/course/view.php?id='.$course->id);
                $table->data[] = array(
                    course_search_layer_of_category_name($course->path,$group),
                    html_writer::link($courselink, $course->fullname,array()),

                );

            }
            
        }else{
            $table->data[] = array(
                get_string('coursenotenroll','block_course_search')
            );
        }
        $html .= html_writer::table($table);

        return $html;

    }
}
