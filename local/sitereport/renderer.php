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
 * @package    local_sitereport
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/local/sitereport/locallib.php');

defined('MOODLE_INTERNAL') || die();

class local_sitereport_renderer extends plugin_renderer_base
{

    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

    public function report_card_list()
    {
     global $OUTPUT,$CFG;

     $html = '';
     $html .= html_writer::start_tag('table',array( 'class'=>"table"));
     $html .= html_writer::start_tag('tbody',array());
     $html .= html_writer::start_tag('tr',array());

     $html .= html_writer::start_tag('td',array());
     $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/userreport.php'));
     $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/local/sitereport/pix/Trainee_Report.png','width'=>250));
     $html .= html_writer::end_tag('a');
     $html .= html_writer::end_tag('td');
     
     $html .= html_writer::start_tag('td',array());
     $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/coursereport.php'));
     $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/local/sitereport/pix/Course_Report.png', 'width'=>250 ));
     $html .= html_writer::end_tag('a');
     $html .= html_writer::end_tag('td');


     $html .= html_writer::start_tag('td',array());
     $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/author_report.php'));
     $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/local/sitereport/pix/Author_Report.png', 'width'=>250));
     $html .= html_writer::end_tag('a');
     $html .= html_writer::end_tag('td');

     $html .= html_writer::end_tag('tr');

     $html .= html_writer::start_tag('tr',array());  
     
     $html .= html_writer::start_tag('td',array());
     $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/teacher_report.php'));
     $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/local/sitereport/pix/Instructor_Report.png','width'=>250));
     $html .= html_writer::end_tag('a');
     $html .= html_writer::end_tag('td');
     
     $html .= html_writer::start_tag('td',array());
     $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/non_teacher_report.php'));
     $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/local/sitereport/pix/Non-Edit_Instructor_Report.png', 'width'=>250 ));
     $html .= html_writer::end_tag('a');
     $html .= html_writer::end_tag('td');

    //    $html .= html_writer::start_tag('td',array());
    //    $html .= html_writer::start_tag('a',array('href'=>$CFG->wwwroot.'/local/sitereport/'));
    //    $html .= html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/blocks/dpt_admin/pix/Create_Course.png', 'width'=>250 ));
    //    $html .= html_writer::end_tag('a');
    //    $html .= html_writer::end_tag('td');
     
     $html .= html_writer::end_tag('tr');

     $html .= html_writer::end_tag('tbody');
     $html .= html_writer::end_tag('table');
     

     
     return $html;
 }

