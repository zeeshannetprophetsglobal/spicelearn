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
 * Strings for component 'local_catimage', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    local_catimage
 */

require_once ($CFG->dirroot . '/local/catimage/lib.php');
defined('MOODLE_INTERNAL') || die();

function get_all_categorys(){

    global $DB;

    $result = $DB->get_records('course_categories',null);

    return $result;
}

function insert_image($data){

    global $DB;
   // print_object($data);die;
    $category = new stdClass();
    $category->category_id = $data->category;
    $category->visible = 0;
    $category->image = $data->catimage_filemanager;
    $category->createtime = time();

    if($recordid = $DB->insert_record('local_category_image',$category,$returnid = true, $bulk = false)){
        $context = context_system::instance();
        if (!empty($data->catimage_filemanager)) {
            $options = array(
                'maxfiles' => 1,
                'maxbytes' => 10485760, // 5MB (2MB=2097152, 5MB=5242880, 10MB=10485760)
                'subdirs' => 0,
                'accepted_types' => ['jpg', 'png', 'svg'],
                'context' => context_system::instance(),
            );
            $filearea = 'catimage';
            $itemid = $recordid;
        //  print_object($itemid);die;
            $result = file_postupdate_standard_filemanager($data, 'catimage', $options, $context, 'local_catimage', $filearea, $itemid);
            return $recordid;
        }
        
    }else{
        return false;
    }
}

