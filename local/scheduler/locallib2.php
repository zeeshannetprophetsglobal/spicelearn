<?php 
use core_completion\progress;

function scheduler_data_report($format,$startdate,$enddate,$department,$subject,$quizdata,$progressdata,$userdata,$cate_data,$departments){
	$downloadData = get_scheduler_data_test($startdate,$enddate,$department,$subject,$quizdata,$progressdata,$userdata,$cate_data,$departments);
	
	ob_clean();
	if ($format) {
		$downloadlogs = $downloadData;

		$fields = ['userid', 'username', 'fullname', 'email', 'course_id', 'course_name', 'category_id', 'category_name','category_path', 'enrollment_date','course_startdate','course_end_date', 'completion_date', 'course_status', 'progress', 'grade', 'quiz_score', 'quiz_score_max','rank', 'department', 'zone', 'l1', 'l2'];



		$filename = clean_filename('scheduler_report');
		$downloadusers = new ArrayObject($downloadlogs);
		$iterator = $downloadusers->getIterator();

		\core\dataformat::download_data($filename, $format, $fields, $iterator, function($data) {

			$finaldata = array();         
			$finaldata['userid'] = $data->userid; 
			$finaldata['username'] = $data->username; 
			$finaldata['fullname'] = $data->fullname; 
			$finaldata['email'] = $data->email; 
			$finaldata['course_id'] = $data->course_id; 
			$finaldata['course_name'] = $data->coursename; 
			$finaldata['category_id'] = $data->category; 
			$finaldata['category_name'] = $data->category_name; 
			$finaldata['category_path'] = $data->category_path; 
			$finaldata['enrollment_date'] = $data->enrollment_date; 
			$finaldata['course_startdate'] = $data->startdate; 
			$finaldata['course_end_date'] = $data->enddate; 
			$finaldata['completion_date'] = $data->completion_date; 
			$finaldata['course_status'] = $data->course_status; 
			$finaldata['progress'] = $data->progress;  
			$finaldata['grade'] = $data->grade; 
			$finaldata['quiz_score'] = $data->quiz_score; 
			$finaldata['quiz_score_max'] = $data->quiz_score_max; 
			$finaldata['rank'] = $data->rank1; 
			$finaldata['department'] = $data->department; 
			$finaldata['zone'] = $data->zone; 
			$finaldata['l1'] = $data->l1; 
			$finaldata['l2'] = $data->l2; 

			return $finaldata;
		});

		exit;
	}
}


