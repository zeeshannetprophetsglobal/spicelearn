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
 * Strings for component 'local_api', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_api
 */

require_once('../../config.php');

require_once($CFG->dirroot . "/local/api/lib.php");

$userid = optional_param('userid',0,PARAM_INT);

global $DB;
// die('asdsd');
if(!empty($userid)){

    $LmsScore = user_profile_score($userid);
        //print_object($LmsScore);die;
    $checkdata = $DB->get_record('local_user_leaderboard',array('userid'=>$userid));
        $finalscore = 0;
            if(empty($checkdata)){

                    $insertObject = new stdClass; 
                    $insertObject->userid = $userid;
                    $insertObject->departmentid = get_user_department_id($userid);
                    $insertObject->gamedata = '';
                    $insertObject->gamescore = '';
                    $insertObject->lmsscore = $LmsScore;
                    $insertObject->finalscore = '';
                    $insertObject->createtime = time();
                    $insertObject->updatetime = TIME();

                    $insertdata = $DB->insert_record('local_user_leaderboard', $insertObject, $returnid=true, $bulk=false);

                    $finalscore = $LmsScore;

            }else{

                    $finalscore = $LmsScore + $checkdata->gamescore;

                    $sql = "UPDATE {local_user_leaderboard} SET lmsscore = $LmsScore,finalscore = $finalscore, updatetime = ".time()." WHERE userid = ".$userid ;
                    
                    $DB->execute($sql,null);

            }
}else{

    echo 'please enter user id in url.';
}
