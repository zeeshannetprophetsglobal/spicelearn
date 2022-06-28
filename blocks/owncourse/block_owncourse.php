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
 * Block displaying information about current logged-in user.
 *
 * This block can be used as anti cheating measure, you
 * can easily check the logged-in user matches the person
 * operating the computer.
 *
 * @package    block_owncourse
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use block_myoverview\output\main;
use block_owncourse\output\coursetab;


/**
 * Displays the current user's profile information.
 *
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_owncourse extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_owncourse');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {

        global $CFG,$PAGE;
        

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin() or isguestuser()) {
            // Only real users can access myprofile block.
            return;
        }

        // $group = get_user_preferences('block_myoverview_user_grouping_preference');
        // $sort = get_user_preferences('block_myoverview_user_sort_preference');
        // $view = get_user_preferences('block_myoverview_user_view_preference');
        // $paging = get_user_preferences('block_myoverview_user_paging_preference');
        // $customfieldvalue = get_user_preferences('block_myoverview_user_grouping_customfieldvalue_preference');

    //     $renderable = new coursetab($group, $sort, $view, $paging, $customfieldvalue);
    //     $renderer =  $PAGE->get_renderer('block_myoverview');
    //    // print_object($renderer);die;
        $this->content = new stdClass();
        $this->content->text = $this->owncourselist();//$renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * allow more than one instance of the block on a page
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        //allow more than one instance on a page
        return false;
    }

    /**
     * allow instances to have their own configuration
     *
     * @return boolean
     */
    function instance_allow_config() {
        //allow instances to have their own configuration
        return false;
    }

    /**
     * instance specialisations (must have instance allow config true)
     *
     */
    public function specialization() {
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all'=>true);
    }

    /**
     * post install configurations
     *
     */
    public function after_install() {
    }

    /**
     * post delete configurations
     *
     */
    public function before_delete() {
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = !empty($this->config) ? $this->config : new stdClass();

        return (object) [
            'instance' => $configs,
            'plugin' => new stdClass(),
        ];
    }

    public function owncourselist(){

        global $DB,$USER,$CFG;

       $sql = 'SELECT c.id,c.fullname FROM {course} as c JOIN {logstore_standard_log} as l ON c.id = l.courseid where l.userid = '.$USER->id.' AND l.eventname LIKE "%course_created%"';
        $coursedata = $DB->get_records_sql($sql,null);
        // $coursedata = $DB->get_records('logstore_standard_log',array('eventname'=>'\core\event\course_created','userid'=>$USER->id));
       // print_object($coursedata);die;  
        if(!empty($coursedata)){
            $html = '';
            $html .= html_writer::start_tag('ul',null);
                foreach($coursedata as $data){
                   
                    $course = get_course($data->id);
                   $courselink = new moodle_url($CFG->wwwroot.'/course/view.php',array('id'=>$data->id));
                    $html .= html_writer::tag('li', html_writer::link($courselink,$course->fullname),array());

                }
                $html .= html_writer::end_tag('ul');

                return $html;

            }else{

                return html_writer::tag('h4',get_string('notcoursefind','block_owncourse'),array());
            }
    } 
}