function get_scheduler_data_test($startdate,$enddate,$department,$subject,$quizdata,$progressdata,$userdata,$cate_data,$departments){
	global $DB;

	$subjectfilter = '';
	if ($subject) {
		$subjectfilter = ' AND cc.id='.$subject;
	}elseif ($department) {
		$subjectfilter = ' AND cc.path LIKE "%'.$department.'%"';
	}

	if(!empty($startdate) && !empty($enddate)){
		$subjectfilter .= ' AND c.startdate BETWEEN '.$startdate.' AND '.$enddate;
	}
	$sql = "SELECT DISTINCT ue.id,u.id as userid,u.username,CONCAT(u.firstname,' ',u.lastname) as fullname,u.email, c.id as course_id, c.fullname as 'coursename',c.startdate,c.enddate,c.category,cc.name as category_name,ue.timecreated as enrollment_date,
	ccom.timecompleted as completion_date,
	CASE
	WHEN (ccom.timecompleted is not null) THEN 'completed'
	WHEN (ccom.timecompleted is null AND ulast.timeaccess is not null) THEN 'inprogress'
	WHEN (ulast.timeaccess is null AND ccom.timecompleted is null) THEN 'notstarted'
	ELSE 'null'
	END AS course_status,
	gg.finalgrade as grade
	FROM {course} AS c
	JOIN {course_categories} as cc ON c.category = cc.id
	JOIN {context} AS ctx ON c.id = ctx.instanceid
	JOIN {role_assignments} AS ra ON ra.contextid = ctx.id
	JOIN {user} AS u ON u.id=ra.userid 
	JOIN {enrol} as e ON e.courseid=c.id
	JOIN {user_enrolments} as ue ON ue.userid=u.id AND e.id=ue.enrolid
	LEFT JOIN {course_completions} as ccom ON ccom.userid=u.id AND ccom.course=c.id
	LEFT JOIN {user_lastaccess} as ulast ON ulast.userid=u.id AND ulast.courseid=c.id
	LEFT JOIN {grade_items} as gi ON gi.courseid=c.id AND gi.itemtype='course'
	LEFT JOIN {grade_grades} as gg ON gg.itemid=gi.id AND gg.finalgrade AND gg.userid=u.id
	LEFT JOIN {user_info_data} as uid ON uid.userid=u.id
	LEFT JOIN {user_info_field} as uif ON uif.id=uid.fieldid
	where c.visible=1 and cc.visible=1 AND ra.roleid=5 ".$subjectfilter;
	
	$data = $DB->get_records_sql($sql,null);
	foreach ($data as $key => $value) {

		$finalgrade = '';
		$rawgrademax = '';
		if ($quizdata[$value->userid][$value->course_id]) {
			$finalgrade = $quizdata[$value->userid][$value->course_id]['finalgrade'];
			$rawgrademax = $quizdata[$value->userid][$value->course_id]['rawgrademax'];
		}
		
		$value->quiz_score = $finalgrade;
		$value->quiz_score_max = $rawgrademax;

		$course_progress = '';
		if ($progressdata[$value->userid][$value->course_id]) {
			$course_progress = $progressdata[$value->userid][$value->course_id]['course_progress'];
		}
		$value->progress = $course_progress;
		

		if ($userdata[$value->userid]['designation']['data'] != NULL) {
			$designation = $userdata[$value->userid]['designation']['data'];
		}
		if ($userdata[$value->userid]['zone']['data'] != NULL) {
			$zone = $userdata[$value->userid]['zone']['data'];
		}
		if ($userdata[$value->userid]['L1']['data'] != NULL) {
			$L1 = $userdata[$value->userid]['L1']['data'];
		}
		if ($userdata[$value->userid]['l2']['data'] != NULL) {
			$l2 = $userdata[$value->userid]['l2']['data'];
		}

		
		// echo "<pre>";print_r($departments);die;
		if ($departments[$value->userid]->name != NULL) {
			$department = $departments[$value->userid]->name;
		}

		$value->rank1 = $designation;
		$value->department = $department;
		$value->zone = $zone;
		$value->l1 = $L1;
		$value->l2 = $l2;

		$value->category_path = $cate_data[$value->category]->category_path;

		$value->enrollment_date = ($value->enrollment_date)?date('d/m/Y',$value->enrollment_date):'';
		$value->startdate = ($value->startdate)?date('d/m/Y',$value->startdate):'';
		$value->enddate = ($value->enddate)?date('d/m/Y',$value->enddate):'';
		$value->completion_date = ($value->completion_date)?date('d/m/Y',$value->completion_date):'';

	// echo "<pre>"; print_r($value);die;
		$data[$key] = $value;
	}
	return $data;

}

function local_get_quizdata(){
	global $DB;
	$quiz_sql = 'SELECT g.id,g.userid,gi.courseid,g.finalgrade,g.rawgrademax 
	FROM mdl_grade_grades g 
	JOIN mdl_grade_items gi ON g.itemid = gi.id 
	WHERE gi.itemtype = "mod" and gi.itemmodule = "quiz" and gi.itemname like "%final%"';
	$quizdata = $DB->get_records_sql($quiz_sql);
	$user_course_array = [];
	foreach ($quizdata as $value) {
		$user_course_array[$value->userid][$value->courseid] = ['finalgrade'=>$value->finalgrade,'rawgrademax'=>$value->rawgrademax];
	}
    // echo "<pre> ";print_r($user_course_array);die;
	return $user_course_array;
}

