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
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_coursereports
 */

define('COURSEREPORTS_PAGE_LIMIT',15);
define('USERWISE_PAGE_LIMIT',25);

function local_coursereports_extend_navigation(navigation_node $nav) {
	
	global $CFG, $PAGE, $COURSE,$USER;

    // Check if users is logged in to extend navigation.
	
	if (!isloggedin()) {
		return;
	}				   
	$icon = new pix_icon('i/report', '');
	
	if(is_siteadmin()){							
		
		// $node2 = $nav->add(
		// 	get_string('pluginname','local_coursereports'),
		// 	new moodle_url($CFG->wwwroot . '/local/coursereports/index.php'),
		// 	navigation_node::TYPE_CUSTOM,
		// 	'coursereports',
		// 	'coursereports',
		// 	$icon
		// );

		// $node1 = $nav->add(
		// 	get_string('userwisereport','local_coursereports'),
		// 	new moodle_url($CFG->wwwroot . '/local/coursereports/userwise.php'),
		// 	navigation_node::TYPE_CUSTOM,
		// 	'userwisereport',
		// 	'userwisereport',
		// 	$icon
		// );
		$node = $nav->add(
			get_string('leaderboard','local_coursereports'),
			new moodle_url($CFG->wwwroot . '/local/coursereports/leaderboard.php'),
			navigation_node::TYPE_CUSTOM,
			'leaderboard',
			'leaderboard',
			$icon
		);
		
		// $node2->showinflatnavigation = true; 
		// $node1->showinflatnavigation = true;  
		$node->showinflatnavigation = true; 
	}
}

function local_coursereports_get_coursedata($page){
	
	global $DB;
	
	if($page){
		$page = $page*COURSEREPORTS_PAGE_LIMIT;
	}
	$CourseData = $DB->get_records('course', array(), $sort='', $fields='*', $page, COURSEREPORTS_PAGE_LIMIT);
	
	return $CourseData;
	
}

function local_coursereports_get_course_complete_count($courseid){
	
	global $DB,$CFG;
	require_once($CFG->libdir.'/completionlib.php');
	$context = context_course::instance($courseid);
	$course_object = get_course($courseid);
	$AllEnrollUser =  get_enrolled_users($context, 'mod/assignment:submit',$groupid = 0, $userfields = 'u.id',);
	$notcomplete = 0;
	$complete = 0;
	foreach($AllEnrollUser as $user){
		
		$cinfo = new completion_info($course_object);
		
		$iscomplete = $cinfo->is_course_complete($user->id);
		
		if(!$iscomplete){
			$notcomplete++;
		}else{
			$complete++;
		}
	}
	$count = new stdClass;
	$count->notcomplete = $notcomplete;
	$count->complete = $complete;
	
	return $count;
}