 public function users_filters(){

    $html = '';

    $html .= html_writer::start_tag('div',array('class'=>'row pb-3' ,'id'=>'trainee_filtter'));

    $html .= html_writer::start_tag('div',array('class'=>'col-md-1'));
    $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'base','placeholder'=>'Sreach by base'));
    $html .= html_writer::end_tag('div');
    $html .= html_writer::start_tag('div',array('class'=>'col-md-2'));
    $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'base','placeholder'=>'Sreach by base'));
    $html .= html_writer::end_tag('div');
    $html .= html_writer::start_tag('div',array('class'=>'col-md-2'));
    $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'zone','placeholder'=>'Sreach by zone'));
    $html .= html_writer::end_tag('div');
    $html .= html_writer::start_tag('div',array('class'=>'col-md-2'));
    $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'rank','placeholder'=>'Sreach by rank'));
    $html .= html_writer::end_tag('div');
    $html .= html_writer::start_tag('div',array('class'=>'col-md-2'));
    $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'l1','placeholder'=>'Sreach by L1'));
    $html .= html_writer::end_tag('div');
    $html .= html_writer::start_tag('div',array('class'=>'col-md-2'));
    $html .= html_writer::tag('input','',array('class'=>'form-control','id'=>'l2','placeholder'=>'Sreach by L2'));
    $html .= html_writer::end_tag('div');
    $html .= html_writer::start_tag('div',array('class'=>'col-md-1'));
    $html .= html_writer::tag('button','',array('class'=>'btn btn-primary','value'=>'submit'));
    $html .= html_writer::end_tag('div');

    $html .= html_writer::end_tag('div');

    return $html;
}
public function user_report($page,$ecn,$base,$zone,$rank,$l1,$l2){

    global $CFG,$OUTPUT;
    
    $html  = '';
    
    $userdata = department_user_detail($page,$ecn,$base,$zone,$rank,$l1,$l2);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center');
                //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('ecn', 'local_sitereport'),
        get_string('name', 'local_sitereport'),
        get_string('email'),
        get_string('base', 'local_sitereport'),
        get_string('rank', 'local_sitereport'),
        get_string('totalcourseenroll', 'local_sitereport'),
        get_string('complete', 'local_sitereport'),
        get_string('inprogress', 'local_sitereport'),
        get_string('notstarted', 'local_sitereport')
        
        
    );
    
    $row = array();
    $sn = 1; 
    //print_object($userdata);die;                                       
    if(!empty($userdata)){                       
        foreach($userdata as $data){

            $designation =  get_profile_field($data->id);
            $statusCount = course_progress_count($data->id,5);
            
            $row[0] = $data->index;
            $row[1] = $data->username;                                
            $row[2] = $data->firstname . ' ' . $data->lastname;
            $row[3] = $data->email;
            $row[4] = $data->city;
            $row[5] = $designation['designation']->data;
            $studentCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'roleid'=>5));
           // $row[6] = html_writer::link($studentCourseLink,count(enrol_get_users_courses($data->id,false)));
            $row[6] = html_writer::link($studentCourseLink,$statusCount['totalcourse']);
            $complteCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'action'=>'complete','roleid'=>5));
            $inprogressCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'action'=>'inprogress','roleid'=>5));
            $notstartedCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'action'=>'notstarted','roleid'=>5));
            $row[7] = html_writer::link($complteCourseLink,$statusCount['complete']);
            $row[8] = html_writer::link($inprogressCourseLink,$statusCount['inprogress']);
            $row[9] = html_writer::link($notstartedCourseLink,$statusCount['notstarted']);

           

            $table->data[] = $row;
            $sn++;        
        }
    }else{
        $table->data[] = ['Data not found'];   
    }   
    
    $html .= html_writer::table($table);
    
    $datacount = department_user_count($ecn,$base,$zone,$rank,$l1,$l2);

    $parameter = array();
    $parameter['page'] = $page;   
    $parameter['ecn'] = $ecn;
    $parameter['base'] = $base;
    $parameter['zone'] = $zone;
    $parameter['rank'] = $rank;
    $parameter['l1'] = $l1;
    $parameter['l2'] = $l2;
    $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/userreport.php', $parameter);
    
   
    $html .= html_writer::start_tag('div',array('class'=>'row')); 
    $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
    $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
    $html .= html_writer::end_tag('div');
    $html .= html_writer::end_tag('div');

    
    
    return $html;
}

