<?php

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
 * External Custom Api
 *
 * @package    localapi
 */
use core_completion\progress;
define('SERVER_KEY','AAAAzN4I1kg:APA91bH9436jQGZmgNdEarQD3pvj1wBtoREl1WAWKYMuElHyCNqTusywO0nt6DC0UNU5EOfj64b72Q4fAwkd9_q-_pk9byYLVCCyUZhPS39gcpUU0MvI2b2EC3PYBcoHru2_CHT9L2pY');

function get_user_category_detail($userid)
{

    global $DB, $CFG, $USER;

    $categorydata = array();
    $sql = "SELECT c.* FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE  ue.userid = " . $userid ." AND c.visible = 1 ORDER BY c.timemodified DESC";

    // $coursedata = $DB->get_records_sql($sql, null);
    $coursedata = enrol_get_my_courses();
	// echo count($mycourses);die;
    $myacourses = array();
    if (!empty($coursedata)) {
        foreach ($coursedata as $cdata) {
            $status = '';
            // $is_overdue = check_overdue($cdata->id);
            $is_notstarted = not_started_course($cdata->id,$userid);
            $is_course_complete = is_course_complete($cdata->id,$userid);

            if($is_course_complete){
                $status = 'Completed';
            }elseif($is_notstarted){
                $status = 'notstarted';
            }else{
                $status = 'Pending';
            }
            
			$categoey_sortname = '';
			$categoey_data = get_category_detail($cdata->category);
			$categoey_sortname = $categoey_data->idnumber;
            $enrollcourses = array();
            $courseobject = get_course($cdata->id);
            $categorydata[] = $cdata->category;
            $enrollcourses['id'] = $cdata->id;
            $enrollcourses['fullname']  = $cdata->fullname;
            $enrollcourses['shortname'] = $cdata->shortname;
			 $enrollcourses['summary'] = $cdata->summary;
            $enrollcourses['sortorder'] = $cdata->sortorder;
            $enrollcourses['visible']   = $cdata->visible;
            $enrollcourses['imageurl']  = get_course_image($cdata->id);
            $enrollcourses['categoryname'] = $categoey_data->name;
			$enrollcourses['categoey_sortname'] = $categoey_sortname;
            $enrollcourses['courseprogress'] = progress::get_course_progress_percentage($courseobject, $userid);
            $enrollcourses['is_favourite'] = is_favourite($cdata->id,$userid);
            $enrollcourses['coursestatus'] = $status;
            $enrollcourses['lastaccess']      = get_last_access_course($cdata->id, $userid);
            $enrollcourses['parent_department'] = get_course_parent_department($categoey_data->path);
            if ($enrollcourses['courseprogress'] === NULL) {
                $enrollcourses['courseprogress'] = 0;
            }
            $myacourses[] = $enrollcourses; 
           
        }
        $unice_category = array_unique($categorydata);
        $categoey_detail = array();
        foreach ($unice_category as $catid) {
            $categoey_detail[] = get_category_detail_by_id($catid, $userid);

        }
      
        return array('category'=>$categoey_detail,'courses'=>$myacourses);
    } else  {
       // return false;
       $categoeydata = new stdClass();
       $enrollcourses = array();
       
       $categoeydata->id = NULL;
       $categoeydata->name = NULL;
       $categoeydata->imageurl = NULL;
       $categoeydata->coursecount = NULL;
       $categoey_detail[] = $categoeydata;

       $enrollcourses['id'] = NULL;
       $enrollcourses['fullname']  = NULL;
       $enrollcourses['shortname'] = NULL;
	   $enrollcourses['summary'] = NULL;
       $enrollcourses['sortorder'] = NULL;
       $enrollcourses['visible']   = NULL;
       $enrollcourses['imageurl']  = NULL;
       $enrollcourses['categoryname'] = NULL;
	   $enrollcourses['categoey_sortname'] = NULL;
       $enrollcourses['courseprogress'] = NULL;
	   $enrollcourses['is_favourite'] = NULL;
       $enrollcourses['coursestatus'] = NULL;
       $enrollcourses['lastaccess']      =NULL;
       $enrollcourses['parent_department'] = NULL;
       $myacourses[] = $enrollcourses; 
		
        return array('category'=>$categoey_detail,'courses'=>$myacourses);
    }  
}

function get_course_parent_department($path){

    global $DB;

    $explodpath = explode("/",$path);

    $typedata =  $DB->get_record('course_categories',array('id'=>$explodpath[2]));
     
    return $typedata->name;
}
function get_category_detail_by_id($categoeyid, $userid)
{
    global $DB;

    //$sql = "SELECT * from {course_categories} WHERE a.id=".$categoeyid;

    $catdata = $DB->get_record('course_categories', array('id' => $categoeyid));

    $categoeydata = new stdClass();

    $categoeydata->id = $catdata->id;
    $categoeydata->name = $catdata->name;
	$categoeydata->categoey_sortname = $catdata->idnumber;
    $categoeydata->imageurl = api_fileview($catdata->id);
    $categoeydata->coursecount = user_course_count($catdata->id, $userid);

    return $categoeydata;

}

function api_fileview($id)
{
    global $DB, $PAGE, $CFG;

    $catdata = $DB->get_record('local_category_image', array('category_id' => $id));

    if (!empty($catdata)) {
        $out = 'no image';
        $context = context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'local_catimage', 'catimage', $catdata->id);
        $file = reset($files);

        $url = '';
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename !== '.') {
                $filename = str_replace(' ', '%20', $filename);
                $path = '/' . $file->get_contextid() . '/local_catimage/catimage/' .
                $catdata->id . '/' . $filename;
                $url = $CFG->wwwroot . '/pluginfile.php' . $path;
            }

        }
        $out = $url;

        // $additionalhtmlhead = var_dump($file) . '' . $url;

        // $out = $additionalhtmlhead;

        return $out;
    } else {
        return $CFG->wwwroot.'/pix/category-sample.png';
    }
}

function local_api_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    global $CFG, $DB;
    require_once "$CFG->libdir/resourcelib.php";

    $filename = array_pop($args);
//    $itemid = array_pop($args);
    $itemid = array_shift($args);

    $fs = get_file_storage();

    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';
    if (!$file = $fs->get_file($context->id, 'local_api', $filearea, $itemid, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
    }
    // finally send the file
    send_stored_file($file, null, 0, $forcedownload, $options);
}

function user_course_count($categoryid, $userid)
{

    global $DB;

    $sql = "SELECT c.id FROM {course} as c JOIN {course_categories} cc ON cc.id = c.category JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND ue.userid = " . $userid . " AND cc.path LIKE '%".$categoryid."%'";
    // echo $sql;
    $coursedata = $DB->get_records_sql($sql, null);

    return count($coursedata);

}


function user_is_category($categoryid,$userid)
{

    global $DB;   

    $sql = "SELECT c.id FROM {course} as c JOIN {course_categories} cc ON cc.id = c.category JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND ue.userid = " . $userid . " AND cc.parent = $categoryid";
    $coursedata = $DB->get_records_sql($sql, null);
   
    return count($coursedata);

}

function get_course_image($courseid)
{       
    global $CFG;
   
    $url = 'no image'; 

    $context = context_course::instance($courseid);
    $fs = get_file_storage();
    $files = $fs->get_area_files( $context->id, 'course', 'overviewfiles', 0 );
   
    foreach ( $files as $f )
    {
      if ( $f->is_valid_image() )
      {
        $filename = str_replace(' ', '%20', $f->get_filename());
        $path = '/' . $f->get_contextid() . '/'.$f->get_component().'/'. $f->get_filearea()  . '/' . $filename;
        $url = $CFG->wwwroot . '/pluginfile.php' . $path;
         //$url = moodle_url::make_pluginfile_url( $f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
      }
    }
	if(empty($url))
	{
		 return $CFG->wwwroot.'/pix/category-sample.png';
	}else{
		return $url;
	}

}

function get_category_detail($categogryid)
{
    global $DB;

    $data = $DB->get_record('course_categories', array('id'=>$categogryid));

    return $data;

}

