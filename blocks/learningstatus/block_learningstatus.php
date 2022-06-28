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
 * Contains the class for the userstatus block.
 *
 * @package    block_learningstatus
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir."/completionlib.php");

use core_completion\progress;

/**
 * userstatus block class.
 *
 * @package    block_learningstatus
 */
class block_learningstatus extends block_base {

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_learningstatus');
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {

        global $OUTPUT,$USER,$DB,$CFG;
        
        $mycourses = enrol_get_my_courses('visible');
        $enrollcourse = count($mycourses);
        
        $completed = 0 ;
        $noycomplete = 0;
        $registercourse = 0;
        $critical = 0;
        foreach($mycourses as $enrolcourse){

            if($enrolcourse->visible == 1) {

            $course = $DB->get_record('course', array('id' => $enrolcourse->id), '*', MUST_EXIST);
            $lastaccess = $DB->get_record('user_lastaccess', array('userid'=>$USER->id,'courseid' => $enrolcourse->id));
            $cinfo = new completion_info($course);               

            if ($cinfo->is_course_complete($USER->id)) {
                $completed++;    
            }else if($lastaccess){
                $noycomplete++;
            }else{
                $registercourse++;
            }

        }

        }
        // echo $completed;die;
        $chart = new \core\chart_pie();
        $chart->set_doughnut(true); // Calling set_doughnut(true) we display the chart as a doughnut.
        $serie1 = new core\chart_series('Number of courses', [$registercourse,$noycomplete,$completed]);
        $chart->add_series($serie1);
        $chart->set_labels(['Not started','In progress','Completed']);
        $serie1->set_colors(['#F93450','#FFD600','#6184F0']);
        

        $this->content = new stdClass();
        if(!empty($mycourses)){
          $url = new moodle_url($CFG->wwwroot.'/blocks/learningstatus/summary.php',array('userid'=>$USER->id));
           $this->content->text = $OUTPUT->render($chart).html_writer::link($url,'More detail',array('class'=>'btn btn-secondary viewmore'));
          // $this->content->text = html_writer::link($url,'More detail');
      }else{
        $this->content->text = 'No course enrolled ';
    }
    $this->content->footer = '';

    return $this->content;
}

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('my' => true);
    }

}