public function course_report($page,$type,$iltsubject,$elsubject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate){

    global $CFG,$OUTPUT;

    $html  = '';
    $coursedata = get_department_course($page,$type,$iltsubject,$elsubject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate);

    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
                // $table->align = array('left', 'left', 'left', 'left','left');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('course'),                               
        get_string('type', 'local_sitereport'),
        get_string('subject', 'local_sitereport'),
        get_string('instructor', 'local_sitereport'),
        get_string('completeuser', 'local_sitereport'),
        get_string('inprogresseuser', 'local_sitereport'),
        get_string('notstarteduser', 'local_sitereport')
        
    );
    
    $row = array();
    $sn = 1;
    if(!empty($coursedata)){
        foreach($coursedata as $data){
            
            $totalinstructor = get_instructors($data->id);
                                              //echo $data->id;
            $row[0] = $data->index;
            $row[1] = html_writer::link($CFG->wwwroot.'/course/view.php?id='.$data->id,$data->fullname);                                
            $row[2] = get_type($data->path);
            $row[3] = get_subject($data->path);

            $instructor = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'instructor','roleback'=>'manager_courseuser'));
            $completeusers = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'complete','roleback'=>'manager_courseuser'));
            $inprogressusers = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'inprogress','roleback'=>'manager_courseuser'));
            $notstartedusers = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'notstarted','roleback'=>'manager_courseuser'));

            $row[4] = html_writer::link($instructor,$totalinstructor);
            $row[5] = html_writer::link($completeusers,$data->completed);
            $row[6] = html_writer::link($inprogressusers,$data->inprogress);
            $row[7] = html_writer::link($notstartedusers,$data->notstarted);
            
            $table->data[] = $row;
            $sn++;        
        }
        
        
    }else{
        $table->data[] = ['Data not found'];   
    }   
    
    $html .= html_writer::table($table);
    
    $parameter = array();
   
    $parameter['type'] = $type;    
    $parameter['timetype'] = $timetype;
    $parameter['c_startdate'] = $c_startdate;
    $parameter['c_enddate'] = $c_enddate;
    $parameter['m_startdate'] = $m_startdate;
    $parameter['m_enddate'] = $m_enddate;
    if($type == 1 ){
        
        $parameter['iltsubject'] = $iltsubject;
    }elseif($type == 2){
       
        $parameter['elsubject'] = $elsubject;
    }

    $datacount = get_department_course(-1,$type,$iltsubject,$elsubject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate);
    $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/coursereport.php', $parameter);
    
   

    $html .= html_writer::start_tag('div',array('class'=>'row')); 
    $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
    $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
    $html .= html_writer::end_tag('div');
    $html .= html_writer::end_tag('div');

    return $html;
}

public function author_report($page,$ecn,$startdate,$enddate){

    global $CFG,$USER,$OUTPUT;
    $html  = '';
       // get_profile_field($USER->id);
    $authordata =  author_record($page,$ecn);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center','center','center');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('ecn', 'local_sitereport'),
        get_string('authorname','local_sitereport'), 
        get_string('email'),                                    
        get_string('designation', 'local_sitereport'),
        get_string('totalcoursecreate', 'local_sitereport')             
        
    );
    
    $row = array();
    $sn = 1;
    if(!empty($authordata)){
        foreach($authordata as $data){

         $designation =  get_profile_field($data->id);
         
         $row[0] = $data->index;
         $row[1] = $data->username;                                
         $row[2] = $data->firstname . ' ' . $data->lastname;
         $row[3] = $data->email;
         $row[4] = $designation['designation']->data;
         $authorCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/author_course.php',array('userid'=>$data->id,'startdate'=>$startdate,'enddate'=>$enddate));
         $row[5] = html_writer::link($authorCourseLink,author_course_count($data->id,$startdate,$enddate));

         $table->data[] = $row;
         $sn++;        
     }
     
 }else{
    $table->data[] = ['Data not found'];   
}   


$html .= html_writer::table($table);

$datacount = count_author_record($ecn);
$pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/author_report.php', array( 'page' => $page));  

$html .= html_writer::start_tag('div',array('class'=>'row')); 
$html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
$html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
$html .= html_writer::end_tag('div');
$html .= html_writer::end_tag('div');

return $html;
}


