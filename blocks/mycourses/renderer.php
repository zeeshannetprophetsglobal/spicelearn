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
// MERCHANTABIILTY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_mycourses
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_tag\output\tag;

require_once($CFG->dirroot . '/blocks/mycourses/lib.php');

defined('MOODLE_INTERNAL') || die();
class block_mycourses_renderer extends plugin_renderer_base
{

    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

    public function user_ilt_courses(){

        global $CFG,$USER,$DB;

        $html ='';
        $courses = block_mycourses_get_user_courses();
        // print_object($courses);die;
        $table = new html_table();
        $table->attributes['class'] = 'table generaltable table-dark-header';
        $table->id = 'darkheader';
        $table->head = array(
            get_string('category', 'block_mycourses'),
            get_string('coursename', 'block_mycourses'),
            get_string('progress', 'block_mycourses'),
            get_string('status', 'block_mycourses'),
          
        );
        $cohort =  cohort_get_user_cohorts ($USER->id);
        $cohortdata = array_values($cohort);
        if ($cohortdata) {
            foreach ($courses as $course) {
                
                // print_object($rolename);die;
                
                $courselink = new moodle_url($CFG->wwwroot.'/course/view.php?id='.$course->id);
                $courseProgress = block_mycourses_course_progress($course->id);

                $progress =  html_writer::start_tag('div',array('class'=>"progress"));
                $progress .= html_writer::start_div($class='progress-bar',array('role'=>"progressbar",'style'=>"width: ".$courseProgress."%",'aria-valuenow'=>$courseProgress, 'aria-valuemin'=>"0",'aria-valuemax'=>"100" ));
                $progress .= html_writer::end_tag('div');

                $context = context_course::instance($course->id);
                $roles = get_user_roles($context, $USER->id, true);
                $role = key($roles);
                $rolename = $roles[$role]->shortname;
                // print_object($rolename);die;
                if ($rolename == 'teacher') {
                    // unset($table->head[2]);
                    // unset($table->head[3]);
                     $table->data[] = array(
                        layer_of_category_name($course->path,'ilt'),
                        html_writer::link($courselink, $course->fullname,array()), 
                        '-',
                        '-',
                    );

                }else{
                    $table->data[] = array(
                        layer_of_category_name($course->path,'ilt'),
                        html_writer::link($courselink, $course->fullname,array()),
                        $progress,
                        block_mycourses_course_status($course->id)
                       
                    );
                }
            }
            $html .= html_writer::table($table);
        }else{
            $html .= 'You are not in any department';
        }
        // print_object($cohortdata);die;

        return $html;

    }

    public function user_elearn_courses(){

        global $CFG;
        $html ='';
        $courses = block_mycourses_get_user_elearn_courses();
       // print_object($courses);

        $table = new html_table();
        $table->attributes['class'] = 'table generaltable table-dark-header';
        $table->id = 'darkheader';
        $table->head = array(
            get_string('category', 'block_mycourses'),
            get_string('coursename', 'block_mycourses'),
            get_string('progress', 'block_mycourses'),
            get_string('status', 'block_mycourses'),
          
        );
        foreach ($courses as $course) {
           
            $courselink = new moodle_url($CFG->wwwroot.'/course/view.php?id='.$course->id);
            $courseProgress = block_mycourses_course_progress($course->id);
            
            $progress =  html_writer::start_tag('div',array('class'=>"progress"));
            $progress .= html_writer::start_div($class='progress-bar',array('role'=>"progressbar",'style'=>"width: ".$courseProgress."%",'aria-valuenow'=>$courseProgress, 'aria-valuemin'=>"0",'aria-valuemax'=>"100" ));
            $progress .= html_writer::end_tag('div');
           
            $table->data[] = array(
                layer_of_category_name($course->path,Elearn),
                html_writer::link($courselink, $course->fullname,array()),
                $progress,
                block_mycourses_course_status($course->id)
               
            );
        }
        $html .= html_writer::table($table);

        return $html;

    }

