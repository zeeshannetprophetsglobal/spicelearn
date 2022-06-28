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
 * @package    local_resourcescore
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');

defined('MOODLE_INTERNAL') || die;

require_once "$CFG->libdir/formslib.php";

class score_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG, $DB;

        $mform = $this->_form; // Don't forget the underscore!
        $courseid = optional_param('courseid', 0,PARAM_INT);
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('requestid', $courseid);

        $defaltdata = $DB->get_record('local_resourcescore',array('courseid'=>$courseid));

        if(!empty($defaltdata)){
            $url = $defaltdata->url;
            $page = $defaltdata->page;
            $pdf = $defaltdata->pdf;
            $video = $defaltdata->video;
            $audio = $defaltdata->audio;
        }else{
            $url = 0;
            $page = 0;
            $pdf = 0;
            $video =0;
            $audio = 0;
        }
       // print_object($defaltdata);die;
        $mform->addElement('text', 'url', get_string('url', 'local_resourcescore'), 'maxlength="100"', 'pattern="[0-9]"');
        $mform->setType('url', PARAM_INT);
       // $mform->addRule('url', get_string('misstitle', 'local_resourcescore'), 'required', null, 'client');
        $mform->setDefault('url', $url);
      

        $mform->addElement('text', 'page', get_string('page', 'local_resourcescore'), 'maxlength="100"');
        $mform->setType('page', PARAM_INT);
       // $mform->addRule('url', get_string('misstitle', 'local_resourcescore'), 'required', null, 'client');
        $mform->setDefault('page', $page);

        $mform->addElement('text', 'pdf', get_string('pdf', 'local_resourcescore'), 'maxlength="100"');
        $mform->setType('pdf', PARAM_INT);
       // $mform->addRule('url', get_string('misstitle', 'local_resourcescore'), 'required', null, 'client');
        $mform->setDefault('pdf', $pdf);

        $mform->addElement('text', 'video', get_string('video', 'local_resourcescore'), 'maxlength="100"');
        $mform->setType('video', PARAM_INT);
       // $mform->addRule('url', get_string('misstitle', 'local_resourcescore'), 'required', null, 'client');
        $mform->setDefault('video', $video);

        $mform->addElement('text', 'audio', get_string('audio', 'local_resourcescore'), 'maxlength="100"');
        $mform->setType('audio', PARAM_INT);
       // $mform->addRule('url', get_string('misstitle', 'local_resourcescore'), 'required', null, 'client');
        $mform->setDefault('audio', $audio);

    
        
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);

    }
    //Custom validation should be added here
    public function validation($data, $files)
    {
        return array();
    }
}

class search_form extends moodleform
{
    public function definition()
    {
        $search_form = $this->_form;
        $search_form->addElement('text', 'search', get_string('search_txt', 'local_resourcescore'), array('placeholder' => 'Search by email'));
        $search_form->setDefault('search', '');
        $search_form->setType('search', PARAM_RAW);
        $this->add_action_buttons($cancel = false, $submitlabel = 'Search');
    }
}