public function teacher_report($page,$ecn,$startdate,$enddate){

    global $CFG,$OUTPUT;
    $html  = '';
    $instructordata =  instructor_record($page,$ecn);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center','center','center');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('ecn', 'local_sitereport'),
        get_string('teachername','local_sitereport'), 
        get_string('email'),                                    
        get_string('designation', 'local_sitereport'),
        get_string('totalcourseenroll', 'local_sitereport')             
        
    );
    
    $row = array();
    $sn = 1;
    foreach($instructordata as $data){
        
     $designation =  get_profile_field($data->id);
     $coursecount = count(get_enrolled_course($data->id,3,$startdate,$enddate));
     if($coursecount){
     $row[0] = $data->index;
     $row[1] = $data->username;                                
     $row[2] = $data->firstname . ' ' . $data->lastname;
     $row[3] = $data->email;
     $row[4] = $designation['designation']->data;
     $teacherCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/courselist.php',array('userid'=>$data->id,'roleid'=>3,'startdate'=>$startdate,'enddate'=>$enddate));
     $row[5] = html_writer::link($teacherCourseLink,$coursecount);
     
     $table->data[] = $row;
     $sn++;   
     }     
 }
 
 
 
 $html .= html_writer::table($table);

 $datacount = count_instructor_record($ecn);
 $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/teacher_report.php', array( 'page' => $page));  
 
 $html .= html_writer::start_tag('div',array('class'=>'row')); 
 $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
 $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
 $html .= html_writer::end_tag('div');
 $html .= html_writer::end_tag('div');

 return $html;
}


public function ceo_teacher_report($page,$dpt,$ecn,$startdate,$enddate){

    global $CFG,$OUTPUT;
    $html  = '';
    $instructordata =  ceo_instructor_record($page,$dpt,$ecn);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center','center','center');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('ecn', 'local_sitereport'),
        get_string('teachername','local_sitereport'), 
        get_string('email'),
        get_string('departments', 'local_sitereport'),                                    
        get_string('designation', 'local_sitereport'),
        get_string('totalcourseenroll', 'local_sitereport')             
        
    );
    
    $row = array();
    $sn = 1;
    foreach($instructordata as $data){
        
     $designation =  get_profile_field($data->id);
     $coursecount = count(get_enrolled_course($data->id,3,$startdate,$enddate));
        if($coursecount){
     $row[0] = $data->index;
     $row[1] = $data->username;                                
     $row[2] = $data->firstname . ' ' . $data->lastname;
     $row[3] = $data->email;
     $row[4] = $data->dpt_name;
     $row[5] = $designation['designation']->data;
     $teacherCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/courselist.php',array('userid'=>$data->id,'action'=>'ceo','roleid'=>3,'startdate'=>$startdate,'enddate'=>$enddate));
     $row[6] = html_writer::link($teacherCourseLink,$coursecount);
     
     $table->data[] = $row;
     $sn++;
        }        
 }
 
 
 
 $html .= html_writer::table($table);
 $datacount = count_ceo_instructor_record($dpt,$ecn);
 $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/ceo_teacher_report.php', array( 'page' => $page));  
 
 $html .= html_writer::start_tag('div',array('class'=>'row')); 
 $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
 $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
 $html .= html_writer::end_tag('div');
 $html .= html_writer::end_tag('div');

 return $html;
}


