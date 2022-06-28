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

use core_calendar\local\event\forms\update;

defined('MOODLE_INTERNAL') || die();


function local_catimage_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    
    $filename = array_pop($args);
//    $itemid = array_pop($args);
    $itemid = array_shift($args);

    $fs = get_file_storage();

    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';
    if (!$file = $fs->get_file($context->id, 'local_catimage', $filearea, $itemid, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
    }
    // finally send the file
    send_stored_file($file, null, 0, $forcedownload, $options);
}


function get_all_categorys(){

    global $DB;

    $result = $DB->get_records('course_categories',null);

    return $result;
}

function insert_image($data){

    global $DB;
   // print_object($data);die;
//    $action = optional_param('action', NULL, PARAM_TEXT);
//    $action = $data->action;
   $context = context_system::instance();
    if(!empty($data->action) && $data->action == 'edit'){
    $sql = "UPDATE {local_category_image} SET image = $data->catimage_filemanager WHERE category_id = $data->id";

       $update =  $DB->execute($sql, null, $returnid = true, $bulk = false);
      
        if (!empty($data->catimage_filemanager) && $update) {
            $options = array(
                'maxfiles' => 1,
                'maxbytes' => 10485760, // 5MB (2MB=2097152, 5MB=5242880, 10MB=10485760)
                'subdirs' => 0,
                'accepted_types' => ['jpg', 'png', 'svg'],
                'context' => context_system::instance(),
            );
            $filearea = 'catimage';
            $itemid = $data->recordid;
        //  print_object($itemid);die;
            $result = file_postupdate_standard_filemanager($data, 'catimage', $options, $context, 'local_catimage', $filearea, $itemid);
           
            return true;
        }
    
    
    }else{
        $category = new stdClass();
        $category->category_id = $data->category;
        $category->visible = 0;
        $category->image = $data->catimage_filemanager;
        $category->createtime = time();
    $check = $DB->get_record('local_category_image',array('category_id'=>$data->category));

    if(empty($check)){
    if($recordid = $DB->insert_record('local_category_image',$category,$returnid = true, $bulk = false)){
        
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

}else{
    return false;
}
    }
}

function fileview($id)
{
     
    $out = '';
    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'local_catimage', 'catimage', $id);
    $url = '';
    foreach ($files as $file) {
        $filename = $file->get_filename();
        if ($filename !== '.') {
            $path = '/' . $file->get_contextid() . '/local_catimage/catimage/' .
                $id . '/' . $filename;
            $url = moodle_url::make_file_url('/pluginfile.php', $path);
        }
    }
    $out = $url;

    return $out;
}

function get_cat_img_detail()
{
    global $DB;

    $sql = "SELECT b.id,b.category_id,a.name,b.image,b.visible,a.path from {course_categories} as a JOIN {local_category_image} as b ON a.id = b.category_id";

    $data = $DB->get_records_sql($sql,null);

    return $data;

}

 function cat_name_by_id($id)
{
    global $DB;

    $data = $DB->get_field('course_categories', 'name', array('id'=>$id) , $strictness=IGNORE_MISSING);
    //print_object($data);die;
    return $data;
}

function delete_record($recordid)
{
    global $DB;
    
    if( $DB->delete_records('local_category_image', array('id'=>$recordid)) ){
        
        return true;
    }else{
        die;
        return false;
    }

}

function local_catimga_layer_of_category_name($path){

    global $DB;   


    $NameExpload = explode("/",$path);
    $subjectdata = array_slice($NameExpload, 1);  


    $count = count($subjectdata);
        // echo $count; 
    $FullNameCategory = '';
    $categorySlash = 0;
    if($count != 0){
        foreach($subjectdata as $data){
            $categorySlash ++;
            $category =  $DB->get_record('course_categories',array('id'=>$data));

            if($count -$categorySlash > 0){
                $FullNameCategory .= $category->name.'/';
            }else{
                $FullNameCategory .= $category->name;
            }

        }
    }
    return $FullNameCategory;
}