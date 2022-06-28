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
 * @package    local_scheduler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/local/scheduler/locallib2.php');

defined('MOODLE_INTERNAL') || die();

use core_completion\progress;

class local_scheduler_renderer extends plugin_renderer_base
{

    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }
    public function user_trascript($department,$subject,$fromdate,$enddate){

        global $CFG,$OUTPUT;

        $html  = '';

        $userdata = get_scheduler_data_test2($fromdate,$enddate,$department,$subject);
        $table = new html_table();
        /* Styling done using HTML table and CSS */
        $table->id = 'datatable';
        $table->attributes['class'] = 'table generaltable';
        $table->align = array('left', 'left', 'left', 'center');
        $table->data = array();

        $table->head = array(
            get_string('userid', 'local_scheduler'),
            get_string('username', 'local_scheduler'),
            get_string('fullname', 'local_scheduler'),
            get_string('email','local_scheduler'),
            get_string('course_id', 'local_scheduler'),
            get_string('course_name', 'local_scheduler'),
            get_string('category_id', 'local_scheduler'),
            get_string('category_name', 'local_scheduler'),
            get_string('category_path', 'local_scheduler'),
            get_string('enrollment_date', 'local_scheduler'),
            get_string('course_startdate', 'local_scheduler'),
            get_string('course_end_date', 'local_scheduler'),
            get_string('completion_date', 'local_scheduler'),
            get_string('course_status', 'local_scheduler'),
            get_string('progress', 'local_scheduler'),
            get_string('grade', 'local_scheduler'),
            get_string('quiz_score', 'local_scheduler'),
            get_string('quiz_score_max', 'local_scheduler'),
            get_string('rank', 'local_scheduler'),
            get_string('department', 'local_scheduler'),
            get_string('zone', 'local_scheduler'),
            get_string('l1', 'local_scheduler'),
            get_string('l2', 'local_scheduler')


        );

        $row = array();              
        if(!empty($userdata)){                       
            foreach($userdata as $data){

                $courseobject = get_course($data->course_id);
                $progress = progress::get_course_progress_percentage($courseobject, $data->userid);

                $quizdata = get_final_quiz_score2($data->course_id,$data->userid);

                $user_fields = get_user_fields_data2($data->userid);

                $row[0] = $data->userid;
                $row[1] = $data->username;
                $row[2] = $data->fullname;
                $row[3] = $data->email;
                $row[4] = $data->course_id;
                $row[5] = $data->course_name;
                $row[6] = $data->category_id;
                $row[7] = $data->category_name;
                $row[8] = get_category_parent_name($data->category);
                $row[9] = ($data->enrollment_date)?date('d/m/Y',$data->enrollment_date):'';
                $row[10] = ($data->startdate)?date('d/m/Y',$data->startdate):'';
                $row[11] = ($data->enddate)?date('d/m/Y',$data->enddate):'';
                $row[12] = ($data->completion_date)?date('d/m/Y',$data->completion_date):'';
                $row[13] = $data->course_status;
                $row[14] = $progress;
                $row[15] = $data->grade;
                $row[16] = $quizdata->finalgrade;
                $row[17] = $quizdata->rawgrademax;
                $row[18] = $user_fields['designation']->data;
                $row[19] = $user_fields['designation']->department;
                $row[20] = $user_fields['zone']->data;
                $row[21] = $user_fields['L1']->data;
                $row[22] = $user_fields['l2']->data;
                $table->data[] = $row;     
            }
        }else{
            $table->data[] = ['Data not found'];   
        }   

        $html .= html_writer::table($table);

        return $html;
    }

}