public function non_teacher_report($page,$ecn,$startdate,$enddate){

  global $CFG,$OUTPUT;
  $html  = '';
  $noninstructordata =  non_instructor_record($page,$ecn);
  $table = new html_table();
  /* Styling done using HTML table and CSS */
  $table->attributes['class'] = 'table generaltable';
  $table->align = array('left', 'left', 'left', 'center','center','center');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
  $table->data = array();
  
  $table->head = array(
    get_string('sn', 'local_sitereport'),
    get_string('ecn', 'local_sitereport'),
    get_string('nonteachername','local_sitereport'), 
    get_string('email'),                                    
    get_string('designation', 'local_sitereport'),
    get_string('totalcourseenroll', 'local_sitereport')             
    
);
  
  $row = array();
  $sn = 1;
  foreach($noninstructordata as $data){
    
     $designation =  get_profile_field($data->id);
     $coursecount = count(get_enrolled_course($data->id,4,$startdate,$enddate));
     if($coursecount){
         $row[0] = $data->index;
         $row[1] = $data->username;                                
         $row[2] = $data->firstname . ' ' . $data->lastname;
         $row[3] = $data->email;
         $row[4] = $designation['designation']->data;
         $teacherCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/courselist.php',array('userid'=>$data->id,'action'=>'non','roleid'=>4,'startdate'=>$startdate,'enddate'=>$enddate));
         $row[5] = html_writer::link($teacherCourseLink,$coursecount);
         
         $table->data[] = $row;
         $sn++;      
     }  
 }
 
 
 
 $html .= html_writer::table($table);
 $datacount = count_non_instructor_record($ecn);
 $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/teacher_report.php', array( 'page' => $page));  
 
 $html .= html_writer::start_tag('div',array('class'=>'row')); 
 $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
 $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
 $html .= html_writer::end_tag('div');
 $html .= html_writer::end_tag('div');

 return $html;
}
public function ceo_non_teacher_report($page,$dpt,$ecn,$startdate,$enddate){

    global $CFG,$OUTPUT;
    $html  = '';
    $noninstructordata =  ceo_non_instructor_record($page,$dpt,$ecn);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center','center','center');
          //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
      get_string('sn', 'local_sitereport'),
      get_string('ecn', 'local_sitereport'),
      get_string('nonteachername','local_sitereport'), 
      get_string('email'),                                    
      get_string('departments', 'local_sitereport'),
      get_string('designation', 'local_sitereport'),
      get_string('totalcourseenroll', 'local_sitereport')             
      
  );
    
    $row = array();
    $sn = 1;
    foreach($noninstructordata as $data){
      
       $designation =  get_profile_field($data->id);
       $coursecount = count(get_enrolled_course($data->id,4,$startdate,$enddate));
       if($coursecount){
       $row[0] = $data->index;
       $row[1] = $data->username;                                
       $row[2] = $data->firstname . ' ' . $data->lastname;
       $row[3] = $data->email;
       $row[4] = $data->dpt_name;
       $row[5] = $designation['designation']->data;
       $teacherCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/courselist.php',array('userid'=>$data->id,'action'=>'ceo_non','roleid'=>4,'startdate'=>$startdate,'enddate'=>$enddate));
       $row[6] = html_writer::link($teacherCourseLink,$coursecount);
       // echo $teacherCourseLink;die;
       $table->data[] = $row;
       $sn++;   
       }     
   }
   
   
   
   $html .= html_writer::table($table);

   $datacount = count_ceo_non_instructor_record($dpt,$ecn);
   $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/teacher_report.php', array( 'page' => $page));  
   
   $html .= html_writer::start_tag('div',array('class'=>'row')); 
   $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
   $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
   $html .= html_writer::end_tag('div');
   $html .= html_writer::end_tag('div');
   
   return $html;
}

public function author_course_list($userid,$startdate,$enddate){

    global $CFG;

    $coursedata = AuthorCourseList($userid,$startdate,$enddate);

    if(!empty($coursedata)){
        $html = '';
        $html .= html_writer::start_tag('ul',null);
        foreach($coursedata as $data){
            
            $course = get_course($data->id);
            $courselink = new moodle_url($CFG->wwwroot.'/course/view.php',array('id'=>$data->id));
            $html .= html_writer::tag('li', html_writer::link($courselink,$course->fullname),array());
                                        //echo CourseImageLink($data->id);        
        }
        $html .= html_writer::end_tag('ul');
        
        return $html;
        
    }else{
        
        return html_writer::tag('h4',get_string('notcoursefind','block_owncourse'),array());
    }
} 


public function teacher_course_list($userid,$roleid,$startdate=NULL,$enddate=NULL){

    global $CFG;

    $coursedata = get_enrolled_course($userid,$roleid,$startdate,$enddate);

    if(!empty($coursedata)){
        $html = '';
        $html .= html_writer::start_tag('ul',null);
        foreach($coursedata as $data){
            
            $course = get_course($data->id);
            $courselink = new moodle_url($CFG->wwwroot.'/course/view.php',array('id'=>$data->id));
            $html .= html_writer::tag('li', html_writer::link($courselink,$course->fullname),array());
                                        //echo CourseImageLink($data->id);        
        }
        $html .= html_writer::end_tag('ul');
        
        return $html;
        
    }else{
        
        return html_writer::tag('h4',get_string('coursenotfound','block_owncourse'),array());
    }
} 