function get_progressdata(){
	global $DB;
	$sql = "SELECT ra.id,u.id AS userid,
	c.id AS courseid,
	(
		(100 / (SELECT COUNT(*) FROM mdl_course_modules cm WHERE cm.course = c.id) ) * 
		(SELECT COUNT(cmc.id) FROM mdl_course_modules_completion cmc
			LEFT JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
			WHERE cmc.userid = u.id AND cm.course = c.id)
		)AS course_progress
	FROM mdl_user u
	JOIN mdl_user_enrolments ue ON ue.userid=u.id
	JOIN mdl_enrol e ON e.id=ue.enrolid
	JOIN mdl_course c ON c.id = e.courseid
	JOIN mdl_context AS ctx ON ctx.instanceid = c.id
	JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id
	JOIN mdl_role AS r ON r.id = e.roleid
	WHERE ra.userid=u.id
	AND ctx.instanceid=c.id
	AND ra.roleid='5'
	AND c.visible='1' GROUP BY u.id,c.id";
	$progressdata = $DB->get_records_sql($sql);
	$user_course_array = [];
	foreach ($progressdata as $value) {
		$user_course_array[$value->userid][$value->courseid] = ['course_progress'=>$value->course_progress];
	}
    // echo "<pre> ";print_r($progressdata);die;
	return $user_course_array;
}

function local_get_userdata(){
	global $DB;
	$sql = "SELECT uid.id,u.id as userid,uif.shortname,uid.data FROM mdl_user as u JOIN mdl_user_info_data as uid ON u.id=uid.userid JOIN mdl_user_info_field as uif ON uid.fieldid=uif.id";
	$userdata = $DB->get_records_sql($sql);
	$user_field_array = [];
	foreach ($userdata as $value) {
		$user_field_array[$value->userid][$value->shortname] = ['data'=>$value->data];
	}
	return $user_field_array;
}

function local_get_departments(){
	global $DB;
	$sql = "SELECT cm.userid,c.name FROM mdl_cohort as c JOIN mdl_cohort_members as cm ON c.id=cm.cohortid";
	$departments = $DB->get_records_sql($sql);
	return $departments;
}

function local_get_cate_data(){
	global $DB;
	$sql = 'SELECT cc.id FROM  {course_categories} as cc WHERE cc.visible=1';
	$categoryData = $DB->get_records_sql($sql,null);
	$cate_data = array();
	foreach ($categoryData as $key => $value) {
		$value->category_path = get_category_parent_name($value->id);
		$cate_data[$value->id] = $value;
	}
	return $cate_data;
}
function get_category_parent_name($category){

	global $DB;
	$sql = 'SELECT * FROM  {course_categories} WHERE id = "'.$category.'"';
	$categoryData = $DB->get_records_sql($sql,null);
	$name = $categoryData[$category]->name;
	if($categoryData[$category]->depth < 3){
		return $name;
	}else{
		return $name = get_category_parent_name($categoryData[$category]->parent) . '/'.$name;
	}
}

// function local_is_course_complete($course_id,$userid){

// 	global $DB, $CFG;
// 	require_once("{$CFG->libdir}/completionlib.php");
// 	$course_object = $DB->get_record('course', array('id'=>$course_id));
// 	$cinfo = new completion_info($course_object);
// 	$iscomplete = $cinfo->is_course_complete($userid);
// 	if(!$iscomplete){
// 		$iscomplete = 0;
// 	}
// 	return $iscomplete;
// }

// function get_scheduler_data($startdate,$enddate,$department,$subject){
// 	global $DB;
// 	$subjectfilter = '';
// 	if ($subject) {
// 		$subjectfilter = ' AND ls.category_id='.$subject;
// 	}elseif ($department) {
// 		$subjectfilter = ' AND ls.category_path LIKE "%'.$department.'%"';
// 	}
// 	if(!empty($startdate) && !empty($enddate)){
// 		$sql = 'SELECT ls.* FROM {local_scheduler} as ls JOIN {course} as c ON c.id=ls.course_id WHERE ls.userid is not Null and c.startdate BETWEEN '.$startdate.' AND '.$enddate.$subjectfilter;
// 	}else{
// 		$sql = 'SELECT * FROM {local_scheduler} as ls WHERE ls.userid is not Null'.$subjectfilter;
// 	}
// 	// echo $sql;die;
// 	$data = $DB->get_records_sql($sql,null);

// 	return $data;
// }