    public function groups_filtter($departmentid){

        $html = '';
        
        $html .= html_writer::start_tag('div',array('class'=>'row pb-3'));

        $html .= html_writer::start_tag('div',array('class'=>'col-md-4')); 
        $html .= html_writer::start_tag('div',array('class'=>'row float-left')); 
        $html .= html_writer::start_tag('select',array('id'=>'dpt-filtter','class'=>"selectpicker", 'data-live-search'=>true));
        $html .= html_writer::tag('option',get_string('selectcategory','block_mycourses'),array('value'=>0));
        $html .= block_mycourse_category_filtter_option($departmentid);
        $html .= html_writer::end_tag('select');     
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div',array('class'=>'col-md-4 float-center'));
        $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'coursename','placeholder'=>'Search by course name'));
        $html .= html_writer::end_tag('div');

        $html .= html_writer::start_tag('div',array('class'=>'col-md-4 float-right'));
        $html .= html_writer::start_tag('div',array('class'=>'row float-right'));
        $html .= html_writer::start_tag('select',array('id'=>'status-filtter'));
        $html .= html_writer::tag('option',get_string('selectstatus','block_mycourses'),array('value'=>0));
        $html .= html_writer::tag('option',get_string('completed','block_mycourses'),array('value'=>1));
        $html .= html_writer::tag('option',get_string('inprogress','block_mycourses'),array('value'=>2));      
        $html .= html_writer::tag('option',get_string('notstart','block_mycourses'),array('value'=>3));      
        $html .= html_writer::end_tag('select');          
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('div');
       
        return $html;
    }


    public function user_courses_by_category($categoryid,$group,$status,$coursename){

        global $CFG;
        // print_object($group);die;
        $html ='';
        $courses = block_mycourses_get_user_courses_by_category($categoryid,$group,$coursename);
        $basecategory = '';
       if($group == 'ilt'){
        $basecategory = ILT;
       }else{
        $basecategory = Elearn;
       }
        $table = new html_table();
        $table->attributes['class'] = 'table generaltable table-dark-header';
        $table->id = 'darkheader';
        $table->head = array(
            get_string('category', 'block_mycourses'),
            get_string('coursename', 'block_mycourses'),
            get_string('progress', 'block_mycourses'),
            get_string('status', 'block_mycourses'),
          
        );

        //print_object($courses);die;
        if(!empty($courses)){

             
            $i = 0;
        foreach ($courses as $course) {
           
            $courselink = new moodle_url($CFG->wwwroot.'/course/view.php?id='.$course->id);
            $courseProgress = block_mycourses_course_progress($course->id);

             $progress =  html_writer::start_tag('div',array('class'=>"progress"));
            $progress .= html_writer::start_div($class='progress-bar',array('role'=>"progressbar",'style'=>"width: ".$courseProgress."%",'aria-valuenow'=>$courseProgress, 'aria-valuemin'=>"0",'aria-valuemax'=>"100" ));
            $progress .= html_writer::end_tag('div');
            $checkstatus = block_mycourses_course_status($course->id);
           
            if($status == 1){
              
                if($checkstatus == 'Completed'){
                   
                    $table->data[] = array(
                        layer_of_category_name($course->path,$group),
                        html_writer::link($courselink, $course->fullname,array()),
                        $progress,
                        block_mycourses_course_status($course->id)
                    
                    );

                    $i++;
                }
                              
             }elseif($status == 2){

                if($checkstatus == 'Inprogress'){
           
                    $table->data[] = array(
                        layer_of_category_name($course->path,$group),
                        html_writer::link($courselink, $course->fullname,array()),
                        $progress,
                        block_mycourses_course_status($course->id)
                    
                    );
                    $i++;
                }
               
             }elseif($status == 3){

                if($checkstatus == 'Not-started'){
           
                    $table->data[] = array(
                        layer_of_category_name($course->path,$group),
                        html_writer::link($courselink, $course->fullname,array()),
                        $progress,
                        block_mycourses_course_status($course->id)
                    
                    );
                    $i++;
                }
               
             }else{

                $table->data[] = array(
                    layer_of_category_name($course->path,$group),
                    html_writer::link($courselink, $course->fullname,array()),
                    $progress,
                    block_mycourses_course_status($course->id)
                
                );
             }

             
        }

            // if($i == 0){
            //     $table->data[] = array(get_string('coursenotenroll','block_mycourses'));
            // }
            
        }else{
            $table->data[] = array(
                get_string('coursenotenroll','block_mycourses')
            );
        }
        $html .= html_writer::table($table);

        return $html;

    }
}