public function trainee_course_list($userid,$action,$roleid){

    global $CFG,$DB;
    
    $coursedata =  get_enrolled_course($userid,$roleid,NULL,NULL);
    // $coursedata =  enrol_get_all_users_courses($userid, true);
   // print_object($coursedata);
    $html ='';
    $table = new html_table();
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center','center','center');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('coursename', 'local_sitereport'),
        get_string('category','local_sitereport'), 
        get_string('progress','local_sitereport'),                                    
        get_string('status', 'local_sitereport')      
    );
    
    $row = array();
    $sn = 1;
    foreach($coursedata as $data){  
        
        
        $courseProgress = user_course_progress($data->id,$userid);

        $progress =  html_writer::start_tag('div',array('class'=>"progress"));
        $progress .= html_writer::start_div($class='progress-bar',array('role'=>"progressbar",'style'=>"width: ".$courseProgress."%",'aria-valuenow'=>$courseProgress, 'aria-valuemin'=>"0",'aria-valuemax'=>"100" ));
        $progress .= html_writer::end_tag('div');

        $isnotstarted = $DB->get_record_sql("SELECT id FROM {user_lastaccess} as ul WHERE courseid=$data->id and userid = $userid");
        if($action == 'complete'){
         
            if(local_is_course_complete($data->id,$userid)){

                $row[0] = $sn;
                $row[1] = $data->fullname;                                
                $row[2] = $DB->get_field('course_categories','name',array('id'=>$data->category));
                $row[3] = $progress;
                $row[4] = sitereport_user_course_status($data->id,$userid);
                
                $table->data[] = $row;
                $sn++;   
            } 
        }elseif($action == 'inprogress'){
           
             if(!local_is_course_complete($data->id,$userid) and $isnotstarted){
               
                $row[0] = $sn;
                $row[1] = $data->fullname;                                
                $row[2] = $data->name;
                $row[3] = $progress;
                $row[4] = sitereport_user_course_status($data->id,$userid);
                $table->data[] = $row;
                $sn++;   
            } 
        }elseif($action == 'notstarted'){
           
             if(!local_is_course_complete($data->id,$userid) and !$isnotstarted){
               
                $row[0] = $sn;
                $row[1] = $data->fullname;                                
                $row[2] = $data->name;
                $row[3] = $progress;
                $row[4] = sitereport_user_course_status($data->id,$userid);
                $table->data[] = $row;
                $sn++;   
            } 
        } else{

            $row[0] = $sn;
            $row[1] = $data->fullname;                                
            $row[2] = $data->name;
            $row[3] = $progress;
            $row[4] = sitereport_user_course_status($data->id,$userid);
            $table->data[] = $row;
            $sn++;   
            
        }   
        
    }
    
    $html .= html_writer::table($table);

    return $html;
} 

