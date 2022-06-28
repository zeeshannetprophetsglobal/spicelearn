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
 * Class definition for the Recently accessed courses block.
 *
 * @package    block_mycourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Recently accessed courses block class.
 *
 * @package    block_mycourses
 * @copyright  Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mycourses extends block_base {
    /**
     * Initialize class member variables
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_mycourses');
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }

       

        $this->content = new stdClass();
        $this->content->text = $this->mycourses_front_element();
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

    /**
     * Allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $configs = get_config('block_mycourses');

        return (object) [
            'instance' => new stdClass(),
            'plugin' => $configs,
        ];
    }

    public function mycourses_front_element(){

        global $CFG;

        $html = '';
        // $html .= html_writer::start_tag('table',array());
        // $html .= html_writer::start_tag('tbody',array());
        // $html .= html_writer::start_tag('tr',array());

        // $html .= html_writer::start_tag('td',array());
        // $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/blocks/mycourses/ilt.php'));
        // $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/mycourses/pix/ILT.png'));
        // $html .= html_writer::end_tag('a');
        // $html .= html_writer::end_tag('td');
        
        // $html .= html_writer::start_tag('td',array());
        // $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/blocks/mycourses/elearning.php'));
        // $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/mycourses/pix/elearing.png'));
        // $html .= html_writer::end_tag('a');
        // $html .= html_writer::end_tag('td');

        // $html .= html_writer::end_tag('tr');
        // $html .= html_writer::end_tag('tbody');
        // $html .= html_writer::end_tag('table');

        $html .= html_writer::start_tag('div',array('class'=>'row'));
        $html .= html_writer::start_tag('div',array('class'=>'col-md-6'));
        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/blocks/mycourses/ilt.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/mycourses/pix/ILT.png'));
        $html .= html_writer::end_tag('a'); 
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div',array('class'=>'col-md-6'));

        $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/blocks/mycourses/elearning.php'));
        $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/mycourses/pix/elearing.png'));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');

        return $html;
    }
}
