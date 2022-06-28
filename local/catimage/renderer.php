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
 * @package    local_catimage
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/local/catimage/lib.php');
defined('MOODLE_INTERNAL') || die();

class local_catimage_renderer extends plugin_renderer_base
{

    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

   public function get_all_category_detail()
   {
       global $OUTPUT,$CFG;
       $alldata = get_cat_img_detail();
       //print_object($alldata);
        $html = '';
        $table = new html_table();
       /* Styling done using HTML table and CSS */
               $table->attributes['class'] = 'table generaltable';
               $table->align = array('left', 'left', 'left', 'center');
       //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
               $table->data = array();
               $table->head = array(get_string('sn', 'local_catimage'), get_string('categoryname', 'local_catimage'),
                   get_string('image','local_catimage'),get_string('action', 'local_catimage'));
       
               $row = array();
               
                   $sn = 1;
                   foreach($alldata as $data){
                       $row[0] = $sn;
                       $row[1] = local_catimga_layer_of_category_name($data->path);
                       $src = fileview($data->id);
                       $image = html_writer::img($src,null,array('hight'=>100,'width'=>100));
                       $row[2] = $image;

                       $editicon = $OUTPUT->pix_icon('t/edit', get_string('edit','local_catimage'));
                       $editlink = new moodle_url($CFG->wwwroot.'/local/catimage/updatecategory.php',array('id'=>$data->category_id,'recordid'=>$data->id,'action'=>'edit') );
                       $edit = html_writer::link($editlink,$editicon,array());
                       $deleteicon = $OUTPUT->pix_icon('t/delete', get_string('delete','local_catimage'));
                       $deletelink = new moodle_url($CFG->wwwroot.'/local/catimage/index.php',array('recordid'=>$data->id,'action'=>'delete'));
                       $delete = html_writer::link($deletelink,$deleteicon,array());
                       
                       $row[3] = $edit.' '.$delete;
                      
                       $table->data[] = $row;
                       $sn++;
                   }
                   $html .= html_writer::table($table);
       

   
    return $html;
   }
}