public function ceo_user_report($page,$dpt,$ecn,$base,$zone,$rank,$l1,$l2){

    global $CFG,$OUTPUT;
    
    $html  = '';    
    
    $userdata = ceo_department_user_detail($page,$dpt,$ecn,$base,$zone,$rank,$l1,$l2);
                //print_object($userdata);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center');
                        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('ecn', 'local_sitereport'),
        get_string('name', 'local_sitereport'),
        get_string('email'),
        get_string('departments', 'local_sitereport'),
        get_string('base', 'local_sitereport'),
        get_string('rank', 'local_sitereport'),
                                             //   get_string('l1', 'local_sitereport'),
                                               // get_string('l2', 'local_sitereport'),
        get_string('totalcourseenroll', 'local_sitereport'),
        get_string('complete', 'local_sitereport'),
        get_string('inprogress', 'local_sitereport'),
        get_string('notstarted', 'local_sitereport')
        
    );
    
    $row = array();
    $sn = 1;                                        
    if(!empty($userdata)){                       
        foreach($userdata as $data){
            
            $designation =  get_profile_field($data->id);
            // $statusCount = course_progress_count($data->id,5);
            $row[0] = $data->index;
            $row[1] = $data->username;                                
            $row[2] = $data->firstname . ' ' . $data->lastname;
            $row[3] = $data->email;
            $row[4] = $data->dpt_name;
            $row[5] = $data->city;
            $row[6] = $designation['designation']->data;
            $studentCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'roleid'=>5,'roleback'=>'ceo'));
            $row[9] = html_writer::link($studentCourseLink,$data->totalcourse);
            
            $complteCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'action'=>'complete','roleid'=>5,'roleback'=>'ceo'));
            $inprogressCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'action'=>'inprogress','roleid'=>5,'roleback'=>'ceo'));
            $notstartedCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/trainee.php',array('userid'=>$data->id,'action'=>'notstarted','roleid'=>5,));
            $row[10] = html_writer::link($complteCourseLink,$data->completed);
            $row[11] = html_writer::link($inprogressCourseLink,$data->inprogress);
            $row[12] = html_writer::link($notstartedCourseLink,$data->notstarted);
            
            $table->data[] = $row;
            $sn++;        
        }
    }else{
        $table->data[] = ['Data not found'];   
    }   
    
    $html .= html_writer::table($table);
    
    $datacount = ceo_department_user_detail(-1,$dpt,$ecn,$base,$zone,$rank,$l1,$l2); 
    
    $parameter = array();
    $parameter['page'] = $page;
    $parameter['dpt'] = $dpt;
    $parameter['ecn'] = $ecn;
    $parameter['base'] = $base;
    $parameter['zone'] = $zone;
    $parameter['rank'] = $rank;
    $parameter['l1'] = $l1;
    $parameter['l2'] = $l2;

    $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/ceo_userreport.php', $parameter);
   
    
    $html .= html_writer::start_tag('div',array('class'=>'row')); 
    $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
    $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
    $html .= html_writer::end_tag('div');
    $html .= html_writer::end_tag('div');
    
    
    
    return $html;
}

public function ceo_course_report($page,$dpt,$type,$subject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate){


    global $CFG,$OUTPUT;
    
    $html  = '';
    $coursedata = get_ceo_course($page,$dpt,$type,$subject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate);

    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
                        // $table->align = array('left', 'left', 'left', 'left','left');
                //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('course'),                               
        get_string('departments', 'local_sitereport'),
        get_string('type', 'local_sitereport'),
        get_string('subject', 'local_sitereport'),
        get_string('instructor', 'local_sitereport'),
        get_string('completeuser', 'local_sitereport'),
        get_string('inprogresseuser', 'local_sitereport'),
        get_string('notstarteduser', 'local_sitereport')
        
    );
    
    $row = array();
    $sn = 1;
    foreach($coursedata as $data){
        
        $totalinstructor = get_instructors($data->id);
                                                      //echo $data->id;
        $row[0] = $data->index;
        $row[1] = html_writer::link($CFG->wwwroot.'/course/view.php?id='.$data->id,$data->fullname);                                
        $row[2] = get_course_dpt_name($data->path);
        $row[3] = get_type($data->path);
        $row[4] = get_subject($data->path);

        $instructor = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'instructor','roleback'=>'ceo_courseuser'));
        $completeusers = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'complete','roleback'=>'ceo_courseuser'));
        $inprogressusers = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'inprogress','roleback'=>'ceo_courseuser'));
        $notstartedusers = new moodle_url($CFG->wwwroot.'/local/sitereport/course_users.php',array('courseid'=>$data->id,'type'=>'notstarted','roleback'=>'ceo_courseuser'));

        $row[5] = html_writer::link($instructor,$totalinstructor);
        $row[6] = html_writer::link($completeusers,$data->completed);
        $row[7] = html_writer::link($inprogressusers,$data->inprogress);
        $row[8] = html_writer::link($notstartedusers,$data->notstarted);
        
        $table->data[] = $row;
        $sn++;        
    }
    
    
    $html .= html_writer::table($table);
    
    $datacount = get_ceo_course(-1,$dpt,$type,$subject,$timetype,$c_startdate,$c_enddate,$m_startdate,$m_enddate);

   
    $parameter = array();
    $parameter['dpt'] = $dpt;  
    $parameter['type'] = $type;
    $parameter['subject'] = $subject;
    $parameter['timetype'] = $timetype;
    $parameter['c_startdate'] = $c_startdate;
    $parameter['c_enddate'] = $c_enddate;
    $parameter['m_startdate'] = $m_startdate;
    $parameter['m_enddate'] = $m_enddate;
   
    $pagelink = new moodle_url($CFG->wwwroot . '/local/sitereport/ceo_coursereport.php',$parameter);
    
    
    
    $html .= html_writer::start_tag('div',array('class'=>'row')); 
    $html .= html_writer::start_tag('div',array('class'=>'col-md-12', 'align'=>'center')); 
    $html .= $OUTPUT->paging_bar($datacount, $page, PERPAGE_LIMIT, $pagelink);
    $html .= html_writer::end_tag('div');
    $html .= html_writer::end_tag('div');
    
    return $html;
}

