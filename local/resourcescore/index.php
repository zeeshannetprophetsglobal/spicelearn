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
 * Strings for component 'local_resourcescore', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_resourcescore
 */

require_once('../../config.php');

//require_once($CFG->dirroot . '/local/resourcescore/lib.php');

defined('MOODLE_INTERNAL') || die();

require_login();

$url = new moodle_url('/local/resourcescore/index.php');

$context = context_system::instance();
    
require_capability('moodle/category:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_resourcescore'));
$PAGE->set_heading(get_string('pluginname', 'local_resourcescore'));

 
echo $OUTPUT->header();

$html = '';

$html .= html_writer::tag('h4',get_string('clickoncourse','local_resourcescore'),array());
$allcourse = get_courses();
//print_object($allcourse);
$html .= html_writer::start_tag('div',array());
$table = new html_table();
$table->attributes['class'] = 'table generaltable';
$table->align = array('left', 'center', 'center', 'center','center','center');
$table->size = array('50%', '10%', '10%', '10%','10%','10%');
$table->data = array();
$table->head = array(get_string('coursename', 'local_resourcescore'), get_string('url', 'local_resourcescore'),
    get_string('page','local_resourcescore'),get_string('pdf', 'local_resourcescore'),get_string('video', 'local_resourcescore'),get_string('audio', 'local_resourcescore'));

    $row = array();
               
    
    foreach($allcourse as $data){
        if($data->id != 1 ){
        $courselink = new moodle_url($CFG->wwwroot.'/local/resourcescore/addscore.php',array('courseid'=>$data->id));
        $defaltdata = $DB->get_record('local_resourcescore',array('courseid'=>$data->id));   
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

        $row[0] = html_writer::link($courselink,$data->fullname,array());
        $row[1] = $url;
        $row[2] = $page;
        $row[3] = $pdf;
        $row[4] = $video;
        $row[5] = $audio;
       
        $table->data[] = $row;
    }
       
    }
    $html .= html_writer::table($table);
   

$html .= html_writer::end_tag('div');
echo $html;
echo $OUTPUT->footer();