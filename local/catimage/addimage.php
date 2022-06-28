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

require_once '../../config.php';
require_once "$CFG->libdir/formslib.php";
require_once $CFG->dirroot . '/local/catimage/lib.php';

defined('MOODLE_INTERNAL') || die();

class addimage_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG,$DB;

        $mform = $this->_form; // Don't forget the underscore!
        $id = optional_param('id', 0, PARAM_INT);
        $action = optional_param('action', '', PARAM_RAW);
        $recordid = optional_param('recordid', 0, PARAM_INT);
        $category = array();
        $allcategory = get_all_categorys();
        $category[null] = get_string('selectcategory', 'local_catimage');

        foreach ($allcategory as $cat) {
        
            $category[$cat->id] = $this->layer_of_category_name($cat->path);
        }
        
        if ($action == 'edit') {
            $name = cat_name_by_id($id);
            $mform->addElement('static', 'category', get_String('category', 'local_catimage'));
            $mform->setDefault('category', $name);
            $mform->addElement('hidden', 'action', $action);
            $mform->setType('action', PARAM_TEXT);
            $mform->addElement('hidden', 'id', $id);
            $mform->setType('id', PARAM_INT);
            $mform->addElement('hidden', 'recordid', $recordid);
            $mform->setType('recordid', PARAM_INT);
        } else {
            $mform->addElement('select', 'category', get_string('selectcategory', 'local_catimage'), $category);
            $mform->addRule('category', get_string('mmisscategory', 'local_catimage'), 'required', null, 'client');
        }
            //image upload
            $options = array(
                'maxfiles' => 1,
                'maxbytes' => 10485760, // 5MB (2MB=2097152, 5MB=5242880, 10MB=10485760)
                'subdirs' => 0,
                'accepted_types' => ['jpg', 'png', 'svg'],
                'context' => context_system::instance(),
            );

            $mform->addElement('filemanager', 'catimage_filemanager', get_string('uploadfile', 'local_catimage'), null, $options);
            $mform->setType('catimage_filemanager', PARAM_TEXT);
            $mform->addRule('catimage_filemanager', get_string('mmisscategoryfilemanager', 'local_catimage'), 'required', null, 'client');

            $buttonarray = array();
            $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
            $buttonarray[] = $mform->createElement('cancel');
            $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
        
    }

    public function layer_of_category_name($path){

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

}
