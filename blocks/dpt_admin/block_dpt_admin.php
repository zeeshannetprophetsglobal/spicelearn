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
 * @package    block_dpt_admin
 * @author     Ayush <aayush.yahoo@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the current user's profile information.
 *
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_dpt_admin extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_dpt_admin');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isloggedin() or isguestuser()) {
            // Only real users can access myprofile block.
            return;
        }

       

        $this->content = new stdClass();
        $this->content->text = $this->dpt_admin_front_element();
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

    public function dpt_admin_front_element(){

        global $CFG;

        $html = '';
        $html .= html_writer::start_tag('table',array( 'class'=>"table"));
        $html .= html_writer::start_tag('tbody',array());
        $html .= html_writer::start_tag('tr',array());

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/admin/user.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/User_List.png','width'=>200));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');
        
        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/user/editadvanced.php?id=-1'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Add_New_User.png', 'width'=>200 ));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/admin/roles/assign.php?contextid=1'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Assign_Author_Role.png', 'width'=>200));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');

        $html .= html_writer::end_tag('tr');
        $html .= html_writer::start_tag('tr',array());       

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/admin/tool/uploaduser/index.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Bulk_User_Upload.png', 'width'=>200));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');   

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/cohort/index.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Create_Category.png', 'width'=>200));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/course/management.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/course_management.png','width'=>200));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');
        
        $html .= html_writer::end_tag('tr');
        $html .= html_writer::start_tag('tr',array());    

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/course/edit.php?category=0'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Create_Course.png', 'width'=>200 ));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Report.jpg', 'width'=>200 ));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');   

        $html .= html_writer::start_tag('td',array());
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/admin/user/user_bulk.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Bulk_User_Actions.png', 'width'=>200 ));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('td');
        
        $html .= html_writer::end_tag('tr');

        $html .= html_writer::end_tag('tbody');
        $html .= html_writer::end_tag('table');

        return $html;
    }
}
