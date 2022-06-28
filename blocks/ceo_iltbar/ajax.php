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

// defined('MOODLE_INTERNAL') || die();


require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/ceo_iltbar/locallib.php');


$action = optional_param('action',null,PARAM_TEXT);
$dep_id = optional_param('id',0,PARAM_INT);


if($action == 'getTrainingTypes'){  
    $data = [];
    $count = 0;
    $trainingData = get_ilt_training_type($dep_id);
    foreach($trainingData as $training){
        $data[$count]['id'] = $training->id;
        $data[$count]['name'] = get_category_parent_name($training->id);
        $count ++;
    }
    echo  json_encode($data);
}