public function ceo_course_users_list($courseid,$type){

    global $CFG;

    $users = get_ceo_users($courseid,$type);
     
    if(!empty($users[$type])){
        $html = '';
        

        $table = new html_table();
        /* Styling done using HTML table and CSS */
        $table->attributes['class'] = 'table generaltable';
                            // $table->align = array('left', 'left', 'left', 'left','left');
                    //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
        $table->data = array();
        
        $table->head = array(
            get_string('sn', 'local_sitereport'),
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
           
            
        );
        
        $row = array();
        $sn = 1;
        foreach($users[$type] as $user){
          
            // echo "<pre>";print_r($user);
            $row[0] = $sn;
            $row[1] =  $user['ecn'];
            $courselink = new moodle_url($CFG->wwwroot.'/user/profile.php',array('id'=>$user['id']));
            $row[2] =  html_writer::link('li', html_writer::link($courselink,$user['fullname']),array());
           
            
            $table->data[] = $row;
            $sn++;        
        }
        
        
        $html .= html_writer::table($table);
        return $html;
        
    }else{
        
        return html_writer::tag('h4',get_string('notuserfind','local_sitereport'),array());
    }
} 


public function ceo_author_report($page,$dpt,$ceo,$startdate,$enddate){

    global $CFG,$USER;
    $html  = '';
               // get_profile_field($USER->id);
    $authordata =  ceo_author_record($page,$dpt,$ceo);
    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable';
    $table->align = array('left', 'left', 'left', 'center','center','center');
                //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->data = array();
    
    $table->head = array(
        get_string('sn', 'local_sitereport'),
        get_string('ecn', 'local_sitereport'),
        get_string('authorname','local_sitereport'), 
        get_string('email'),
        get_string('departments', 'local_sitereport'),
        get_string('designation', 'local_sitereport'),
        get_string('totalcoursecreate', 'local_sitereport')             
        
    );
    
    $row = array();
    $sn = 1;
    foreach($authordata as $data){
        
     $designation =  get_profile_field($data->id);
     
     $row[0] = $data->index;
     $row[1] = $data->username;                                
     $row[2] = $data->firstname . ' ' . $data->lastname;
     $row[3] = $data->email;
     $row[4] = $data->dpt_name;
     $row[5] = $designation['designation']->data;
     $authorCourseLink = new moodle_url($CFG->wwwroot.'/local/sitereport/author_course.php',array('userid'=>$data->id,'startdate'=>$startdate,'enddate'=>$enddate,'roleback'=>'ceo'));
     $row[6] = html_writer::link($authorCourseLink,author_course_count($data->id,$startdate,$enddate));
     
     $table->data[] = $row;
     $sn++;        
 }
 
 
 
 $html .= html_writer::table($table);
 
 return $html;
}


}
