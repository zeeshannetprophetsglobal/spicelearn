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


 function add_score($alldata,$courseid){

    global $DB;
   // print_object($alldata);die;
    $insertObject = new stdClass;
    $insertObject->courseid = $courseid;
    $insertObject->url = $alldata->url;
    $insertObject->page = $alldata->page;
    $insertObject->pdf = $alldata->pdf;
    $insertObject->video = $alldata->video;
    $insertObject->audio = $alldata->audio;

    $checkdata = $DB->get_record('local_resourcescore',array('courseid'=>$courseid));
     
    if($checkdata){
        
        
        $sql = "UPDATE {local_resourcescore} SET url=". $alldata->url.", page = ". $alldata->page .",pdf = ". $alldata->pdf .",video=". $alldata->video .",audio=". $alldata->audio ." WHERE courseid = $courseid";
       $return =  $DB->execute($sql,null);
    }else{
        $return = $DB->insert_record('local_resourcescore', $insertObject, $returnid=true, $bulk=false);
   
    }
    
    return $return;
 }