function get_last_access_course($courseid, $userid)

    {

        global $DB;

        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', array('courseid' => $courseid, 'userid' => $userid));

        $course = get_course($courseid);
        
        if(empty($lastaccess))
        {
			return $course->timecreated;
		}else{
        return $lastaccess;
		}
    }
	
	 function search_my_course($userid,$search,$categoryid)
    {
        global $DB;
		
        if(empty($categoryid)){
        if(empty($search))
        {
             $sql = "SELECT c.*,cc.path FROM {course} as c JOIN {course_categories} AS cc ON cc.id = c.category JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND ue.userid = " . $userid;
        }else{
             $sql = "SELECT c.*,cc.path FROM {course} as c JOIN {course_categories} AS cc ON cc.id = c.category JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND ue.userid = " . $userid ." AND c.fullname LIKE '%".$search."%'";
        }
    }else{
       
        if(empty($search))
        {
             $sql = "SELECT c.*,cc.path FROM {course} as c  JOIN {course_categories} AS cc ON cc.id = c.category JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND ue.userid = " . $userid." AND c.category = $categoryid";
        }else{
           
             $sql = "SELECT c.*,cc.path FROM {course} as c JOIN {course_categories} AS cc ON cc.id = c.category JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND  ue.userid = " . $userid ." AND c.category = $categoryid AND c.fullname LIKE '%".$search."%'";
        }
    }
        
        $coursedata = $DB->get_records_sql($sql, null);
        $myacourses = array();
        if (!empty($coursedata)) {
            foreach ($coursedata as $cdata) {
				
				$categoey_detail = get_category_detail($cdata->category);
                $status = '';
            $is_overdue = check_overdue($cdata->id);
            $is_course_complete = is_course_complete($cdata->id,$userid);
            if($is_overdue){
                $status = 'Overdue';
            }elseif($is_course_complete){
                $status = 'Completed';
            }else{
                $status = 'Pending';
            }
                $enrollcourses = new stdClass();
                $courseobject = get_course($cdata->id);
                $enrollcourses->id = $cdata->id;
                $enrollcourses->fullname  = $cdata->fullname;
                $enrollcourses->shortname = $cdata->shortname;
                $enrollcourses->sortorder = $cdata->sortorder;
                $enrollcourses->visible   = $cdata->visible;
                $enrollcourses->imageurl  = get_course_image($cdata->id);
                $enrollcourses->categoryname = $categoey_detail->name;
				$enrollcourses->categoey_sortname = $categoey_detail->idnumber;
                $enrollcourses->courseprogress = progress::get_course_progress_percentage($courseobject, $userid);
                $enrollcourses->is_favourite = is_favourite($cdata->id,$userid);
                $enrollcourses->lastaccess      = get_last_access_course($cdata->id, $userid);
                $enrollcourses->coursestatus = $status;
                $enrollcourses->parent_department = get_course_parent_department($cdata->path);;
                $myacourses[] = $enrollcourses; 
               
            }
           
           
           
          
            return array('courses'=>$myacourses);
        }else{
            return array('courses'=>[]);   
        }
    }
	
	 function check_overdue($courseid)
    {
        global $DB;

        $time = time();
       
        $sql = "SELECT * FROM {course} WHERE id = $courseid AND enddate < $time AND enddate !=0";

        $coursedata = $DB->get_record_sql($sql, null);

        if(!empty($coursedata)){

            return true;
        }else{
            return false;
        }

    }

    function not_started_course($courseid,$userid){

        global $DB;

        $time = time();
       
        $sql = "SELECT * FROM {user_lastaccess} WHERE userid=$userid AND courseid=$courseid";

        $coursedata = $DB->get_record_sql($sql, null);

        if(empty($coursedata)){

            return true;
        }else{
            return false;
        }

    }

    function is_course_complete($course_id,$userid){
       
        global $DB, $CFG;
        require_once("{$CFG->libdir}/completionlib.php");
        $course_object = $DB->get_record('course', array('id'=>$course_id));
        $cinfo = new completion_info($course_object);
        $iscomplete = $cinfo->is_course_complete($userid);
        if(!$iscomplete){
            $iscomplete = 0;
        }
        return $iscomplete;
      }

    function mycourses_status_data($userid)
    {
        global $DB,$CFG;

        $sql = "SELECT c.* FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE c.visible = 1 AND ue.userid = " . $userid ;

        $coursedata = $DB->get_records_sql($sql, null);


        $notstarted = array();
        $complete = array();
        $pending = array();
        if (!empty($coursedata)) {
            foreach ($coursedata as $cdata) {
				$categoey_detail = get_category_detail($cdata->category);
                $enrollcourses = new stdClass();
                $courseobject = get_course($cdata->id);
                // $is_overdue = check_overdue($cdata->id);
                $is_notstarted = not_started_course($cdata->id,$userid);
                $is_course_complete = is_course_complete($cdata->id,$userid);

                if($is_course_complete)
                {
                    $enrollcourses->id = $cdata->id;
                    $enrollcourses->fullname  = $cdata->fullname;
                    $enrollcourses->shortname = $cdata->shortname;
                    $enrollcourses->sortorder = $cdata->sortorder;
                    $enrollcourses->visible   = $cdata->visible;
                    $enrollcourses->imageurl  = get_course_image($cdata->id);
                    $enrollcourses->categoryname = $categoey_detail->name;
					$enrollcourses->categoey_sortname = $categoey_detail->idnumber;
                    $enrollcourses->courseprogress = progress::get_course_progress_percentage($courseobject, $userid);
                    $enrollcourses->is_favourite = is_favourite($cdata->id,$userid);
                    $enrollcourses->lastaccess      = get_last_access_course($cdata->id, $userid);
                    $complete[] = $enrollcourses; 

                }elseif($is_notstarted){
                $enrollcourses->id = $cdata->id;
                $enrollcourses->fullname  = $cdata->fullname;
                $enrollcourses->shortname = $cdata->shortname;
                $enrollcourses->sortorder = $cdata->sortorder;
                $enrollcourses->visible   = $cdata->visible;
                $enrollcourses->imageurl  = get_course_image($cdata->id);
                $enrollcourses->categoryname = $categoey_detail->name;
                $enrollcourses->categoey_sortname = $categoey_detail->idnumber;
                $enrollcourses->courseprogress = progress::get_course_progress_percentage($courseobject, $userid);
                $enrollcourses->is_favourite = is_favourite($cdata->id,$userid);
                $enrollcourses->lastaccess      = get_last_access_course($cdata->id, $userid);
                $notstarted[] = $enrollcourses;

                }else{

                    $enrollcourses->id = $cdata->id;
                    $enrollcourses->fullname  = $cdata->fullname;
                    $enrollcourses->shortname = $cdata->shortname;
                    $enrollcourses->sortorder = $cdata->sortorder;
                    $enrollcourses->visible   = $cdata->visible;
                    $enrollcourses->imageurl  = get_course_image($cdata->id);
                    $enrollcourses->categoryname = $categoey_detail->name;
					$enrollcourses->categoey_sortname = $categoey_detail->idnumber;
                    $enrollcourses->courseprogress = progress::get_course_progress_percentage($courseobject, $userid);
                    $enrollcourses->is_favourite = is_favourite($cdata->id,$userid);
                    $enrollcourses->lastaccess      = get_last_access_course($cdata->id, $userid);
                    $pending[] = $enrollcourses; 

                }
               
            }

            return array('notstarted'=>$notstarted,'complete'=>$complete,'pending'=>$pending);

    }else{
        return array('notstarted'=>[],'complete'=>[],'pending'=>[]);
    }
}

function get_favourit_course($userid)
{
    global $DB;

    $sql = "SELECT f.*,cc.path FROM {favourite} as f join {course} as c ON f.itemid = c.id JOIN {course_categories} AS cc ON cc.id = c.category  WHERE f.itemtype = 'courses' AND c.visible = 1 AND f.userid = $userid";

    $coursedata = $DB->get_records_sql($sql, null);

    $favourite = array();
    if (!empty($coursedata)) {
        foreach ($coursedata as $data) {
			//** Checking course completion station
			$is_overdue = check_overdue($data->itemid);
            $is_course_complete = is_course_complete($data->itemid,$userid);
            if($is_overdue){
                $status = 'Overdue';
            }elseif($is_course_complete){
                $status = 'Completed';
            }else{
                $status = 'Pending';
            }
			//** Checking course completion station
			
			
            $courseobject = get_course($data->itemid);
			$categoey_detail = get_category_detail($courseobject->category);
            $enrollcourses = new stdClass();
            $enrollcourses->id = $data->itemid;
            $enrollcourses->fullname = $courseobject->fullname;
            $enrollcourses->shortname = $courseobject->shortname;
            $enrollcourses->sortorder = $courseobject->sortorder;
            $enrollcourses->visible = $courseobject->visible;
            $enrollcourses->imageurl = get_course_image($data->itemid);
            $enrollcourses->categoryname = $categoey_detail->name;
			$enrollcourses->categoey_sortname = $categoey_detail->idnumber;
            $enrollcourses->courseprogress = progress::get_course_progress_percentage($courseobject, $userid);
            $enrollcourses->lastaccess = get_last_access_course($data->itemid, $userid);            
			$enrollcourses->is_course_complete = $status;
            $enrollcourses->parent_department = get_course_parent_department($data->path);
            $favourite[] = $enrollcourses;

        }
        return array('favourite' => $favourite);
    } else {
        return array('favourite' => $favourite);
    }

}