function get_learderboard_data($page,$cohort,$search){

	global $DB;
	
	if($page){
		$page = $page*COURSEREPORTS_PAGE_LIMIT;
	}

	if(!empty($search['name']) && !empty($search['email']) && !empty($cohort)){

		$sql = "SELECT * FROM {local_user_leaderboard} WHERE departmentid = ".$cohort." AND userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
		$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
		
		if(empty($learderboardData)){

			$sql = "SELECT * FROM {local_user_leaderboard} WHERE departmentid = ".$cohort." AND userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
			$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
			
		}
		

	}elseif(!empty($search['name']) && !empty($search['email'])){

		$sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
			$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
			echo $sql;
			if(empty($learderboardData)){

				$sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
					$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
					
				}
				

			}elseif(!empty($cohort) && !empty($search['email'])){

				$sql = "SELECT * FROM {local_user_leaderboard} WHERE departmentid = ".$cohort." AND userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
				$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
				
			}elseif(!empty($cohort) && !empty($search['name'])){

				$sql = "SELECT * FROM {local_user_leaderboard} WHERE departmentid = ".$cohort." AND userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
				$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
				

			}elseif( !empty($search['email']) && empty($cohort) && empty($search['name']) ){

				
				$sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE email LIKE '%".$search['email']."%')";
					$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
					

				}elseif(!empty($search['name']) && empty($cohort) && empty($search['email'])){

					$sql = "SELECT * FROM {local_user_leaderboard} WHERE userid IN(SELECT CONCAT(id) AS 'userids'  FROM {user} WHERE firstname LIKE '%".$search['name']."%')";
						$learderboardData = $DB->get_records_sql($sql, null, $page, COURSEREPORTS_PAGE_LIMIT);
						
					}elseif($cohort && empty($search['email']) && empty($search['name'])){
						$learderboardData = $DB->get_records('local_user_leaderboard',array('departmentid'=>$cohort), $sort='finalscore DESC', $fields='*', $page, COURSEREPORTS_PAGE_LIMIT);
						
					}else{
						$learderboardData = $DB->get_records('local_user_leaderboard',null, $sort='finalscore DESC', $fields='*', $page, COURSEREPORTS_PAGE_LIMIT);
					}
					
					return $learderboardData;
				}


				function get_enroll_course_count_by_userid($userid){

					global $DB;

					$sql = "SELECT DISTINCT c.id FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as ue ON ue.enrolid = en.id WHERE ue.userid =".$userid;
					
					$data = $DB->get_records_sql($sql,null);

					return count($data);
				}

				function course_status_by_userid($userid){

					global $DB,$CFG;
					require_once($CFG->libdir.'/completionlib.php');
					$enrolcoursesql = "SELECT  DISTINCT c.id as 'courseid' ,  ue.timestart as timestart  FROM {course} as c JOIN {course_categories} as cc on c.category=cc.id JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as  ue ON ue.enrolid = en.id WHERE cc.idnumber LIKE '%el%' and ue.userid =".$userid;
	// echo $enrolcoursesql;die;
					$courseids = $DB->get_recordset_sql($enrolcoursesql,null);
					
					$completed = 0;
					$inprogress = 0;
					$notcomplete = 0;
					foreach($courseids as $id){

						$course_object = get_course($id->courseid);

						$cinfo = new completion_info($course_object);
						
						$iscomplete = $cinfo->is_course_complete($userid);

						if($iscomplete){
							$completed++;
						}elseif($id->timestart == 0){
							$notcomplete++;
						}else{
							$inprogress++;
						}	

						
					}



					$staatus = new stdClass;
					$staatus->completed = $completed;
					$staatus->inprogress = $inprogress;
					$staatus->notstart = $notcomplete;

					return $staatus;

				}


				function get_userwise_data($page,$cohort,$search){

					global $DB;

					if($page){
						$page = $page*USERWISE_PAGE_LIMIT;
					}
					if(!empty($cohort) && !empty($search['name'])){

						$sql = "SELECT u.* FROM {user} as u JOIN {cohort_members} AS cm on u.id= cm.userid WHERE cm.cohortid = ".$cohort." AND u.id NOT IN (1,2) AND u.firstname LIKE '%".$search['name']."%' ORDER BY u.firstname ASC LIMIT ".$page.",".USERWISE_PAGE_LIMIT;
						$userdata = $DB->get_recordset_sql($sql,null);

					}elseif(!empty($search['name']) && empty($cohort)){

						$sql = "SELECT *FROM {user}  WHERE  id NOT IN (1,2) AND firstname LIKE '%".$search['name']."%' ORDER BY firstname ASC LIMIT ".$page.",".USERWISE_PAGE_LIMIT;
						$userdata = $DB->get_recordset_sql($sql,null);
					}elseif($cohort){
						$sql = "SELECT u.* FROM {user} as u JOIN {cohort_members} AS cm on u.id= cm.userid WHERE cm.cohortid = ".$cohort." AND u.id NOT IN (1,2) ORDER BY u.firstname ASC LIMIT ".$page.",".USERWISE_PAGE_LIMIT;
						$userdata = $DB->get_recordset_sql($sql,null);
					}else{
						$usersql = "SELECT * FROM {user} where id NOT IN (1,2) ORDER BY firstname ASC LIMIT ".$page.",".USERWISE_PAGE_LIMIT;
						$userdata = $DB->get_recordset_sql($usersql,null);
					}

					return $userdata;
				}

				function get_userreport_data($page,$userid,$staatus){

					global $DB;

					if($page){
						$page = $page*USERWISE_PAGE_LIMIT;
					}
					


					$enrolcoursesql = "SELECT  DISTINCT c.id as 'courseid' ,c.fullname,  ue.timestart as 'timestart'  FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as  ue ON ue.enrolid = en.id WHERE ue.userid = ". $userid ;
					
	//$coursedata = $DB->get_records_sql($enrolcoursesql,null, $page, USERWISE_PAGE_LIMIT);
					$coursedata = $DB->get_recordset_sql($enrolcoursesql,null);
	//print_object($coursedata);
					$coursedataarray = array();

					foreach($coursedata as $course){

						$course_object = get_course($course->courseid);

						$cinfo = new completion_info($course_object);
						
						$iscomplete = $cinfo->is_course_complete($userid);

						$statusObject = new stdClass;
						
						if($iscomplete){
							$statusObject->completed = $course;
						}elseif($course->timestart == 0){
							$statusObject->notstart = $course;
						}else{
							$statusObject->inprogress = $course;
						}	

						$coursedataarray[$course->courseid] = $statusObject;
					}

					if($staatus){
						return $coursedataarray;
					}else{
						return $coursedata;
					}
					
					
				}

				function user_grade_data($courseid,$uid){

					global $CFG;
					require_once($CFG->dirroot.'/grade/querylib.php');
					require_once($CFG->dirroot.'/lib/grade/grade_item.php');
					require_once($CFG->dirroot.'/lib/grade/grade_grade.php');
					require_once($CFG->dirroot.'/lib/gradelib.php');
					$gradedata = grade_get_course_grades($courseid,$uid); 

					$grade = (int)$gradedata->grades[$uid]->str_grade=='-'?'0.00':$gradedata->grades[$uid]->str_grade;
					
					return $grade;
				}


				function user_course_status($courseid,$uid,$starttime){

					$course_object = get_course($courseid);

					$cinfo = new completion_info($course_object);
					
					$iscomplete = $cinfo->is_course_complete($uid);

					if($iscomplete){
						return 'Completed';
					}elseif($starttime == 0){
						return 'Not start';
					}else{
						return 'inprogress';
					}	

				}

				function local_cousrewisereprt_get_user_department($userid)
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

				function local_cousrewisereprt_get_user_department_name($userid)
				{
					global $DB;
					
					$sql = "SELECT c.name,c.idnumber FROM {cohort} AS c JOIN {cohort_members} AS cm ON c.id = cm.cohortid WHERE cm.userid = $userid";
					
					$depart = $DB->get_records_sql($sql,null);
					
					if($depart ){ 
						foreach($depart as $d){
							$name = $d->name;
						}
						
					}else{
						$name = 'None';
					}
					return $name;
				}

				function course_complete_count($userid){

					global $DB;
					
					$enrolcoursesql = "SELECT  DISTINCT c.id as 'courseid' ,  ue.timestart as timestart  FROM {course} as c JOIN {enrol} as en ON en.courseid = c.id JOIN {user_enrolments} as  ue ON ue.enrolid = en.id WHERE ue.userid =".$userid;
					
					$courseids = $DB->get_recordset_sql($enrolcoursesql,null);
					
					$completed = 0;
					$inprogress = 0;
					$notcomplete = 0;
					foreach($courseids as $id){

						$course_object = get_course($id->courseid);

						$cinfo = new completion_info($course_object);
						
						$iscomplete = $cinfo->is_course_complete($userid);

						if($iscomplete){
							$completed++;
						}else{
							$inprogress++;
						}	

					}

					return $completed;
				}
				function download_report_coursewise($format){


					global $DB;
					
					$requesteddata = get_courses();
					$downloadData = array();
					foreach($requesteddata as $coursedata){

						$context = context_course::instance($coursedata->id);

						$is_courseComplete = local_coursereports_get_course_complete_count($coursedata->id);

						$CourseObject = new stdClass;
						$CourseObject->id = $coursedata->id;
						$CourseObject->name = $coursedata->fullname;
						$CourseObject->complete = $is_courseComplete->complete;
						if(count_enrolled_users($context, $withcapability = '', $groupid = 0)){
							$completedpercentage = $is_courseComplete->complete/count_enrolled_users($context, $withcapability = '', $groupid = 0)*100;
						}else{
							$completedpercentage = 0.00;
						}
						$CourseObject->percentage = number_format((float)$completedpercentage, 2, '.', '');
						$CourseObject->inprogress = $is_courseComplete->notcomplete;
						$CourseObject->totalenrolled = count_enrolled_users($context, $withcapability = '', $groupid = 0);
						$downloadData[] = $CourseObject;
					}
					
					if ($format) {
						$downloadlogs = $downloadData;

						$fields = [
							get_string('coursename','local_coursereports'),
							get_string('completed','local_coursereports'),
							get_string('completedpercentage','local_coursereports'),
							get_string('userinprogress','local_coursereports'),
							get_string('totalenrollment','local_coursereports') 
						];
						
						

						$filename = clean_filename('coursewise_report');
						$downloadusers = new ArrayObject($downloadlogs);
						$iterator = $downloadusers->getIterator();

						\core\dataformat::download_data($filename, $format, $fields, $iterator, function($data) {
							
							

							
							$finaldata['coursename'] = $data->name;		
							$finaldata['completed'] = $data->complete;
							$finaldata['department'] = $data->percentage;
							$finaldata['totalcourse'] = $data->inprogress;
							$finaldata['totalscore'] = $data->totalenrolled;
							
							
							


							return $finaldata;
						});

						exit;
					}



				}

				function leaderboad_downloadData(){

					global $DB;
					$requesteddata = $DB->get_records('local_user_leaderboard',array());
	//print_object($requesteddata);die;
					$downloadData = array();
					foreach($requesteddata as $udata){
						$userdata = core_user::get_user($udata->userid);
						
						$CourseObject = new stdClass;
						$CourseObject->id = $udata->userid;
						$CourseObject->name = $userdata->firstname.' '.$userdata->lastname;
						$CourseObject->email = $userdata->email;
						$CourseObject->department = local_cousrewisereprt_get_user_department_name($udata->userid);
						
						$CourseObject->totalcourse = get_enroll_course_count_by_userid($udata->userid);
						$CourseObject->totalscore = $udata->finalscore;
						$CourseStatus = course_status_by_userid($udata->userid);
						$CourseObject->complete = $CourseStatus->completed;
						$CourseObject->inprogress = $CourseStatus->inprogress;
						$CourseObject->notstart = $CourseStatus->notstart;
						$CourseObject->gamescore = $udata->gamescore;
						$CourseObject->lmsscore = $udata->lmsscore;
						$downloadData[] = $CourseObject;
					}

					return $downloadData;
				}

				function download_report_leaderboaed($format){

					$downloadData = leaderboad_downloadData();
  // die(233);
					ob_clean();
					if ($format) {
						$downloadlogs = $downloadData;

						$fields = [
							get_string('name','local_coursereports'),
							get_string('email','local_coursereports'),
							get_string('department','local_coursereports'),
							get_string('totalcourse','local_coursereports'),
							get_string('totalscore','local_coursereports'),
							get_string('completecourse','local_coursereports'),
							get_string('inprogress','local_coursereports'),
							get_string('notstart','local_coursereports'),
							get_string('gamescore','local_coursereports'),
							get_string('lmsscore','local_coursereports')
							
						];
						
						

						$filename = clean_filename('leaderboard_report');
						$downloadusers = new ArrayObject($downloadlogs);
						$iterator = $downloadusers->getIterator();

						\core\dataformat::download_data($filename, $format, $fields, $iterator, function($data) {
							
							

							$finaldata = array();           
							
							$finaldata['name'] = $data->name;
							$finaldata['email'] = $data->email;
							$finaldata['department'] = $data->department;
							$finaldata['totalcourse'] = $data->totalcourse;
							$finaldata['totalscore'] = $data->totalscore;
							
							$finaldata['completecourse'] = $data->complete;
							$finaldata['inprogress'] = $data->inprogress;
							$finaldata['notstart'] = $data->notstart;
							$finaldata['gamescore'] =  $data->gamescore;
							$finaldata['lmsscore'] = $data->lmsscore;
							


							return $finaldata;
						});

						exit;
					}

				}

				function download_report_userwise($format){



					global $DB;
					$requesteddata = $DB->get_records('user',array());

					$downloadData = array();
					foreach($requesteddata as $userdata){
						
						$CourseObject = new stdClass;
						$CourseObject->id = $userdata->id;
						$CourseObject->name = $userdata->firstname.' '.$userdata->lastname;
						$CourseObject->department = local_cousrewisereprt_get_user_department_name($userdata->id);
						
						$CourseObject->totalcourse = get_enroll_course_count_by_userid($userdata->id);
						$CourseStatus = course_status_by_userid($userdata->id);
						$CourseObject->complete = $CourseStatus->completed;
						$CourseObject->inprogress = $CourseStatus->inprogress;
						$CourseObject->notstart = $CourseStatus->notstart;
						$downloadData[] = $CourseObject;
					}


  // die(233);
					if ($format) {
						$downloadlogs = $downloadData;

						$fields = [
							get_string('name','local_coursereports'),
							get_string('department','local_coursereports'),
							get_string('totalcourse','local_coursereports'),
							get_string('completecourse','local_coursereports'),
							get_string('inprogress','local_coursereports'),
							get_string('notstart','local_coursereports'),
							
							
						];
						
						

						$filename = clean_filename('userwise_report');
						$downloadusers = new ArrayObject($downloadlogs);
						$iterator = $downloadusers->getIterator();

						\core\dataformat::download_data($filename, $format, $fields, $iterator, function($data) {
							
							$finaldata = array();
							
							$finaldata['name'] = $data->name;		
							$finaldata['department'] = $data->department;
							$finaldata['totalcourse'] = $data->totalcourse;
							$finaldata['completecourse'] = $data->complete;
							$finaldata['inprogress'] = $data->inprogress;
							$finaldata['notstart'] = $data->notstart;
							


							return $finaldata;
						});

						exit;
					}
				}


				function course_progress_count2($userid, $roleid)
{

    global $DB;

    $sql = "SELECT 
    DISTINCT 
    COUNT(DISTINCT c.id) AS totalcourse,
    COUNT(DISTINCT IF(ccom.timecompleted is not null,c.id,NULL)) AS completed,
    COUNT(DISTINCT IF(ccom.timecompleted is null AND ulast.timeaccess is not null,c.id,NULL)) AS inprogress,
    COUNT(DISTINCT IF(ulast.timeaccess is null AND ccom.timecompleted is null,c.id,NULL)) AS notstarted
    FROM mdl_user as u
    JOIN mdl_enrol as e 
    JOIN mdl_user_enrolments as ue ON e.id=ue.enrolid 
    JOIN mdl_role_assignments ra ON ra.userid = u.id
    JOIN mdl_role r ON r.id = ra.roleid AND ra.roleid = $roleid 
    JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel=50
    JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id
    LEFT JOIN mdl_course_completions as ccom ON ccom.course=c.id AND ccom.userid=u.id
    LEFT JOIN mdl_user_lastaccess as ulast ON ulast.userid=u.id AND ulast.courseid=c.id
    WHERE c.visible=1 AND e.status =0 AND u.suspended =0 AND u.deleted =0 AND u.id=$userid";

    $coursestatus = $DB->get_record_sql($sql);
    $enrollcoursecount = $coursestatus->totalcourse;
    $complete = $coursestatus->completed;
    $inprogrss = $coursestatus->inprogress;
    $notstarted = $coursestatus->notstarted;

    return ['complete' => $complete, 'inprogress' => $inprogrss, 'notstarted' => $notstarted, 'totalcourse' => $enrollcoursecount];
}