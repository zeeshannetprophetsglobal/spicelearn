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
 * @package    local_coursereports
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_coursereports_renderer extends plugin_renderer_base
{

    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

    public function corusereports_data($page)
    {

        global $DB, $CFG, $OUTPUT, $USER;
        
		//$a = local_coursereports_completed_count();
		//print_object($a);die;
        
        $html = '';
        
        
         // table data

        $table = new html_table();
        /* Styling done using HTML table and CSS */
        $table->attributes['class'] = 'table generaltable ';
               // $table->align = array('center', 'center', 'center', 'center','center', 'center', 'center', 'center','center');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
        $table->size = array('5%', '35%', '15%','15%','15%','15%');

        $table->data = array();
        
        
        $table->head = array(
            get_string('sn', 'local_coursereports'),
            get_string('coursename', 'local_coursereports'), 
                                       // get_string('department', 'local_coursereports'), 
            get_string('completed','local_coursereports'),                                      
            get_string('completedpercentage', 'local_coursereports'),                                        
            get_string('userinprogress','local_coursereports'), 
            get_string('totalenrollment', 'local_coursereports')                                        
            
        );
        
        $row = array();
        
        $CourseData = local_coursereports_get_coursedata($page);
        
        if($page){
           $sn = $page*COURSEREPORTS_PAGE_LIMIT;
       }else{
        $sn = 1;
    }
    foreach($CourseData as $course){
     
     $context = context_course::instance($course->id);
     
     if($course->id != 1){
        
         $is_courseComplete = local_coursereports_get_course_complete_count($course->id);
         
         $row[0] = $sn;                       
         $row[1] = $course->fullname;
                       // $row[2] = '';
         $row[3] = $is_courseComplete->complete;
         
         if($is_courseComplete->complete){
          $completedpercentage = $is_courseComplete->complete/count_enrolled_users($context, $withcapability = '', $groupid = 0)*100;
      }else{
        $completedpercentage = 0.00;
    }
    
    $row[4] =   number_format((float)$completedpercentage, 2, '.', '');
    $row[5] = $is_courseComplete->notcomplete;
    $row[6] = count_enrolled_users($context, $withcapability = '', $groupid = 0);
    
    $table->data[] = $row;
    $sn++;
}
}
$html .= html_writer::table($table);

echo html_writer::tag('div',$html,array('class'=>'corsereports-table'));;


$datacount = $DB->count_records('course', null);
$pagelink = new moodle_url($CFG->wwwroot . '/local/coursereports/index.php', array('page' => $page));


echo $OUTPUT->paging_bar($datacount, $page, COURSEREPORTS_PAGE_LIMIT, $pagelink);


}

public function leaderboard_data($page,$cohort,$search)
{

    global $DB, $CFG, $OUTPUT, $USER;
    
    
    
    $html = '';
    
    
         // table data

    $table = new html_table();
    /* Styling done using HTML table and CSS */
    $table->attributes['class'] = 'table generaltable ';
    $table->align = array('center', 'center', 'center', 'center','center', 'center', 'center', 'center','center');
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
    $table->size = array('2%', '15%', '15%','10%','10%','14%','8%','9%','9%','8%');

    $table->data = array();
    
    
    $table->head = array(
        get_string('rank', 'local_coursereports'),
        get_string('name', 'local_coursereports'), 
        get_string('department', 'local_coursereports'),
        get_string('totalcourse', 'local_coursereports'), 
        get_string('totalscore','local_coursereports'),                                      
        get_string('completecourse', 'local_coursereports'),                                        
        get_string('inprogress','local_coursereports'), 
        get_string('notstart', 'local_coursereports'),
        get_string('gamescore', 'local_coursereports'),
        get_string('lmsscore', 'local_coursereports'),
        

        
    );
    
    $row = array();
    
    $leaderboardData = get_learderboard_data($page,$cohort,$search);
    
    if($page){
       $sn = ($page*COURSEREPORTS_PAGE_LIMIT)+1;
   }else{
    $sn = 1;
}
foreach($leaderboardData as $data){
   
    $row[0] = $sn;
    $userdata = core_user::get_user($data->userid);                       
    $row[1] = $userdata->firstname. ' '. $userdata->lastname;
    $departmentdata = $DB->get_record('cohort', array('id'=>$data->departmentid));
    $row[2] = $departmentdata->name;
    $statusCount = course_progress_count2($userdata->id,5);
    // $CourseStatus = course_status_by_userid($data->userid);
    // $row[3] = get_enroll_course_count_by_userid($userdata->id);
    $row[3] = $statusCount['totalcourse'];
    
    
    $row[4] =  $data->finalscore;
    $row[5] = $statusCount['complete'];
    $row[6] =  $statusCount['inprogress'];
    $row[7] = $statusCount['notstarted'];
    $row[8] = $data->gamescore;

    $row[9] = $data->lmsscore;
    
    
    $table->data[] = $row;
    $sn++;
    
}
$html .= html_writer::table($table);
$loader =  html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/pix/loader.gif','style'=>'display:none;','class'=>'loader'));

echo html_writer::tag('div',$loader,array('class'=>'loader-div','align'=>'center'));                  

if(!empty($search['name']) && !empty($search['email']) && !empty($cohort)){

    $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
        $datacount = $DB->count_records_sql($sql,  array('departmentid'=>$cohort));
        
        if(empty($learderboardData)){
            
            $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
                $datacount = $DB->count_records_sql($sql,  array('departmentid'=>$cohort));
                
            }
            
            
        }elseif(!empty($search['name']) && !empty($search['email'])){
            
            $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
                $datacount = count($DB->get_records_sql($sql, null));
                
                if(empty($learderboardData)){
                    
                    $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
                        $datacount = count($DB->get_records_sql($sql, null));
                        
                    }
                    
                    
                }elseif(!empty($cohort) && !empty($search['email'])){

                    $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
                        $datacount = count($DB->get_records_sql($sql,  array('departmentid'=>$cohort)));
                        
                    }elseif(!empty($cohort) && !empty($search['name'])){
                        
                        $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
                            $datacount = $DB->count_records_sql($sql, array('departmentid'=>$cohort));
                            
                            
                        }elseif( !empty($search['email']) && empty($cohort) && empty($search['name']) ){
                            
                            
                            $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
                                $datacount = count($DB->get_records_sql($sql, null)); 
                                
                                
                            }elseif(!empty($search['name']) && empty($cohort) && empty($search['email'])){
                                
                                $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
                                 
                                    $datacount = count($DB->get_records_sql($sql, array()));
                                    
                                }elseif($cohort && empty($search['email']) && empty($search['name'])){

                                  $datacount = $DB->count_records('local_user_leaderboard', array('departmentid'=>$cohort));
                                  
                              }else{

                               $datacount = $DB->count_records('local_user_leaderboard', null);
                           }

                           $pagelink = new moodle_url($CFG->wwwroot . '/local/coursereports/leaderboard.php', array('page' => $page,'dpt'=>$cohort));
                           

                           $html .= $OUTPUT->paging_bar($datacount, $page, COURSEREPORTS_PAGE_LIMIT, $pagelink);

                           $allhtml =  html_writer::tag('div',$html,array('class'=>'leaderboard-table'));
                           
                           echo $allhtml;
                       }



                       public function userwise_data($page,$cohort,$search)
                       {

                        global $DB, $CFG, $OUTPUT, $USER;
                        
                        
                        
                        $html = '';
                        
                        
         // table data

                        $table = new html_table();
                        /* Styling done using HTML table and CSS */
                        $table->attributes['class'] = 'table generaltable ';
                        $table->align = array('center', 'center', 'center', 'center','center','center','center' );
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
                        $table->size = array('5%', '30%', '20%','10%','15%','10%','10%');

                        $table->data = array();
                        
                        
                        $table->head = array(
                            get_string('sn', 'local_coursereports'),
                            get_string('name', 'local_coursereports'), 
                            get_string('department', 'local_coursereports'), 
                            get_string('totalcourse', 'local_coursereports'),
                            get_string('completecourse', 'local_coursereports'),                                      
                            get_string('inprogress', 'local_coursereports'),
                            get_string('notstart', 'local_coursereports'),     

                            
                        );
                        
                        $row = array();
                        
                        $userwisedata = get_userwise_data($page,$cohort,$search);
                        
                        if($page){
                           $sn = ($page*USERWISE_PAGE_LIMIT)+1;
                       }else{
                        $sn = 1;
                    }
                    foreach($userwisedata as $data){
                       
                        
                        $row[0] = $sn;
                        
                        $row[1] = $data->firstname.' '.$data->lastname;

                        $row[2] = local_cousrewisereprt_get_user_department_name($data->id);
                        
                        $row[3] = get_enroll_course_count_by_userid($data->id);

                        $CourseStatus = course_status_by_userid($data->id);
                        $completedLink = new moodle_url($CFG->wwwroot.'/local/coursereports/userreport.php',array('id'=>$data->id,'status'=>'completed'));
                        $row[4] = html_writer::link($completedLink, $CourseStatus->completed,array());

                        $inprogressLink = new moodle_url($CFG->wwwroot.'/local/coursereports/userreport.php',array('id'=>$data->id,'status'=>'inprogress'));
                        $row[5] =  html_writer::link($inprogressLink, $CourseStatus->inprogress,array());

                        $notstartLink = new moodle_url($CFG->wwwroot.'/local/coursereports/userreport.php',array('id'=>$data->id,'status'=>'notstart'));
                        $row[6] = html_writer::link($notstartLink, $CourseStatus->notstart,array());
                        
                        
                        $table->data[] = $row;
                        $sn++;
                        
                    }
                    $html .= html_writer::table($table);
                    $loader =  html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/pix/loader.gif','style'=>'display:none;','class'=>'loader'));

                    echo html_writer::tag('div',$loader,array('class'=>'loader-div','align'=>'center'));
                    
                    if(!empty($cohort) && !empty($search['name'])){

                        $sql = "SELECT count(u.id) FROM {user} as u JOIN {cohort_members} AS cm on u.id= cm.userid WHERE cm.cohortid = ".$cohort." AND u.id NOT IN (1,2) AND u.firstname LIKE '%".$search['name']."%'";
                        $datacount = $DB->count_records_sql($sql,null);
                        
                    }elseif(!empty($search['name']) && empty($cohort)){

                        $sql = "SELECT count(id) FROM {user}  WHERE  id NOT IN (1,2) AND firstname LIKE '%".$search['name']."%'";
                        $datacount = $DB->count_records_sql($sql,null);
                        
                    }elseif($cohort){
                     
                        $sql = "SELECT count(u.id) FROM {user} as u JOIN {cohort_members} AS cm WHERE cm.cohortid = ".$cohort." AND u.id NOT IN (1,2)" ;
                        $datacount = $DB->count_records_sql($sql,null);
                        
                    }else{
                        $sql = "SELECT * FROM {user} where id NOT IN (1,2)" ;
                        $datacount = COUNT($DB->get_records_sql($sql,null));
                    }

                    $pagelink = new moodle_url($CFG->wwwroot . '/local/coursereports/userwise.php', array('page' => $page,'dpt'=>$cohort));
                    

                    $html .= $OUTPUT->paging_bar($datacount, $page, USERWISE_PAGE_LIMIT, $pagelink);

                    $allhtml =  html_writer::tag('div',$html,array('class'=>'userwisereport-table'));
                    
                    echo $allhtml;
                }


                public function user_report($page,$status,$userid)
                {

                    global $DB, $CFG, $OUTPUT, $USER;
                    
                    
                    
                    $html = '';
                    
                    
         // table data

                    $table = new html_table();
                    /* Styling done using HTML table and CSS */
                    $table->attributes['class'] = 'table generaltable ';
                    $table->align = array('center', 'center', 'center','center' );
        //$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
                    $table->size = array('10%', '50%', '20%','20%');

                    $table->data = array();
                    
                    
                    $table->head = array(
                        get_string('sn', 'local_coursereports'),
                        get_string('coursename', 'local_coursereports'), 
                        get_string('status', 'local_coursereports'),                                     
                        get_string('quiz_score', 'local_coursereports'),     

                        
                    );
                    
                    $row = array();
                    
                    $userwisedata = get_userreport_data($page,$userid,$status);
                    
                    
                // if($page){
				// 	$sn = ($page*USERWISE_PAGE_LIMIT)+1;
				// }else{
                //     $sn = 1;
				// }
                    $sn = 1;
                    

                    if($status == 'completed'){				
                       
                        foreach($userwisedata as $data){
                          

                            if(!empty($data->completed->courseid)){ 

                                $row[0] = $sn;
                                $row[1] = $data->completed->fullname;
                                $row[2] = user_course_status($data->completed->courseid,$userid,$data->completed->timestart);
                                $row[3] =  user_grade_data($data->completed->courseid,$userid);
                                $table->data[] = $row;
                                $sn++;

                            }
                            
                        }
                    }elseif($status == 'notstart'){  

                        foreach($userwisedata as $data){

                            if(!empty($data->notstart)){ 

                                $row[0] = $sn;
                                $row[1] = $data->notstart->fullname;                          
                                $row[2] = user_course_status($data->notstart->courseid,$userid,$data->notstart->timestart);
                                $row[3] =  user_grade_data($data->notstart->courseid,$userid);

                                $table->data[] = $row;
                                $sn++;

                            }

                        }
                    }elseif($status == 'inprogress'){

                        foreach($userwisedata as $data){

                            if(!empty($data->inprogress)){ 

                                $row[0] = $sn;
                                $row[1] = $data->inprogress->fullname;                          
                                $row[2] = user_course_status($data->inprogress->courseid,$userid,$data->inprogress->timestart);
                                $row[3] =  user_grade_data($data->inprogress->courseid,$userid);

                                $table->data[] = $row;
                                $sn++;
                            }
                            
                        }
                        
                    }else{

                        foreach($userwisedata as $data){
                           
                           

                            $row[0] = $sn;
                            
                            $row[1] = $data->fullname;
                            
                            $row[2] = user_course_status($data->courseid,$userid,$data->timestart);
                            $row[3] =  user_grade_data($data->courseid,$userid);
                            
                            $table->data[] = $row;
                            $sn++;
                            
                        }
                      	//print_object(array_unique($row));
                        
                        
                    }

                    
                    
                    
                    $html .= html_writer::table($table);
                  //  $loader =  html_writer::tag('img','',array('src'=>$CFG->wwwroot.'/pix/loader.gif','style'=>'display:none;','class'=>'loader'));

                 //   echo html_writer::tag('div',$loader,array('class'=>'loader-div','align'=>'center'));
                    
                    
                    
               //  $sql = "SELECT  DISTINCT c.id as 'courseid' ,c.fullname,  ue.timestart as timestart  FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as  ue ON ue.enrolid = en.id WHERE ue.userid = ". $userid ;
                    
         // $datacount = count($DB->get_records_sql($sql, null));
                    
         // $pagelink = new moodle_url($CFG->wwwroot . '/local/coursereports/userreport.php', array('page' => $page,'id'=>$userid));
                    

         //$html .= $OUTPUT->paging_bar($datacount, $page, USERWISE_PAGE_LIMIT, $pagelink);

                    $allhtml =  html_writer::tag('div',$html,array('class'=>'leaderboard-table'));
                    
                    echo $allhtml;
                }

            }