function is_favourite($courseid,$userid)
{
    global $DB;

    $data = $DB->get_record('favourite',array('itemtype'=>'courses','itemid'=>$courseid,'userid'=>$userid));

    if(!empty($data)){
        return true;
    }else{
        return false;
    }
}

function is_module_complete($cmid,$userid)
{
    global $DB;

    $result = $DB->get_field('course_modules_completion', 'completionstate', array('coursemoduleid'=>$cmid,'userid'=>$userid), $strictness=IGNORE_MISSING);
	
	if(empty($result)){
		
		return 0;
	}else{
    return $result;
	}
}


function get_user_department($userid)
{
    global $DB;

        $result = $DB->get_records('cohort_members',array('userid'=>$userid));
		
        $department = array();
        $data = array();
       foreach($result as $cohortid)
       {
            $department[] = $cohortid->cohortid;
       }

       $str_ids = implode(",",$department);

     
       if(!empty($str_ids))
       {

            $sql = "SELECT userid from {cohort_members} WHERE cohortid IN($str_ids)";
            $userids =   $DB->get_records_sql($sql, NULL);
            $id = array();
       
            foreach($userids as $key => $uids)
                {
                    $id[] = $key;
                }

       return $id;

       }else{
           return false;
       }
}

function user_leaderboard($userid,$timeslot)
{
$dep = get_user_department_name($userid); 
    global $DB,$CFG,$USER,$PAGE;
   // require_once($CFG->wwwroot . '/grade/querylib.php');
    $userid_or_ids = get_user_department($userid);
	
    $str_users = implode(",",$userid_or_ids);
	
    $categorydata = array();
     $sql = "SELECT c.* FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE ue.userid = " . $userid;

        $coursedata = $DB->get_records_sql($sql, null);
        
        $maxgradesum = 0;
        $courseids = array();
          

        foreach($coursedata as $course){
        
          /*  // $data[$course->id] = grade_get_course_grades($course->id, $userid_or_ids);
           $grade_item = grade_item::fetch_course_item($course->id);
           $item = new stdClass();
          
           //$item->scaleid    = $grade_item->scaleid;
           //$item->name       = $grade_item->get_name();
           $item->grademin   = $grade_item->grademin;
           $item->grademax   = $grade_item->grademax;
           $item->gradepass  = $grade_item->gradepass;
           //$item->locked     = $grade_item->is_locked();
           //$item->hidden     = $grade_item->is_hidden();
           //$item->courseid   = $course->id;
           $item->grades     = array();  */         

        //    foreach($grade_detail as $grades){

        //     $maxgradeuser["userid"] = $grades->id;

        //     $maxgradeuser['totalgrade'] += $grades->finalgrade;

        //   $item->grades[$grades->id] = $grades;

		$mxagsql = $DB->get_field('grade_items', 'grademax', array('itemtype'=>'course','courseid'=>$course->id), $strictness=IGNORE_MISSING);

               $maxgradesum += round($mxagsql);
             
                $courseids[] = $course->id;
                
//$allactivity = get_array_of_activities($course->id);				
	//			print_object($allactivity);
				
           }
			
           $str_courseid = implode(",",$courseids);

           $todaylast = strtotime('today');
           $current = time();
           $lastmonday = strtotime("last monday");
           $lastmonth = strtotime('last month');
           $last3month = strtotime('-3 month');

			if(!empty($str_courseid) && !empty($str_users)){

                $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid  WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL  AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
                $grade_detail = $DB->get_records_sql($sql,NULL);
            
                $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid  WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $todaylast AND gg.timemodified < $current) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
                $grade_detail_day = $DB->get_records_sql($sql,NULL);

                $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $lastmonday AND gg.timemodified < $current ) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
                $grade_detail_week = $DB->get_records_sql($sql,NULL);

                $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $lastmonth AND gg.timemodified < $current) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
                $grade_detail_month = $DB->get_records_sql($sql,NULL);
                $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid   WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $last3month AND gg.timemodified < $current) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
                $grade_detail_quarter = $DB->get_records_sql($sql,NULL);

			}else{
				
				return false;
			}
          
          $usergrades = array(); 
          $mygrade = array();
		  $mydata = array(); 
          $rank = 1;
		 
           foreach($grade_detail as $key=>$ug)
           {
			   if(get_rank($ug->id) != 0){
				$resource_score = get_user_resourse_score($ug->id);
				
            $userdata = $DB->get_record('user',array('id'=>$ug->id));
            $userpictures = new user_picture($userdata);
            $userpictures->size = 1; // Size f1.
            $profileimageurls = $userpictures->get_url($PAGE);
                    $gradedata = array();         
                    $gradedata['userid'] = $ug->id;
					$gradedata['dep'] = $dep;
                    $gradedata['rank'] = get_rank($ug->id);
                    $gradedata['firstname'] = $ug->firstname;
                    $gradedata['lastname'] = $ug->lastname;
                    $gradedata['email'] = $ug->email;
                    $gradedata['userimage'] = $profileimageurls->out(false);
					$gradedata['finalgrade'] = (string)final_score($ug->id);
           
					
                    if($userid == $ug->id){
						
                                
                        $mydata['userid'] = $ug->id;
						$mydata['dep'] = $dep;
                        $mydata['rank'] = get_rank($ug->id);
                        $mydata['firstname'] = $ug->firstname;
                        $mydata['lastname'] = $ug->lastname;
                        $mydata['email'] = $ug->email;
                        $mydata['userimage'] = $profileimageurls->out(false);
                        $mydata['finalgrade'] = (string)final_score($ug->id);                
						
						
						$bdata = get_all_badges($ug->id);		 
						
						$allbadges = array();
						
						foreach($bdata as $badges_data){
							
						$allbadgesData = array();
						
						$allbadgesData['id'] = $badges_data->id;
						$allbadgesData['url'] = $CFG->wwwroot.'/pix/frame218.png';
						$allbadgesData['badgeunlock'] = false; 	
						
						if(round($ug->finalgrade) > $badges_data->score){
							$allbadgesData['url'] = $badges_data->url;
								$allbadgesData['badgeunlock'] = true; 
						}elseif(round($ug->finalgrade) > $badges_data->score){
							$allbadgesData['url'] = $badges_data->url;
							$allbadgesData['badgeunlock'] = true; 
						}elseif(round($ug->finalgrade) > $badges_data->score){
							$allbadgesData['url'] = $badges_data->url;
							$allbadgesData['badgeunlock'] = true; 
						}elseif(round($ug->finalgrade) > $badges_data->score){
							$allbadgesData['url'] = $badges_data->url;
							$allbadgesData['badgeunlock'] = true; 
						}elseif(round($ug->finalgrade) > $badges_data->score){
							$allbadgesData['url'] = $badges_data->url;
							$allbadgesData['badgeunlock'] = true; 
						}	
						
						$allbadges[] = $allbadgesData;
						}
						$mydata['badges'] = $allbadges;
						
                        $mygrade[$key] = $mydata;
                   }
                $usergrades[$key] = $gradedata;
                $rank++;
           }
           
           
		   if(empty($mydata)){
						
						$userdata = $DB->get_record('user',array('id'=>$userid));
						$userpictures = new user_picture($userdata);
						$userpictures->size = 1; // Size f1.
						$profileimageurls = $userpictures->get_url($PAGE);
			
						$mydata['userid'] = $userid;
						$mydata['dep'] = $dep;
                        $mydata['rank'] = 0;
                        $mydata['firstname'] = $userdata->firstname;
                        $mydata['lastname'] = $userdata->lastname;
                        $mydata['email'] = $userdata->email;
                        $mydata['userimage'] = $profileimageurls->out(false);
                        $mydata['finalgrade'] = (string)final_score($ug->id);  

						$mydata['badgeunlock1'] = false; 
						$mydata['badgeunlock2'] = false; 
						$mydata['badgeunlock3'] = false; 
						$mydata['badgeunlock4'] = false; 
						$mydata['badgeunlock5'] = false; 
						
						if(round($ug->finalgrade) > 20){
								$mydata['badgeunlock1'] = true; 
						}elseif(round($ug->finalgrade) > 500){
							$mydata['badgeunlock2'] = true; 
						}elseif(round($ug->finalgrade) > 1000){
							$mydata['badgeunlock3'] = true; 
						}elseif(round($ug->finalgrade) > 1500){
							$mydata['badgeunlock4'] = true; 
						}elseif(round($ug->finalgrade) > 2000){
							$mydata['badgeunlock5'] = true; 
						}	
						
						
											
    
                        $mygrade[$key] = $mydata;
		   }
		   }
           
            // day data

            $usergrades_day = array(); 
            $mygrade_day = array();
            $mydata_day = array(); 
            $rank_day = 1;
           foreach($grade_detail_day as $key=>$ug)
           {

            $userdata = $DB->get_record('user',array('id'=>$ug->id));
            $userpictures = new user_picture($userdata);
            $userpictures->size = 1; // Size f1.
            $profileimageurls = $userpictures->get_url($PAGE);
                    $gradedata_day = array();         
                    $gradedata_day['userid'] = $ug->id;
					$gradedata_day['dep'] = $dep;
                    $gradedata_day['rank'] = $rank_day;
                    $gradedata_day['firstname'] = $ug->firstname;
                    $gradedata_day['lastname'] = $ug->lastname;
                    $gradedata_day['email'] = $ug->email;
                    $gradedata_day['userimage'] = $profileimageurls->out(false);
                    $gradedata_day['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);
            
					
                    if($userid == $ug->id){
						
                                
                        $mydata_day['userid'] = $ug->id;
						$mydata_day['dep'] = $dep;
                        $mydata_day['rank'] = $rank_day;
                        $mydata_day['firstname'] = $ug->firstname;
                        $mydata_day['lastname'] = $ug->lastname;
                        $mydata_day['email'] = $ug->email;
                        $mydata_day['userimage'] = $profileimageurls->out(false);
                        $mydata_day['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);                
    
                        $mygrade[$key] = $mydata_day;
                   }
                $usergrades_day[$key] = $gradedata_day;
                $rank_day++;
           }
           
		   if(empty($mydata_day)){
						
						$userdata = $DB->get_record('user',array('id'=>$userid));
						$userpictures = new user_picture($userdata);
						$userpictures->size = 1; // Size f1.
						$profileimageurls = $userpictures->get_url($PAGE);
			
						$mydata_day['userid'] = $userid;
						$mydata_day['dep'] = $dep;
                        $mydata_day['rank'] = 0;
                        $mydata_day['firstname'] = $userdata->firstname;
                        $mydata_day['lastname'] = $userdata->lastname;
                        $mydata_day['email'] = $userdata->email;
                        $mydata_day['userimage'] = $profileimageurls->out(false);
                        $mydata_day['finalgrade'] = (string)get_user_resourse_score($userid);                  
    
                        $mygrade[$key] = $mydata_day;
		   }
           
           // week data

           $usergrades_week = array(); 
           $mygrade_week = array();
           $mydata_week = array(); 
           $rank_week = 1;
          foreach($grade_detail_week as $key=>$ug)
          {

           $userdata = $DB->get_record('user',array('id'=>$ug->id));
           $userpictures = new user_picture($userdata);
           $userpictures->size = 1; // Size f1.
           $profileimageurls = $userpictures->get_url($PAGE);
                   $gradedata_week = array();         
                   $gradedata_week['userid'] = $ug->id;
				   $gradedata_week['dep'] = $dep;
                   $gradedata_week['rank'] = $rank_week;
                   $gradedata_week['firstname'] = $ug->firstname;
                   $gradedata_week['lastname'] = $ug->lastname;
                   $gradedata_week['email'] = $ug->email;
                   $gradedata_week['userimage'] = $profileimageurls->out(false);
                   $gradedata_week['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);
           
                   
                   if($userid == $ug->id){
                       
                               
                       $mydata_week['userid'] = $ug->id;
					   $mydata_week['dep'] = $dep;
                       $mydata_week['rank'] = $rank_week;
                       $mydata_week['firstname'] = $ug->firstname;
                       $mydata_week['lastname'] = $ug->lastname;
                       $mydata_week['email'] = $ug->email;
                       $mydata_week['userimage'] = $profileimageurls->out(false);
                       $mydata_week['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);                
   
                       $mygrade[$key] = $mydata_week;
                  }
               $usergrades_week[$key] = $gradedata_week;
               $rank_week++;
          }
          
          if(empty($mydata_week)){
                       
                       $userdata = $DB->get_record('user',array('id'=>$userid));
                       $userpictures = new user_picture($userdata);
                       $userpictures->size = 1; // Size f1.
                       $profileimageurls = $userpictures->get_url($PAGE);
           
                       $mydata_week['userid'] = $userid;
					    $mydata_week['dep'] = $dep;
                       $mydata_week['rank'] = 0;
                       $mydata_week['firstname'] = $userdata->firstname;
                       $mydata_week['lastname'] = $userdata->lastname;
                       $mydata_week['email'] = $userdata->email;
                       $mydata_week['userimage'] = $profileimageurls->out(false);
                       $mydata_week['finalgrade'] = (string)get_user_resourse_score($userid);                 
   
                       $mygrade[$key] = $mydata_week;
          }
          

          // month data

          $usergrades_month = array(); 
           $mygrade_month = array();
           $mydata_month = array(); 
           $rank_month = 1;
          foreach($grade_detail_month as $key=>$ug)
          {

           $userdata = $DB->get_record('user',array('id'=>$ug->id));
           $userpictures = new user_picture($userdata);
           $userpictures->size = 1; // Size f1.
           $profileimageurls = $userpictures->get_url($PAGE);
                   $gradedata_month = array();         
                   $gradedata_month['userid'] = $ug->id;
				    $gradedata_month['dep'] = $dep;
                   $gradedata_month['rank'] = $rank_month;
                   $gradedata_month['firstname'] = $ug->firstname;
                   $gradedata_month['lastname'] = $ug->lastname;
                   $gradedata_month['email'] = $ug->email;
                   $gradedata_month['userimage'] = $profileimageurls->out(false);
                   $gradedata_month['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);
           
                   
                   if($userid == $ug->id){
                       
                               
                       $mydata_month['userid'] = $ug->id;
					   $mydata_month['dep'] = $dep;
                       $mydata_month['rank'] = $rank_month;
                       $mydata_month['firstname'] = $ug->firstname;
                       $mydata_month['lastname'] = $ug->lastname;
                       $mydata_month['email'] = $ug->email;
                       $mydata_month['userimage'] = $profileimageurls->out(false);
                       $mydata_month['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);                
   
                       $mygrade[$key] = $mydata_month;
                  }
               $usergrades_month[$key] = $gradedata_month;
               $rank_month++;
          }
          
          if(empty($mydata_month)){
                       
                       $userdata = $DB->get_record('user',array('id'=>$userid));
                       $userpictures = new user_picture($userdata);
                       $userpictures->size = 1; // Size f1.
                       $profileimageurls = $userpictures->get_url($PAGE);
           
                       $mydata_month['userid'] = $userid;
					    $mydata_month['dep'] = $dep;
                       $mydata_month['rank'] = 0;
                       $mydata_month['firstname'] = $userdata->firstname;
                       $mydata_month['lastname'] = $userdata->lastname;
                       $mydata_month['email'] = $userdata->email;
                       $mydata_month['userimage'] = $profileimageurls->out(false);
                       $mydata_month['finalgrade'] = (string)get_user_resourse_score($userid);                  
   
                       $mygrade[$key] = $mydata_month;
          }
          

          // quarter data

          $usergrades_quarter = array(); 
           $mygrade_quarter = array();
           $mydata_quarter = array(); 
           $rank_quarter = 1;
          foreach($grade_detail_quarter as $key=>$ug)
          {

           $userdata = $DB->get_record('user',array('id'=>$ug->id));
           $userpictures = new user_picture($userdata);
           $userpictures->size = 1; // Size f1.
           $profileimageurls = $userpictures->get_url($PAGE);
                   $gradedata_quarter = array();         
                   $gradedata_quarter['userid'] = $ug->id;
				    $gradedata_quarter['dep'] = $dep;
                   $gradedata_quarter['rank'] = $rank_quarter;
                   $gradedata_quarter['firstname'] = $ug->firstname;
                   $gradedata_quarter['lastname'] = $ug->lastname;
                   $gradedata_quarter['email'] = $ug->email;
                   $gradedata_quarter['userimage'] = $profileimageurls->out(false);
                   $gradedata_quarter['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);
           
                   
                   if($userid == $ug->id){
                       
                               
                       $mydata_quarter['userid'] = $ug->id;
					   $mydata_quarter['dep'] = $dep;
                       $mydata_quarter['rank'] = $rank_quarter;
                       $mydata_quarter['firstname'] = $ug->firstname;
                       $mydata_quarter['lastname'] = $ug->lastname;
                       $mydata_quarter['email'] = $ug->email;
                       $mydata_quarter['userimage'] = $profileimageurls->out(false);
                       $mydata_quarter['finalgrade'] = (string)(round($ug->finalgrade)+ $resource_score);                
   
                       $mygrade[$key] = $mydata_quarter;
                  }
               $usergrades_quarter[$key] = $gradedata_quarter;
               $rank_quarter++;
          }
          
          if(empty($mydata_quarter)){
                       
                       $userdata = $DB->get_record('user',array('id'=>$userid));
                       $userpictures = new user_picture($userdata);
                       $userpictures->size = 1; // Size f1.
                       $profileimageurls = $userpictures->get_url($PAGE);
           
                       $mydata_quarter['userid'] = $userid;
					    $mydata_quarter['dep'] = $dep;
                       $mydata_quarter['rank'] = 0;
                       $mydata_quarter['firstname'] = $userdata->firstname;
                       $mydata_quarter['lastname'] = $userdata->lastname;
                       $mydata_quarter['email'] = $userdata->email;
                       $mydata_quarter['userimage'] = $profileimageurls->out(false);
                       $mydata_quarter['finalgrade'] = (string)get_user_resourse_score($userid);                
   
                       $mygrade[$key] = $mydata_quarter;
          }
   
        $finalreport = new stdClass();
        $finalreport->maxgrade = $maxgradesum;
        $finalreport->userreport = $usergrades;
        $finalreport->mydata = $mydata;
        $finalreport->gradedata_day = $usergrades_day;
        $finalreport->mydata_day = $mydata_day;
        $finalreport->gradedata_week = $usergrades_week;
        $finalreport->mydata_week = $mydata_week;
        $finalreport->gradedata_month = $usergrades_month;
        $finalreport->mydata_month = $mydata_month;
        $finalreport->gradedata_quarter = $usergrades_quarter;
        $finalreport->mydata_quarter = $mydata_quarter;


   
   
    
    return $finalreport;
}


function leaderboard_by_categoryid($categoryid,$userid)
{
    global $DB,$PAGE;
	$dep = get_user_department_name($userid);  
    $sql = "SELECT c.* FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE ue.userid = " . $userid . " AND c.category = " . $categoryid;

    $coursedata = $DB->get_records_sql($sql, null);
    $courseids = array();
    $enroldata = array();
    $maxgradesum = 0;
    foreach($coursedata as $course)
    {
        /* $grade_item = grade_item::fetch_course_item($course->id);
        $item = new stdClass();
       
        $item->scaleid    = $grade_item->scaleid;
        $item->name       = $grade_item->get_name();
        $item->grademin   = $grade_item->grademin;
        $item->grademax   = $grade_item->grademax;
        $item->gradepass  = $grade_item->gradepass;
        $item->locked     = $grade_item->is_locked();
        $item->hidden     = $grade_item->is_hidden();
        $item->courseid   = $course->id;
        $item->grades     = array();  */         

$mxagsql = $DB->get_field('grade_items', 'grademax', array('itemtype'=>'course','courseid'=>$course->id), $strictness=IGNORE_MISSING);

                $maxgradesum += round($mxagsql);
            // $maxgradesum += $item->grademax;
     
        $courseids[] = $course->id;
        $modcontext =context_course::instance($course->id);
       $enroldata[] = get_enrolled_users($modcontext, 'mod/assignment:submit');
    }
   /*  $userids = array();
    foreach($enroldata as $enroll)
    {
         foreach($enroll as $userdata){
             $userids[] = $userdata->id; 
         }   
    } */

        //$unice_user = array_unique($userids);

        $str_courseid = implode(",",$courseids);
	   
       // $str_users = implode(",",$unice_user);
		 $userid_or_ids = get_user_department($userid);	
		 $str_users = implode(",",$userid_or_ids);
	
        
        $todaylast = strtotime('today');
        $current = time();
        $lastmonday = strtotime("last monday");
        $lastmonth = strtotime('last month');
        $last3month = strtotime('-3 month');

         if(!empty($str_courseid) && !empty($str_users)){

             $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL  AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC ";
             $grade_detail = $DB->get_records_sql($sql,NULL);
         
             $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $todaylast AND gg.timemodified < $current) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
             $grade_detail_day = $DB->get_records_sql($sql,NULL);

             $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $lastmonday AND gg.timemodified < $current ) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
             $grade_detail_week = $DB->get_records_sql($sql,NULL);

             $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $lastmonth AND gg.timemodified < $current) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
             $grade_detail_month = $DB->get_records_sql($sql,NULL);
             $sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as finalgrade ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL AND (gg.timemodified > $last3month AND gg.timemodified < $current) AND gg.userid IN ($str_users) AND gi.courseid IN ($str_courseid) GROUP BY gg.userid ORDER BY finalgrade DESC";
             $grade_detail_quarter = $DB->get_records_sql($sql,NULL);

         }else{
             
             return false;
         }
      // print_object($grade_detail);die;
       $usergrades = array(); 
       $mygrade = array();
       $mydata = array(); 
       $rank = 1;
        foreach($grade_detail as $key=>$ug)
        {

         $userdata = $DB->get_record('user',array('id'=>$ug->id));
         $userpictures = new user_picture($userdata);
         $userpictures->size = 1; // Size f1.
         $profileimageurls = $userpictures->get_url($PAGE);
                 $gradedata = array();         
                 $gradedata['userid'] = $ug->id;
				  $gradedata['dep'] = $dep;
                 $gradedata['rank'] = $rank;
                 $gradedata['firstname'] = $ug->firstname;
                 $gradedata['lastname'] = $ug->lastname;
                 $gradedata['email'] = $ug->email;
                 $gradedata['userimage'] = $profileimageurls->out(false);;
                 $gradedata['finalgrade'] = (string)round($ug->finalgrade);
         
                 
                 if($userid == $ug->id){
                     
                              
                     $mydata['userid'] = $ug->id;
					  $mydata['dep'] = $dep;
                     $mydata['rank'] = $rank;
                     $mydata['firstname'] = $ug->firstname;
                     $mydata['lastname'] = $ug->lastname;
                     $mydata['email'] = $ug->email;
                     $mydata['userimage'] = $profileimageurls->out(false);
                     $mydata['finalgrade'] = (string)round($ug->finalgrade);                
 
                     $mygrade[$key] = $mydata;
                }
             $usergrades[$key] = $gradedata;
             $rank++;
        }
        
        
        if(empty($mydata)){
                     
                     $userdata = $DB->get_record('user',array('id'=>$userid));
                     $userpictures = new user_picture($userdata);
                     $userpictures->size = 1; // Size f1.
                     $profileimageurls = $userpictures->get_url($PAGE);
         
                     $mydata['userid'] = $userid;
					 $mydata['dep'] = $dep;
                     $mydata['rank'] = 0;
                     $mydata['firstname'] = $userdata->firstname;
                     $mydata['lastname'] = $userdata->lastname;
                     $mydata['email'] = $userdata->email;
                     $mydata['userimage'] = $profileimageurls->out(false);
                     $mydata['finalgrade'] = '0';               
 
                     $mygrade[$key] = $mydata;
        }
        
         // day data

         $usergrades_day = array(); 
         $mygrade_day = array();
         $mydata_day = array(); 
         $rank_day = 1;
        foreach($grade_detail_day as $key=>$ug)
        {

         $userdata = $DB->get_record('user',array('id'=>$ug->id));
         $userpictures = new user_picture($userdata);
         $userpictures->size = 1; // Size f1.
         $profileimageurls = $userpictures->get_url($PAGE);
                 $gradedata_day = array();         
                 $gradedata_day['userid'] = $ug->id;
				 $gradedata_day['dep'] = $dep;
                 $gradedata_day['rank'] = $rank_day;
                 $gradedata_day['firstname'] = $ug->firstname;
                 $gradedata_day['lastname'] = $ug->lastname;
                 $gradedata_day['email'] = $ug->email;
                 $gradedata_day['userimage'] = $profileimageurls->out(false);;
                 $gradedata_day['finalgrade'] = (string)round($ug->finalgrade);
         
                 
                 if($userid == $ug->id){
                     
                             
                     $mydata_day['userid'] = $ug->id;
					 $mydata_day['dep'] = $dep;
                     $mydata_day['rank'] = $rank_day;
                     $mydata_day['firstname'] = $ug->firstname;
                     $mydata_day['lastname'] = $ug->lastname;
                     $mydata_day['email'] = $ug->email;
                     $mydata_day['userimage'] = $profileimageurls->out(false);
                     $mydata_day['finalgrade'] = (string)round($ug->finalgrade);                
 
                     $mygrade[$key] = $mydata_day;
                }
             $usergrades_day[$key] = $gradedata_day;
             $rank_day++;
        }
        
        if(empty($mydata_day)){
                     
                     $userdata = $DB->get_record('user',array('id'=>$userid));
                     $userpictures = new user_picture($userdata);
                     $userpictures->size = 1; // Size f1.
                     $profileimageurls = $userpictures->get_url($PAGE);
         
                     $mydata_day['userid'] = $userid;
					  $mydata_day['dep'] = $dep;
                     $mydata_day['rank'] = 0;
                     $mydata_day['firstname'] = $userdata->firstname;
                     $mydata_day['lastname'] = $userdata->lastname;
                     $mydata_day['email'] = $userdata->email;
                     $mydata_day['userimage'] = $profileimageurls->out(false);
                     $mydata_day['finalgrade'] = (string)round($ug->finalgrade);                
 
                     $mygrade[$key] = $mydata_day;
        }
        
        // week data

        $usergrades_week = array(); 
        $mygrade_week = array();
        $mydata_week = array(); 
        $rank_week = 1;
       foreach($grade_detail_week as $key=>$ug)
       {

        $userdata = $DB->get_record('user',array('id'=>$ug->id));
        $userpictures = new user_picture($userdata);
        $userpictures->size = 1; // Size f1.
        $profileimageurls = $userpictures->get_url($PAGE);
                $gradedata_week = array();         
                $gradedata_week['userid'] = $ug->id;
				 $gradedata_week['dep'] = $dep;
                $gradedata_week['rank'] = $rank_week;
                $gradedata_week['firstname'] = $ug->firstname;
                $gradedata_week['lastname'] = $ug->lastname;
                $gradedata_week['email'] = $ug->email;
                $gradedata_week['userimage'] = $profileimageurls->out(false);;
                $gradedata_week['finalgrade'] = (string)round($ug->finalgrade);
        
                
                if($userid == $ug->id){
                    
                            
                    $mydata_week['userid'] = $ug->id;
					 $mydata_week['dep'] = $dep;
                    $mydata_week['rank'] = $rank_week;
                    $mydata_week['firstname'] = $ug->firstname;
                    $mydata_week['lastname'] = $ug->lastname;
                    $mydata_week['email'] = $ug->email;
                    $mydata_week['userimage'] = $profileimageurls->out(false);
                    $mydata_week['finalgrade'] = (string)round($ug->finalgrade);                

                    $mygrade[$key] = $mydata_week;
               }
            $usergrades_week[$key] = $gradedata_week;
            $rank_week++;
       }
       
       if(empty($mydata_week)){
                    
                    $userdata = $DB->get_record('user',array('id'=>$userid));
                    $userpictures = new user_picture($userdata);
                    $userpictures->size = 1; // Size f1.
                    $profileimageurls = $userpictures->get_url($PAGE);
        
                    $mydata_week['userid'] = $userid;
					 $mydata_week['dep'] = $dep;
                    $mydata_week['rank'] = 0;
                    $mydata_week['firstname'] = $userdata->firstname;
                    $mydata_week['lastname'] = $userdata->lastname;
                    $mydata_week['email'] = $userdata->email;
                    $mydata_week['userimage'] = $profileimageurls->out(false);
                    $mydata_week['finalgrade'] = (string)round($ug->finalgrade);              

                    $mygrade[$key] = $mydata_week;
       }
       

       // month data

       $usergrades_month = array(); 
        $mygrade_month = array();
        $mydata_month = array(); 
        $rank_month = 1;
       foreach($grade_detail_month as $key=>$ug)
       {

        $userdata = $DB->get_record('user',array('id'=>$ug->id));
        $userpictures = new user_picture($userdata);
        $userpictures->size = 1; // Size f1.
        $profileimageurls = $userpictures->get_url($PAGE);
                $gradedata_month = array();         
                $gradedata_month['userid'] = $ug->id;
				 $gradedata_month['dep'] = $dep;
                $gradedata_month['rank'] = $rank_month;
                $gradedata_month['firstname'] = $ug->firstname;
                $gradedata_month['lastname'] = $ug->lastname;
                $gradedata_month['email'] = $ug->email;
                $gradedata_month['userimage'] = $profileimageurls->out(false);;
                $gradedata_month['finalgrade'] = (string)round($ug->finalgrade);
        
                
                if($userid == $ug->id){
                    
                            
                    $mydata_month['userid'] = $ug->id;
					$mydata_month['dep'] = $dep;
                    $mydata_month['rank'] = $rank_month;
                    $mydata_month['firstname'] = $ug->firstname;
                    $mydata_month['lastname'] = $ug->lastname;
                    $mydata_month['email'] = $ug->email;
                    $mydata_month['userimage'] = $profileimageurls->out(false);
                    $mydata_month['finalgrade'] = (string)round($ug->finalgrade);                

                    $mygrade[$key] = $mydata_month;
               }
            $usergrades_month[$key] = $gradedata_month;
            $rank_month++;
       }
       
       if(empty($mydata_month)){
                    
                    $userdata = $DB->get_record('user',array('id'=>$userid));
                    $userpictures = new user_picture($userdata);
                    $userpictures->size = 1; // Size f1.
                    $profileimageurls = $userpictures->get_url($PAGE);
        
                    $mydata_month['userid'] = $userid;
					$mydata_month['dep'] = $dep;
                    $mydata_month['rank'] = 0;
                    $mydata_month['firstname'] = $userdata->firstname;
                    $mydata_month['lastname'] = $userdata->lastname;
                    $mydata_month['email'] = $userdata->email;
                    $mydata_month['userimage'] = $profileimageurls->out(false);
                    $mydata_month['finalgrade'] = (string)round($ug->finalgrade);               

                    $mygrade[$key] = $mydata_month;
       }
       

       // quarter data

       $usergrades_quarter = array(); 
        $mygrade_quarter = array();
        $mydata_quarter = array(); 
        $rank_quarter = 1;
       foreach($grade_detail_quarter as $key=>$ug)
       {

        $userdata = $DB->get_record('user',array('id'=>$ug->id));
        $userpictures = new user_picture($userdata);
        $userpictures->size = 1; // Size f1.
        $profileimageurls = $userpictures->get_url($PAGE);
                $gradedata_quarter = array();         
                $gradedata_quarter['userid'] = $ug->id;
				$gradedata_quarter['dep'] = $dep;
                $gradedata_quarter['rank'] = $rank_quarter;
                $gradedata_quarter['firstname'] = $ug->firstname;
                $gradedata_quarter['lastname'] = $ug->lastname;
                $gradedata_quarter['email'] = $ug->email;
                $gradedata_quarter['userimage'] = $profileimageurls->out(false);;
                $gradedata_quarter['finalgrade'] = (string)round($ug->finalgrade);
        
                
                if($userid == $ug->id){
                    
                            
                    $mydata_quarter['userid'] = $ug->id;
					$mydata_quarter['dep'] = $dep;
                    $mydata_quarter['rank'] = $rank_quarter;
                    $mydata_quarter['firstname'] = $ug->firstname;
                    $mydata_quarter['lastname'] = $ug->lastname;
                    $mydata_quarter['email'] = $ug->email;
                    $mydata_quarter['userimage'] = $profileimageurls->out(false);
                    $mydata_quarter['finalgrade'] = (string)round($ug->finalgrade);                

                    $mygrade[$key] = $mydata_quarter;
               }
            $usergrades_quarter[$key] = $gradedata_quarter;
            $rank_quarter++;
       }
       
       if(empty($mydata_quarter)){
                    
                    $userdata = $DB->get_record('user',array('id'=>$userid));
                    $userpictures = new user_picture($userdata);
                    $userpictures->size = 1; // Size f1.
                    $profileimageurls = $userpictures->get_url($PAGE);
        
                    $mydata_quarter['userid'] = $userid;
					$mydata_quarter['dep'] = $dep;
                    $mydata_quarter['rank'] = 0;
                    $mydata_quarter['firstname'] = $userdata->firstname;
                    $mydata_quarter['lastname'] = $userdata->lastname;
                    $mydata_quarter['email'] = $userdata->email;
                    $mydata_quarter['userimage'] = $profileimageurls->out(false);
                    $mydata_quarter['finalgrade'] = (string)round($ug->finalgrade);                 

                    $mygrade[$key] = $mydata_quarter;
       }

     $finalreport = new stdClass();
     $finalreport->maxgrade = $maxgradesum;
     $finalreport->userreport = array_slice($usergrades, 0, 10);
     $finalreport->mydata = $mydata;
     $finalreport->gradedata_day = $usergrades_day;
     $finalreport->mydata_day = $mydata_day;
     $finalreport->gradedata_week = $usergrades_week;
     $finalreport->mydata_week = $mydata_week;
     $finalreport->gradedata_month = $usergrades_month;
     $finalreport->mydata_month = $mydata_month;
     $finalreport->gradedata_quarter = $usergrades_quarter;
     $finalreport->mydata_quarter = $mydata_quarter;



    return $finalreport;

}


function get_user_department_name($userid)
{
		global $DB;
		
		$sql = "SELECT c.idnumber FROM {cohort} AS c JOIN {cohort_members} AS cm ON c.id = cm.cohortid WHERE cm.userid = $userid";
		
		$depart = $DB->get_record_sql($sql,null);
		
		$name = $depart->idnumber;
		if($name){
			$name = $name;
		}else{
			$name = 'None';
		}
		return $name;
}
function get_user_department_id($userid)
{
		global $DB;
		
		$sql = "SELECT c.id,c.idnumber FROM {cohort} AS c JOIN {cohort_members} AS cm ON c.id = cm.cohortid WHERE cm.userid = $userid";
		
		$depart = $DB->get_record_sql($sql,null);
		
		$name = $depart->id;
		if($name){
			$name = $name;
		}else{
			$name = '0';
		}
		return $name;
}

function get_my_course_count($userid)
{
	global $DB;
	
	 $sql = "SELECT c.id FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE  c.visible = 1 AND ue.userid = " . $userid;

    $coursedata = $DB->get_records_sql($sql, null);
	$count = count($coursedata);
	return $count;
	
	
}

function get_scorm_grade($userid,$cmid)
{
	global $DB;
	
	$score = $DB->get_field('grade_items', 'grademax', array('itemtype'=>'course','courseid'=>$course->id), $strictness=IGNORE_MISSING);
	
	return '';
	
}

function update_user_profile($userid,$lname)
{
	global $DB;	
	if($userid !="" && $lname !="" )
	{
	$uid = $userid;
$DB->execute("UPDATE {user} SET lastname= '{$lname}' WHERE id = '{$uid}'");
	}	
	//return '';	
}

function get_course_name($courseid)
{
	global $DB;	
	$course = $DB->get_record('course', array('id' => $courseid));
	return $course->fullname;
}

/*******************  Push Notification Function ******************************/
function course_start_notification(){

global $DB;    

	$url = "https://fcm.googleapis.com/fcm/send";
   
   $coursedata = get_courses();
	
	foreach($coursedata as $courseid){
	
	 $tokensql = "SELECT DISTINCT a.id,a.userid,b.device_token,c.courseid 
	FROM {user_enrolments} as a join {user} AS b ON a.userid=b.id join {enrol} as c on c.id=a.enrolid
	where  b.device_token != 'null' and b.device_token != '' and c.courseid=". $courseid->id ." and a.timestart = 0 and a.userid NOT IN (SELECT CONCAT(userid) FROM {local_check_puss_notification} WHERE courseid = ".$courseid->id. " and msgid = 1 )";
		$getuser = $DB->get_records_sql($tokensql,null);
		
		//print_object($getuser);
		
		
		$arr_deviceId = array();
		
		foreach($getuser as $user){
			//var_dump($user->device_token);
			if($user->device_token != 'null' && $user->device_token != ''){
				
			$arr_deviceId[] = $user->device_token;
			
			$insertdata = new stdclass;
			$insertdata->userid = $user->userid;
			$insertdata->courseid = $user->courseid;
			$insertdata->msgid = 1;
			$insertdata->timecreated = time();
			
			$DB->insert_record('local_check_puss_notification', $insertdata, $returnid=true, $bulk=false);
			
			}
		}
//print_object($arr_deviceId);
	
 // $serverKey = 'AAAAJv9DEKA:APA91bGZdAki8i4bCVFtqD65j73dB2AJyKh0EOgShospx11BKrIudMh9PcxH8ZdE6HHjZyXtXSwriwfnialol6nHxxrm62Cm_6Qtncx4C81YPEAHAjOVJdxmNG5sT2o05rtbM6--IDDc';
  $serverKey = SERVER_KEY;

  $arr_messageInfo['subject'] = "Pending Course Alert";
$arr_messageInfo['messageBody'] = $courseid->fullname. " course is pending for you.";
$notification = array('title' => $arr_messageInfo['subject'] , 'body' => $arr_messageInfo['messageBody'], 'sound' => 'default'); //, 'badge' => '1'

				if(count($arr_deviceId)) {
					$arrayToSend = array('registration_ids' => $arr_deviceId, 'notification' => $notification, 'priority' => 'high');//for multiple device

					$json = json_encode($arrayToSend);

					$headers = array();
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Authorization: key='. $serverKey;

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
					curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
					
					//curl_setopt($ch, CURLOPT_VERBOSE, 0);
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					//curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to 
					//Send the request
					$response = curl_exec($ch);
					//Close request
					if ($response === FALSE) {
					die('FCM Send Error: ' . curl_error($ch));
					}
					curl_close($ch);
				}
	
	
	}
	}
	
function course_complete_notification(){
	
	
global $DB;    

	$url = "https://fcm.googleapis.com/fcm/send";
   
	$coursedata = get_courses();
	
	foreach($coursedata as $courseid){
	
	$tokensql = "SELECT DISTINCT a.id,a.course,c.fullname,a.userid,b.device_token 
	FROM {course_completions} as a join {user} AS b ON a.userid=b.id 
	join {course} as c ON a.course=c.id 
	where a.timecompleted IS NOT NULL and b.device_token != 'null' and b.device_token != '' and a.course=". $courseid->id ." and a.userid NOT IN (SELECT CONCAT(userid) FROM {local_check_puss_notification} WHERE courseid = ".$courseid->id." and msgid = 2)";
		$getuser = $DB->get_records_sql($tokensql,null);
		
		//print_object($getuser);
		
		
		$arr_deviceId = array();
		
		foreach($getuser as $user){
			//var_dump($user->device_token);
			if($user->device_token != 'null' && $user->device_token != ''){
				
			$arr_deviceId[] = $user->device_token;
			
			$insertdata = new stdclass;
			$insertdata->userid = $user->userid;
			$insertdata->courseid = $user->course;
			$insertdata->msgid = 2;
			$insertdata->timecreated = time();
			
			$DB->insert_record('local_check_puss_notification', $insertdata, $returnid=true, $bulk=false);
			
			}
		}
//print_object($arr_deviceId);
	
 // $serverKey = 'AAAAJv9DEKA:APA91bGZdAki8i4bCVFtqD65j73dB2AJyKh0EOgShospx11BKrIudMh9PcxH8ZdE6HHjZyXtXSwriwfnialol6nHxxrm62Cm_6Qtncx4C81YPEAHAjOVJdxmNG5sT2o05rtbM6--IDDc';
  $serverKey = SERVER_KEY;

$arr_messageInfo['subject'] = " Course Completion Alert";
$arr_messageInfo['messageBody'] = " You have Completed ".$courseid->fullname. " course  !";
$notification = array('title' => $arr_messageInfo['subject'] , 'body' => $arr_messageInfo['messageBody'], 'sound' => 'default'); //, 'badge' => '1'

				if(count($arr_deviceId)) {
					$arrayToSend = array('registration_ids' => $arr_deviceId, 'notification' => $notification, 'priority' => 'high');//for multiple device

					$json = json_encode($arrayToSend);

					$headers = array();
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Authorization: key='. $serverKey;

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
					curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
					
					//curl_setopt($ch, CURLOPT_VERBOSE, 0);
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					//curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to 
					//Send the request
					$response = curl_exec($ch);
					//Close request
					if ($response === FALSE) {
					die('FCM Send Error: ' . curl_error($ch));
					}
					curl_close($ch);
				}
	
}	
}


function enrolled_notification(){
	
	global $DB;    

	$url = "https://fcm.googleapis.com/fcm/send";
    
	$coursedata = get_courses();
	
	foreach($coursedata as $courseid){
	
	 $tokensql = "SELECT DISTINCT a.id,a.userid,b.device_token,c.courseid 
	FROM {user_enrolments} as a join {user} AS b ON a.userid=b.id join {enrol} as c on c.id=a.enrolid
	where  b.device_token != 'null' and b.device_token != '' and c.courseid=". $courseid->id ." and a.userid NOT IN (SELECT CONCAT(userid) FROM {local_check_puss_notification} WHERE courseid = ".$courseid->id. " and msgid = 3 )";
		$getuser = $DB->get_records_sql($tokensql,null);
		
		
		
		$arr_deviceId = array();
		
		foreach($getuser as $user){
			//var_dump($user->device_token);
			if($user->device_token != 'null' && $user->device_token != ''){
				
			$arr_deviceId[] = $user->device_token;
			
			$insertdata = new stdclass;
			$insertdata->userid = $user->userid;
			$insertdata->courseid = $user->courseid;
			$insertdata->msgid = 3;
			$insertdata->timecreated = time();
			
			$DB->insert_record('local_check_puss_notification', $insertdata, $returnid=true, $bulk=false);
			
			}
		}
//print_object($arr_deviceId);
	
 // $serverKey = 'AAAAJv9DEKA:APA91bGZdAki8i4bCVFtqD65j73dB2AJyKh0EOgShospx11BKrIudMh9PcxH8ZdE6HHjZyXtXSwriwfnialol6nHxxrm62Cm_6Qtncx4C81YPEAHAjOVJdxmNG5sT2o05rtbM6--IDDc';
  $serverKey = SERVER_KEY;
$arr_messageInfo['subject'] = "New Course Alert";
$arr_messageInfo['messageBody'] = $courseid->fullname. " course has been assigned to you." ;
$notification = array('title' => $arr_messageInfo['subject'] , 'body' => $arr_messageInfo['messageBody'], 'sound' => 'default'); //, 'badge' => '1'

				if(count($arr_deviceId)) {
					$arrayToSend = array('registration_ids' => $arr_deviceId, 'notification' => $notification, 'priority' => 'high');//for multiple device

					$json = json_encode($arrayToSend);

					$headers = array();
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Authorization: key='. $serverKey;

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
					curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
					
					//curl_setopt($ch, CURLOPT_VERBOSE, 0);
					//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					//curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to 
					//Send the request
					$response = curl_exec($ch);
					//Close request
					if ($response === FALSE) {
					die('FCM Send Error: ' . curl_error($ch));
					}
					curl_close($ch);
				}
	
}	
}
/*******************  Push Notification Function ******************************/



function get_all_badges($userid){
	
	global $DB;
	
	$data = $DB->get_records('local_badges',null);
	
	return $data;
	
}

function get_scrom_maxscore($moduleid){
	
	global $DB;
	
	$sql = "SELECT b.id,c.maxgrade FROM {modules} as a join {course_modules} as b on a.id = b.module join {scorm} as c on c.id = b.instance where a.id = 19 and b.id = {$moduleid}";
	
	$data = $DB->get_record_sql($sql,null);
	
	return $data->maxgrade;
}

function get_user_resourse_score($userid){
	
	global $DB,$CFG;
	require_once($CFG->dirroot . '/course/lib.php');
    if(!empty($course)){
        if (!file_exists($CFG->dirroot . '/course/format/' . $course->format . '/lib.php')) {
            throw new moodle_exception('cannotgetcoursecontents', 'webservice', '', null,
                                        get_string('courseformatnotfound', 'error', $course->format));
        } else {
            require_once($CFG->dirroot . '/course/format/' . $course->format . '/lib.php');
        }
    }
	
			
			
	 $sql = "SELECT c.* FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE ue.userid = " . $userid;

    $coursedata = $DB->get_records_sql($sql, null);
	
	
	$allmod = array();
	$baseurl = 'webservice/pluginfile.php';
	$module = array();
	$allscore = array();
	foreach($coursedata as $course){
		
		$defaltdata = $DB->get_record('local_resourcescore',array('courseid'=>$course->id));
		
		$data = get_array_of_activities($course->id);
		
		$score = 0;
			foreach($data AS $modData){
	
				if($modData->mod != 'scorm'){
					if($modData->mod == 'resource'){
				$cm = new stdClass;
				$cm->id = $modData->cm;
				$cm->instance = $modData->id;
				$cm->modname = $modData->mod;
				require_once($CFG->dirroot . '/mod/' . $modData->mod . '/lib.php');
                            $getcontentfunction = $modData->mod.'_export_contents';
                            if (function_exists($getcontentfunction)) {
                                $contents = $getcontentfunction($cm, $baseurl);
                              
                                foreach ($contents as $content) {
                                    // Check repository file (only main file).
                                 
									if($cm->modname != 'scorm'){
									 $module['type'] = $content['type'];
                                    $module['filename'] = $content['filename'];
                                    $module['filepath'] = $content['filepath'];
                                    $module['filesize'] = $content['filesize'];
                                    $module['fileurl'] = $content['fileurl'];
                                    $module['timecreated'] = $content['timecreated'];
                                    $module['timemodified'] = $content['timemodified'];
                                    $module['sortorder'] = $content['sortorder'];
                                    $module['userid'] = $content['userid'];
                                    $module['author'] = $content['author'];
                                    $module['license'] = $content['license'];
                                    $module['mimetype'] = $content['mimetype'];
									
									//print_object($defaltdata);									
                                    if($content['mimetype'] == 'application/pdf')
                                    {
										 if(is_module_complete($cm->id,$userid) == 1)
											{
												$score += $defaltdata->pdf;
												
											}else{
												   $score += 0;
												   
											}
                                       
                                    }elseif($content['mimetype'] == "video/mp4")
                                    {
                                        if(is_module_complete($cm->id,$userid) == 1)
											{
												$score += $defaltdata->video;
												
											}else{
												   $score += 0;
												  
											}
                                      
                                    }elseif($content['mimetype'] == "audio/mp3")
                                    {
                                        if(is_module_complete($cm->id,$userid) == 1)
											{
												$score += $defaltdata->audio;
												
											}else{
												   $score += 0;
												  
											}
                                       
                                    }
									
                                }
									
                                }

                               
                            }	
									
									
									
									
					}elseif($modData->mod == 'url'){
						
						 if(is_module_complete( $modData->cm,$userid) == 1)
											{
												$score += $defaltdata->url;
												
											}else{
												   $score += 0;
												  
											}
						
					}elseif($modData->mod == 'page'){
						
						 if(is_module_complete( $modData->cm,$userid) == 1)
											{
												$score += $defaltdata->page;
												
											}else{
												   $score += 0;
												  
											}
						
					}
					 
							
							
				}
			}
		$allscore[] = $score;	
	}
	$finalscore = array_sum($allscore);
	
	return $finalscore;
	
}

function game_score($userid){
	
	
	$url = "https://infiniterunner2.firebaseio.com/Data/$userid.json";
				
				$data =  file_get_contents($url);
				if($data != null){
				$arrayData = json_decode($data);
				
				return $arrayData->user_score;
				
				}else{
					return 0;
				}
}

function get_user_profile_data($userid){
	
				global $DB;
				
				$sql = "SELECT uf.shortname,ud.data 
			FROM {user_info_data} ud 
			JOIN {user_info_field} uf ON uf.id = ud.fieldid
			WHERE ud.userid = $userid ";
			$params = array('userid' =>  $userid, 'fieldname' => 'degination');

			$fieldvalue = $DB->get_records_sql($sql, null);

			return $fieldvalue;
}

function user_profile_score($userid){
	
	global $DB,$USER,$PAGE;
	
	$sql = "SELECT u.id,gg.timemodified,u.username,u.firstname,u.lastname,u.email,sum(gg.finalgrade) as 'finalgrade' ,gi.courseid FROM {grade_grades} as gg JOIN {grade_items} as gi ON gi.id=gg.itemid JOIN {user} as u ON u.id=gg.userid  WHERE gi.itemtype='course' AND gg.finalgrade IS NOT NULL  AND gg.userid = $userid AND gi.courseid IN (SELECT CONCAT(c.id) AS courseid FROM {course} as c JOIN {course_categories} as cc on c.category=cc.id JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE cc.idnumber LIKE '%el%' and ue.userid = $userid) GROUP BY gg.userid ORDER BY finalgrade DESC";
	 
    $grade_detail = $DB->get_record_sql($sql,NULL);
	// echo json_encode($grade_detail);die;
	$finalgrade = $grade_detail->finalgrade + get_user_resourse_score($userid);
	return  round($finalgrade);	
	
}

function get_rank($userid){
	
	global $DB;
	
	$getRanksql = "SELECT COUNT(*) AS rank FROM {local_user_leaderboard} WHERE finalscore>=(SELECT finalscore FROM {local_user_leaderboard} WHERE userid=".$userid .")";
	
	$Rank = $DB->get_record_sql($getRanksql, null);
	//print_object($Rank);die($userid);
	if($Rank){
		
		return $Rank->rank;
	}else{
		return 0;
	}
}

function final_score($userid){
	
	global $DB;
	
	$data = $DB->get_record('local_user_leaderboard',array('userid'=>$userid));
	
	if($data){
		return $data->finalscore;
	}else{
		return 0;
	}
}

function all_leaderboard($userid){
	
	global $DB,$PAGE;
	
	$userid_or_ids = get_user_department($userid);
	$str_users = empty(implode(",",$userid_or_ids))?0:implode(",",$userid_or_ids);
	
	 $sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN (" .$str_users. ") AND finalscore != 0 ORDER BY finalscore DESC , updatetime DESC LIMIT 10";
	
	$alldata = $DB->get_records_sql($sql,null);
	
	$rank = 1;
	foreach($alldata as $data){
		
		$user = $DB->get_record('user',array('id'=>$data->userid));
					$userpictures = new user_picture($user);
                    $userpictures->size = 1; // Size f1.
                    $profileimageurls = $userpictures->get_url($PAGE);
	
		
		$data->id = $rank;
		$data->rank = $rank;
		$data->userimage = $profileimageurls->out(false);
		$data->dep = get_user_department_name($userid); 
		$data->finalgrade = $data->finalscore; 
		$data->firstname = $user->firstname;
		$data->lastname = $user->lastname;
		$data->email = $user->email;
		
		$rank++;
	}
	return $alldata;
	
}