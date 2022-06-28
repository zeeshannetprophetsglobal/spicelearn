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

//require_once($CFG->libdir . '/coursecatlib.php');


use core_completion\progress;

require_once($CFG->libdir . "/enrollib.php");
require_once($CFG->dirroot . '/cohort/lib.php');
define('AUTHOR', 9);
define('TEACHER', 3);
define('NONTEACHER', 4);
define('STUDENT', 5);
define('PERPAGE_LIMIT', 10);
define('ZONE', 4);
define('RANK', 3);
define('L1', 6);
define('L2', 7);

function get_profile_field($userid)
{

    global $DB;

    // $sql = 'SELECT ud.* FROM {user_info_field} AS uf JOIN {user_info_data} AS ud ON uf.id = ud.fieldid where ud.userid='.$userid;

    $sql = 'SELECT uif.shortname, uid.data FROM {user_info_data} as uid  JOIN {user_info_field} as uif ON uid.fieldid = uif.id where  uid.userid =' . $userid;

    $data = $DB->get_records_sql($sql, null);

    //  print_object($data['designation']->data);
    return $data;
}

function author_record($page, $ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }
    if (!empty($ecn)) {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }

    // print_object($sql);die;
    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);

    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }
    return $Alldata;
}


function author_record_downloaddata($ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);


    if (!empty($ecn)) {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }

    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return $Alldata;
}


function count_author_record($ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);


    if (!empty($ecn)) {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }

    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return count($Alldata);
}
function ceo_author_record($page, $dpt, $ecn)
{

    global $DB, $USER;

    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }

    $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }
    //  echo $sql;
    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }
    //print_object($Alldata);die;
    return $Alldata;
}


function ceo_author_record_datadownload($dpt, $ecn)
{

    global $DB, $USER;


    $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . AUTHOR . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }
    //  echo $sql;
    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return $Alldata;
}


function instructor_record($page, $ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }

    if (!empty($ecn)) {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }
    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);

    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }

    return $Alldata;
}



function instructor_record_downloaddata($ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);


    if (!empty($ecn)) {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {

        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }

    $Alldata = $DB->get_records_sql($sql, null);


    return $Alldata;
}


function count_instructor_record($ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);


    if (!empty($ecn)) {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }
    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return count($Alldata);
}

function ceo_instructor_record($page, $dpt, $ecn)
{

    global $DB, $USER;


    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }

    $sql = 'SELECT DISTINCT  u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }

    // echo $sql;
    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }

    return $Alldata;
}


function ceo_instructor_record_downloaddata($dpt, $ecn)
{

    global $DB;



    $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }

    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return $Alldata;
}


function count_ceo_instructor_record($dpt, $ecn)
{

    global $DB, $USER;


    $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . TEACHER . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }

    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return count($Alldata);
}

function non_instructor_record($page, $ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }
    if (!empty($ecn)) {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }
    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }
    //print_object($Alldata);die;
    return $Alldata;
}


function non_instructor_record_downloaddata($ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    if (!empty($ecn)) {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }
    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return $Alldata;
}


function count_non_instructor_record($ecn)
{

    global $DB, $USER;


    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    if (!empty($ecn)) {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id . ' AND u.username LIKE "%' . $ecn . '%"';
    } else {
        $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . ' AND cm.cohortid =' . $cohortdata[0]->id;
    }
    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return count($Alldata);
}

function ceo_non_instructor_record($page, $dpt, $ecn)
{

    global $DB, $USER;

    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }

    $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN {cohort_members} AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }

    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }
    //print_object($Alldata);die;
    return $Alldata;
}


function ceo_non_instructor_record_downloadata($dpt, $ecn)
{

    global $DB;


    $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }

    $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);

    //print_object($Alldata);die;
    return $Alldata;
}


function count_ceo_non_instructor_record($dpt, $ecn)
{

    global $DB, $USER;



    $sql = 'SELECT u.id,u.username,u.firstname,u.lastname,u.email,ch.name as "dpt_name"  FROM {user} as u  JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN {cohort} as ch ON ch.id = cm.cohortid JOIN {role_assignments} AS ra ON cm.userid = ra.userid WHERE ra.roleid = ' . NONTEACHER . '';


    $table_filter['dpt'] = ['where_and' => ' AND ch.id =' . $dpt];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE "%' . $ecn . '%"'];



    //  print_object($table_filter);
    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }
        }
    }

    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return count($Alldata);
}

function author_course_count($userid, $startdate, $enddate)
{

    global $DB;

    if (!empty($startdate) && !empty($enddate)) {

        $sql = 'SELECT c.id,c.fullname FROM {course} as c JOIN {local_course_creator} as l ON c.id = l.courseid where l.userid = ' . $userid . ' AND c.visible = 1 AND c.timecreated BETWEEN ' . $startdate . ' AND ' . $enddate;
    } else {

        $sql = 'SELECT c.id,c.fullname FROM {course} as c JOIN {local_course_creator} as l ON c.id = l.courseid where c.visible = 1 AND l.userid = ' . $userid . '';
    }

    // echo $sql;die;
    $coursedata = $DB->get_records_sql($sql, null);

    return count($coursedata);
}

function AuthorCourseList($userid, $startdate, $enddate)
{

    global $DB;

    $fitler = '';
    if (!empty($startdate) and !empty($enddate)) {
        $fitler = ' AND c.timecreated BETWEEN ' . $startdate . ' AND ' . $enddate;
    }

    $sql = 'SELECT c.id,c.fullname FROM {course} as c JOIN {local_course_creator} as l ON c.id = l.courseid where c.visible = 1 and l.userid = ' . $userid . $fitler;

    $coursedata = $DB->get_records_sql($sql, null);

    return $coursedata;
}

function CourseImageLink($courseid)
{

    global $CFG;
    $url = '';
    require_once($CFG->libdir . '/filelib.php');

    $context = context_course::instance($courseid);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);

    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $filename = str_replace(' ', '%20', $f->get_filename());
            $path = '/' . $f->get_contextid() . '/' . $f->get_component() . '/' . $f->get_filearea()  . '/' . $filename;
            $url = $CFG->wwwroot . '/pluginfile.php' . $path;
            //$url = moodle_url::make_pluginfile_url( $f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    // $course = get_course($courseid);
    // $url = \core_course\external\course_summary_exporter::get_course_image($course);
    // print_object($url); die;
    return $url;
}

function get_enrolled_course($userid, $roleid, $startdate, $enddate)
{

    global $DB;

    if (!empty($startdate) && !empty($enddate)) {

        $sql = "SELECT DISTINCT c.*,u.id AS userid,cc.name  FROM {user} u JOIN {user_enrolments} ue ON ue.userid = u.id JOIN {enrol} e ON e.id = ue.enrolid JOIN {role_assignments} ra ON ra.userid = u.id JOIN {context} ct ON ct.id = ra.contextid AND ct.contextlevel = 50 JOIN {course} c ON c.id = ct.instanceid AND e.courseid = c.id JOIN {course_categories} as cc ON c.category = cc.id JOIN {role} r ON r.id = ra.roleid AND ra.roleid = $roleid WHERE c.visible = 1 and e.status = 0 AND u.suspended = 0 AND u.deleted = 0 AND (ue.timeend = 0 OR ue.timeend > UNIX_TIMESTAMP(NOW())) AND ue.status = 0 AND ra.userid = $userid AND c.startdate BETWEEN  $startdate AND $enddate";
    } else {

        $sql = "SELECT DISTINCT c.*,u.id AS userid,cc.name  FROM {user} u JOIN {user_enrolments} ue ON ue.userid = u.id JOIN {enrol} e ON e.id = ue.enrolid JOIN {role_assignments} ra ON ra.userid = u.id JOIN {context} ct ON ct.id = ra.contextid AND ct.contextlevel = 50 JOIN {course} c ON c.id = ct.instanceid AND e.courseid = c.id JOIN {course_categories} as cc ON c.category = cc.id JOIN {role} r ON r.id = ra.roleid AND ra.roleid = $roleid WHERE  c.visible = 1 and e.status = 0 AND u.suspended = 0 AND u.deleted = 0 AND (ue.timeend = 0 OR ue.timeend > UNIX_TIMESTAMP(NOW())) AND ue.status = 0 AND ra.userid = $userid";
    }
    // echo $sql;die;
    $coursedata = $DB->get_records_sql($sql, null);

    return $coursedata;
}



function department_user_detail($page = 0, $ecn, $base, $zone, $rank, $l1, $l2, $downloaddata = null)
{

    global $USER, $DB;

    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);
    if (empty($page)) {
        $page = 0;
    } else {
        $page = $page * PERPAGE_LIMIT;
    }

    $sql = 'SELECT DISTINCT u.id,u.username,u.firstname,u.lastname,u.email,u.city FROM mdl_user as u JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN mdl_role_assignments AS ra ON cm.userid = ra.userid __JOIN__  WHERE ra.roleid = ' . STUDENT . ' AND cm.cohortid =' . $cohortdata[0]->id;

    $table_filter['base'] = ['where_and' => ' AND u.city LIKE  "%' . $base . '%"'];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE  "%' . $ecn . '%"'];

    $table_filter['zone'] = ['join' => ' JOIN mdl_user_info_data as uif1 ON u.id = uif1.userid', 'where_and' => ' AND (uif1.fieldid = ' . ZONE . ' AND uif1.data LIKE  "%' . $zone . '%")'];
    $table_filter['rank'] = ['join' => ' JOIN mdl_user_info_data as uif2 ON u.id = uif2.userid', 'where_and' => ' AND (uif2.fieldid = ' . RANK . ' AND uif2.data LIKE  "%' . $rank . '%")'];
    $table_filter['l1'] = ['join' => ' JOIN mdl_user_info_data as uif3 ON u.id = uif3.userid', 'where_and' => ' AND (uif3.fieldid = ' . L1 . ' AND uif3.data LIKE  "%' . $l1 . '%")'];
    $table_filter['l2'] = ['join' => ' JOIN mdl_user_info_data as uif4 ON u.id = uif4.userid', 'where_and' => ' AND (uif4.fieldid = ' . L2 . ' AND uif4.data LIKE  "%' . $l2 . '%")'];

    $JOIN__user_info_data = false;
    $join_sql = '';

    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }

            if (isset($table_filter[$key]['join']) && !$JOIN__user_info_data) {

                if (($key == 'dpt' || $key == 'base' || $key == 'ecn') && ($key != 'zone' && $key != 'rank' &&

                    $key != 'l1' && $key != 'l2')) {

                    $join_sql = $table_filter[$key]['join'];
            } else {
                $join_sql .= $table_filter[$key]['join'];
            }
                // $JOIN__user_info_data  = true;
        }
    }
}
$sql = str_replace('__JOIN__', $join_sql, $sql);

    // print_object($sql);die;
$Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);

foreach ($Alldata as $index) {
    $page++;
    $index->index = $page;
}

return $Alldata;
}

function department_user_detail_downloaddata($ecn, $base, $zone, $rank, $l1, $l2)
{

    global $USER, $DB;

    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    $filter = ' AND cm.cohortid =' . $cohortdata[0]->id;
    if($base){
        $filter .= ' AND u.city LIKE  "%' . $base . '%"';
    }
    if($ecn){
        $filter .= ' AND u.username LIKE  "%' . $ecn . '%"';
    }

    $jointable = '';
    if($zone){
        $jointable .= ' JOIN mdl_user_info_data as uif1 ON u.id = uif1.userid ';
        $filter .= ' AND uif1.fieldid = ' . ZONE . ' AND uif1.data LIKE  "%' . $zone . '%"';
    }

    if($rank){
        $jointable .= ' JOIN mdl_user_info_data as uif2 ON u.id = uif2.userid ';
        $filter .= ' AND uif2.fieldid = ' . RANK . ' AND uif2.data LIKE  "%' . $rank . '%"';
    }

    if($l1){
        $jointable .= ' JOIN mdl_user_info_data as uif3 ON u.id = uif3.userid ';
        $filter .= ' AND uif3.fieldid = ' . L1 . ' AND uif3.data LIKE  "%' . $l1 . '%"';
    }

    if($l2){
        $jointable .= ' JOIN mdl_user_info_data as uif4 ON u.id = uif4.userid ';
        $filter .= ' AND uif4.fieldid = ' . L2 . ' AND uif4.data LIKE  "%' . $l2 . '%"';
    }

    
    $sql = 'SELECT u.id ,u.username ,u.firstname ,u.lastname ,u.email, u.city,
            COUNT(DISTINCT c.id) AS totalcourse,
            COUNT(DISTINCT IF(ccom.timecompleted is not null,c.id,NULL)) AS completed,
            COUNT(DISTINCT IF(ccom.timecompleted is null AND ulast.timeaccess is not null,c.id,NULL)) AS inprogress,
            COUNT(DISTINCT IF(ulast.timeaccess is null AND ccom.timecompleted is null,c.id,NULL)) AS notstarted
            FROM mdl_user as u
            JOIN mdl_cohort_members AS cm ON u.id = cm.userid
            JOIN mdl_role_assignments AS ra ON cm.userid = ra.userid
            JOIN mdl_role r ON r.id = ra.roleid AND ra.roleid = 5 
            JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel=50
            JOIN mdl_course c ON c.id = ct.instanceid
            JOIN mdl_enrol as e ON e.courseid = c.id
            JOIN mdl_user_enrolments as ue ON e.id=ue.enrolid AND ue.userid = u.id
            LEFT JOIN mdl_course_completions as ccom ON ccom.course=c.id AND ccom.userid=u.id
            LEFT JOIN mdl_user_lastaccess as ulast ON ulast.userid=u.id AND ulast.courseid=c.id
            '.$jointable.'
            WHERE c.visible=1 AND e.status =0 AND u.suspended =0 AND u.deleted =0 '.$filter.'
            group by u.id';
    // echo $sql;die;
    $Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
    return $Alldata;
}


function department_user_count($ecn, $base, $zone, $rank, $l1, $l2)
{

    global $USER, $DB;

    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    $sql = 'SELECT DISTINCT u.id FROM mdl_user as u JOIN mdl_cohort_members AS cm ON u.id = cm.userid JOIN mdl_role_assignments AS ra ON cm.userid = ra.userid __JOIN__  WHERE ra.roleid = ' . STUDENT . ' AND cm.cohortid =' . $cohortdata[0]->id;

    $table_filter['base'] = ['where_and' => ' AND u.city LIKE  "%' . $base . '%"'];
    $table_filter['ecn'] = ['where_and' => ' AND u.username LIKE  "%' . $ecn . '%"'];
    $table_filter['zone'] = ['join' => ' JOIN mdl_user_info_data as uif1 ON u.id = uif1.userid', 'where_and' => ' AND (uif1.fieldid = ' . ZONE . ' AND uif1.data LIKE  "%' . $zone . '%")'];
    $table_filter['rank'] = ['join' => ' JOIN mdl_user_info_data as uif2 ON u.id = uif2.userid', 'where_and' => ' AND (uif2.fieldid = ' . RANK . ' AND uif2.data LIKE  "%' . $rank . '%")'];
    $table_filter['l1'] = ['join' => ' JOIN mdl_user_info_data as uif3 ON u.id = uif3.userid', 'where_and' => ' AND (uif3.fieldid = ' . L1 . ' AND uif3.data LIKE  "%' . $l1 . '%")'];
    $table_filter['l2'] = ['join' => ' JOIN mdl_user_info_data as uif4 ON u.id = uif4.userid', 'where_and' => ' AND (uif4.fieldid = ' . L2 . ' AND uif4.data LIKE  "%' . $l2 . '%")'];


    $JOIN__user_info_data = false;
    $join_sql = '';

    foreach ($table_filter as $key => $val) {
        if (isset($$key) && !empty($$key)) {
            $key . '=' . $$key;
            $sql = str_replace('__where_and__', $table_filter[$key]['where_and'], $sql);
            if (isset($table_filter[$key]['where_and'])) {
                $sql .= $table_filter[$key]['where_and'];
            }

            if (isset($table_filter[$key]['join']) && !$JOIN__user_info_data) {
                if (($key == 'dpt' || $key == 'base' || $key == 'ecn') && ($key != 'zone' && $key != 'rank' &&

                    $key != 'l1' && $key != 'l2')) {

                    $join_sql = $table_filter[$key]['join'];
            } else {
                $join_sql .= $table_filter[$key]['join'];
            }
                //$JOIN__user_info_data  = true;

        }
    }
}
$sql = str_replace('__JOIN__', $join_sql, $sql);

$Alldata = $DB->get_records_sql($sql, null);

    //print_object($Alldata);die;
return count($Alldata);
}

function user_course_progress($courseid, $userid)
{

    global $DB;

    $courseobject = get_course($courseid);

    return progress::get_course_progress_percentage($courseobject, $userid);
}

function sitereport_user_course_status($courseid, $userid)
{

    global $DB;
    $is_course_complete = local_is_course_complete($courseid, $userid);
    $isnotstarted = $DB->get_record_sql("SELECT id FROM {user_lastaccess} as ul WHERE courseid=$courseid and userid = $userid");

    $status = 'Inprogress';
    if ($is_course_complete) {

        $status = 'Completed';
    } elseif ($isnotstarted) {

        $status = 'Inprogress';
    } else {

        $status = 'notstarted';
    }

    return $status;
}

function local_is_course_complete($course_id, $userid)
{

    global $DB, $CFG;
    require_once("{$CFG->libdir}/completionlib.php");
    $course_object = $DB->get_record('course', array('id' => $course_id));
    $cinfo = new completion_info($course_object);
    $iscomplete = $cinfo->is_course_complete($userid);
    if (!$iscomplete) {
        $iscomplete = 0;
    }
    return $iscomplete;
}

function course_progress_count($userid, $roleid)
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

function get_department_course($page = 0, $type, $iltsubject, $elsubject, $timetype, $c_startdate, $c_enddate, $m_startdate, $m_enddate, $dowloaddata = false)
{

    global $USER, $DB;

    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);
    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='" . $cohortdata[0]->idnumber . "'");
        if ($parent_cate) {
            $dpt = $parent_cate->id;
        }
    }

    $filter = ' AND c.visible = 1 AND cc.visible=1 ';

    if ($dpt) {
        $filter .= ' AND cc.path LIKE  "%/' . $dpt . '%"';
    }
    if ($type == 1) {
        $filter .= ' AND cc.idnumber LIKE  "%ilt%"';
        if ($iltsubject) {
            $filter .= ' AND cc.id =' . $iltsubject;
        }
    } elseif ($type == 2) {
        $filter .= ' AND cc.idnumber LIKE  "%el%"';
        if ($elsubject) {
            $filter .= ' AND cc.id =' . $elsubject;
        }
    }

    if ($timetype == 1) {
        $filter .= ' AND c.startdate BETWEEN ' . $c_startdate . ' AND ' . $c_enddate;
    } elseif ($timetype == 2) {
        $filter .= ' AND c.timemodified BETWEEN ' . $m_startdate . ' AND ' . $m_enddate;
    }

    $sql .= "SELECT 
DISTINCT c.id,c.fullname,cc.path, 
COUNT(DISTINCT IF(ccom.timecompleted is not null,u.id,NULL)) AS completed, 
COUNT(DISTINCT IF(ccom.timecompleted is null AND ulast.timeaccess is not null,u.id,NULL)) AS inprogress, 
COUNT(DISTINCT IF(ulast.timeaccess is null AND ccom.timecompleted is null,u.id,NULL)) AS notstarted 
FROM 
mdl_course as c 
JOIN mdl_course_categories as cc ON c.category = cc.id 
JOIN mdl_context ct ON c.id = ct.instanceid
JOIN mdl_role_assignments ra ON ct.id=ra.contextid  
JOIN mdl_role r ON r.id = ra.roleid AND r.shortname='student'
JOIN mdl_user as u ON u.id=ra.userid
LEFT JOIN mdl_course_completions as ccom ON ccom.course=c.id AND ccom.userid=u.id 
LEFT JOIN mdl_user_lastaccess as ulast ON ulast.userid=u.id AND ulast.courseid=c.id 
    WHERE  1=1 ".$filter." GROUP BY c.id ORDER BY c.id asc " ;


    if ($page == -1) {
        $coursedata = $DB->get_records_sql($sql, null);
        $coursedata = count($coursedata);
    
    }else if($page == -2){

        $coursedata = $DB->get_records_sql($sql, null);
    
    }else{
    
        if (empty($page)) {
            $page = 0;
        } else {
            $page = $page * PERPAGE_LIMIT;
        }
        $coursedata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    
    }
    
    foreach ($coursedata as $index) {
        $page++;
        $index->index = $page;
    }
    return $coursedata;
}


function get_course_user_status($courseid)
{

    global $DB;
    $coursecentext = context_course::instance($courseid);
    // get_enrolled_users($coursecentext, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0);

    $intructorCount = count_enrolled_users($coursecentext, 'mod/quiz:manage');

    $enrol_users = get_enrolled_users($coursecentext, 'mod/assignment:submit');

    $complete = 0;
    $inprogress = 0;
    $notstarted = 0;
    foreach ($enrol_users as $user) {

        $is_course_complete = local_is_course_complete($courseid, $user->id);

        $isnotstarted = $DB->get_record_sql("SELECT id FROM {user_lastaccess} as ul WHERE courseid=$courseid and userid = $user->id");
    
        if ($is_course_complete) {
            $complete++;
        } else if ($isnotstarted) {
            $inprogress++;
        } else {
            $notstarted++;
        }
    }

    $report = new stdClass;
    $report->instructor = $intructorCount;
    $report->complete = $complete;
    $report->inprogress = $inprogress;
    $report->notstarted = $notstarted;

    return $report;
}

function get_type($path)
{

    global $DB;

    $explodpath = explode("/", $path);

    $typedata =  $DB->get_record('course_categories', array('id' => $explodpath[2]));

    return $typedata->name;
}

function get_subject($path)
{

    global $DB;
    $explodpath = explode("/", $path);

    $subjectdata = array_slice($explodpath, 3);

    $count = count($subjectdata);
    $FullNameCategory = '';
    foreach ($subjectdata as $data) {

        $category =  $DB->get_record('course_categories', array('id' => $data));

        if ($count > 1) {
            $FullNameCategory .= $category->name . '/';
        } else {
            $FullNameCategory .= $category->name;
        }
    }
    // print_object($subjectdata);die;
    return $FullNameCategory;
}

function download_trainee_report($format, $ecn, $base, $zone, $rank, $l1, $l2,$userprofiledata)
{

    $downloadData = department_user_detail_downloaddata($ecn, $base, $zone, $rank, $l1, $l2);
    //  print_object($downloadData);


    $UserDataArray = array();
    foreach ($downloadData as $userdata) {

        // $profiedata = get_profile_field($userdata->id);
        //$statusCount = course_progress_count($userdata->id, 5);
        $designation = '';
        $zone = '';
        $L1 = '';
        $l2 = '';
        if ($userprofiledata[$userdata->id]['designation']['data'] != NULL) {
			$designation = $userprofiledata[$userdata->id]['designation']['data'];
		}
		if ($userprofiledata[$userdata->id]['zone']['data'] != NULL) {
			$zone = $userprofiledata[$userdata->id]['zone']['data'];
		}
		if ($userprofiledata[$userdata->id]['L1']['data'] != NULL) {
			$L1 = $userprofiledata[$userdata->id]['L1']['data'];
		}
		if ($userprofiledata[$userdata->id]['l2']['data'] != NULL) {
			$l2 = $userprofiledata[$userdata->id]['l2']['data'];
		}
        
        $dataObject = new stdClass;
        $dataObject->ecn = $userdata->username;
        $dataObject->name = $userdata->firstname.' '.$userdata->lastname;
        $dataObject->email = $userdata->email;
        $dataObject->base = $userdata->city;
        $dataObject->zone = $zone;
        $dataObject->rank = $designation;
        $dataObject->l1 = $L1;
        $dataObject->l2 = $l2;
        $dataObject->totalcourseenroll = $userdata->totalcourse;
        $dataObject->complete = $userdata->completed;
        $dataObject->inprogress = $userdata->inprogress;
        $dataObject->notstarted = $userdata->notstarted;
        $UserDataArray[] = $dataObject;
    }
    //print_object($UserDataArray);die;
    ob_clean();
    if ($format) {
        $downloadlogs = $UserDataArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('base', 'local_sitereport'),
            get_string('zone', 'local_sitereport'),
            get_string('rank', 'local_sitereport'),
            get_string('l1', 'local_sitereport'),
            get_string('l2', 'local_sitereport'),
            get_string('totalcourseenroll', 'local_sitereport'),
            get_string('complete', 'local_sitereport'),
            get_string('inprogress', 'local_sitereport'),
            get_string('notstarted', 'local_sitereport')

        ];

        $filename = clean_filename('trainee_report');
        $downloadusers = new ArrayObject($downloadlogs);
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {

            $finaldata = array();
            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            $finaldata['base'] = $data->base;
            $finaldata['zone'] = $data->zone;
            $finaldata['rank'] = $data->rank;
            $finaldata['l1'] = $data->l1;
            $finaldata['l2'] = $data->l2;
            $finaldata['totalcourseenroll'] = $data->totalcourseenroll;
            $finaldata['complete'] =  $data->complete;
            $finaldata['inprogress'] = $data->inprogress;
            $finaldata['notstarted'] = $data->notstarted;

            return $finaldata;
        });

        exit;
    }
}

function download_ceo_trainee_report($format, $dpt, $ecn, $base, $zone, $rank, $l1, $l2,$userprofiledata)
{

    $downloadData = ceo_department_user_detail(-2,$dpt, $ecn, $base, $zone, $rank, $l1, $l2);

    $UserDataArray = array();
    foreach ($downloadData as $userdata) {

        $designation = '';
        $zone = '';
        $L1 = '';
        $l2 = '';
        if ($userprofiledata[$userdata->id]['designation']['data'] != NULL) {
            $designation = $userprofiledata[$userdata->id]['designation']['data'];
        }
        if ($userprofiledata[$userdata->id]['zone']['data'] != NULL) {
            $zone = $userprofiledata[$userdata->id]['zone']['data'];
        }
        if ($userprofiledata[$userdata->id]['L1']['data'] != NULL) {
            $L1 = $userprofiledata[$userdata->id]['L1']['data'];
        }
        if ($userprofiledata[$userdata->id]['l2']['data'] != NULL) {
            $l2 = $userprofiledata[$userdata->id]['l2']['data'];
        }
        
        $dataObject = new stdClass;
        $dataObject->ecn = $userdata->username;
        $dataObject->name = $userdata->firstname.' '.$userdata->lastname;
        $dataObject->email = $userdata->email;
        $dataObject->departments = $userdata->dpt_name;
        $dataObject->base = $userdata->city;
        $dataObject->zone = $zone;
        $dataObject->rank = $designation;
        $dataObject->l1 = $L1;
        $dataObject->l2 = $l2;
        $dataObject->totalcourseenroll = $userdata->totalcourse;
        $dataObject->complete = $userdata->completed;
        $dataObject->inprogress = $userdata->inprogress;
        $dataObject->notstarted = $userdata->notstarted;
        $UserDataArray[] = $dataObject;
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $UserDataArray;
        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('departments', 'local_sitereport'),
            get_string('base', 'local_sitereport'),
            get_string('zone', 'local_sitereport'),
            get_string('rank', 'local_sitereport'),
            get_string('l1', 'local_sitereport'),
            get_string('l2', 'local_sitereport'),
            get_string('totalcourseenroll', 'local_sitereport'),
            get_string('complete', 'local_sitereport'),
            get_string('inprogress', 'local_sitereport'),
            get_string('notstarted', 'local_sitereport')

        ];

        $filename = clean_filename('trainee_report');
        $downloadusers = new ArrayObject($downloadlogs);
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {

            $finaldata = array();
            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            $finaldata['departments'] = $data->departments;
            $finaldata['base'] = $data->base;
            $finaldata['zone'] = $data->zone;
            $finaldata['rank'] = $data->rank;
            $finaldata['l1'] = $data->l1;
            $finaldata['l2'] = $data->l2;
            $finaldata['totalcourseenroll'] = $data->totalcourseenroll;
            $finaldata['complete'] =  $data->complete;
            $finaldata['inprogress'] = $data->inprogress;
            $finaldata['notstarted'] = $data->notstarted;
            return $finaldata;

        });
        exit;
    }
}


function download_course_report($format, $type, $iltsubject, $elsubject, $timetype, $c_startdate, $c_enddate, $m_startdate, $m_enddate){

    $downloadData =  get_department_course(-2,$type, $iltsubject, $elsubject, $timetype, $c_startdate, $c_enddate, $m_startdate, $m_enddate);
    
    $courseArray = array();
    foreach ($downloadData as $coursedata) {
        
        $totalinstructor = get_instructors($coursedata->id);
        $objectData = new stdClass;
        $objectData->course = $coursedata->fullname;
        $objectData->type = get_type($coursedata->path);
        $objectData->subject = get_subject($coursedata->path);
        $objectData->instructor = $totalinstructor;
        $objectData->completeuser =  $coursedata->completed;
        $objectData->inprogresseuser = $coursedata->inprogress;
        $objectData->notstarteduser = $coursedata->notstarted;
        $courseArray[] = $objectData;

    }
    ob_clean();
    if ($format) {
        $downloadlogs = $courseArray;
        $fields = [
            get_string('course'),
            get_string('type', 'local_sitereport'),
            get_string('subject', 'local_sitereport'),
            get_string('instructor', 'local_sitereport'),
            get_string('completeuser', 'local_sitereport'),
            get_string('inprogresseuser', 'local_sitereport'),
            get_string('notstarteduser', 'local_sitereport')
        ];

        $filename = clean_filename('Course_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();
        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {

            $finaldata = array();
            $finaldata['course'] = $data->course;
            $finaldata['type'] = $data->type;
            $finaldata['subject'] = $data->subject;
            $finaldata['instructor'] = $data->instructor;
            $finaldata['completeuser'] = $data->completeuser;
            $finaldata['inprogresseuser'] = $data->inprogresseuser;
            $finaldata['notstarteduser'] = $data->notstarteduser;



            return $finaldata;
        });

        exit;
    }
}


function ceo_download_course_report($format, $dpt, $type, $subject, $timetype, $c_startdate, $c_enddate, $m_startdate, $m_enddate)
{

    $downloadvar =  get_ceo_course(-2,$dpt, $type, $subject, $timetype, $c_startdate, $c_enddate, $m_startdate, $m_enddate);

    $courseArray = array();
    foreach ($downloadvar as $coursedata) {
        $totalinstructor = get_instructors($coursedata->id);
        $objectData = new stdClass;
        $objectData->course = $coursedata->fullname;
        $objectData->departments = get_course_dpt_name($coursedata->path);;
        $objectData->type = get_type($coursedata->path);
        $objectData->subject = get_subject($coursedata->path);
        $objectData->instructor = $totalinstructor;
        $objectData->completeuser =  $coursedata->completed;
        $objectData->inprogresseuser = $coursedata->inprogress;
        $objectData->notstarteduser = $coursedata->notstarted;

        $courseArray[] = $objectData;
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $courseArray;

        $fields = [
            get_string('course'),
            get_string('departments', 'local_sitereport'),
            get_string('type', 'local_sitereport'),
            get_string('subject', 'local_sitereport'),
            get_string('instructor', 'local_sitereport'),
            get_string('completeuser', 'local_sitereport'),
            get_string('inprogresseuser', 'local_sitereport'),
            get_string('notstarteduser', 'local_sitereport')

        ];



        $filename = clean_filename('Course_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['course'] = $data->course;
            $finaldata['departments'] = $data->departments;
            $finaldata['type'] = $data->type;
            $finaldata['subject'] = $data->subject;
            $finaldata['instructor'] = $data->instructor;
            $finaldata['completeuser'] = $data->completeuser;
            $finaldata['inprogresseuser'] = $data->inprogresseuser;
            $finaldata['notstarteduser'] = $data->notstarteduser;



            return $finaldata;
        });

        exit;
    }
}


function ceo_download_author_report($format, $dpt, $ecn, $startdate, $enddate)
{

    $downloadData = ceo_author_record_datadownload($dpt, $ecn); //get_department_course(0,$date,$type,$subject,true);
    // die(233);
    $authorArray = array();
    foreach ($downloadData as $userdata) {

        $designation =  get_profile_field($userdata->id);

        $objectData = new stdClass;
        $objectData->ecn = $userdata->username;
        $objectData->name = $userdata->firstname;
        $objectData->email = $userdata->email;
        $objectData->departments = $userdata->dpt_name;
        $objectData->designation = $designation['designation']->data;
        $objectData->totalcoursecreate = author_course_count($userdata->id, $startdate, $enddate);
        $authorArray[] = $objectData;
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $authorArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('departments', 'local_sitereport'),
            get_string('designation', 'local_sitereport'),
            get_string('totalcoursecreate', 'local_sitereport')

        ];



        $filename = clean_filename('author_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            $finaldata['departments'] = $data->departments;
            $finaldata['designation'] = $data->designation;
            $finaldata['totalcoursecreate'] = $data->totalcoursecreate;

            return $finaldata;
        });

        exit;
    }
}


function download_author_report($format, $ecn)
{

    $downloadData = author_record_downloaddata($ecn); //get_department_course(0,$date,$type,$subject,true);
    // die(233);
    $authorArray = array();
    foreach ($downloadData as $userdata) {

        $designation =  get_profile_field($userdata->id);

        $objectData = new stdClass;
        $objectData->ecn = $userdata->username;
        $objectData->name = $userdata->firstname;
        $objectData->email = $userdata->email;

        $objectData->designation = $designation['designation']->data;
        $objectData->totalcoursecreate = author_course_count($userdata->id, NULL, NULL);
        $authorArray[] = $objectData;
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $authorArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('designation', 'local_sitereport'),
            get_string('totalcoursecreate', 'local_sitereport')

        ];



        $filename = clean_filename('author_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            $finaldata['designation'] = $data->designation;
            $finaldata['totalcoursecreate'] = $data->totalcoursecreate;

            return $finaldata;
        });

        exit;
    }
}


function ceo_download_instructor_report($format, $dpt, $ecn, $startdate, $enddate)
{

    $downloadData = ceo_instructor_record_downloaddata($dpt, $ecn); //get_department_course(0,$date,$type,$subject,true);
    // die(233);
    $authorArray = array();
    foreach ($downloadData as $userdata) {

        $designation =  get_profile_field($userdata->id);

        $objectData = new stdClass;
        $objectData->ecn = $userdata->username;
        $objectData->name = $userdata->firstname;
        $objectData->email = $userdata->email;
        $objectData->departments = $userdata->dpt_name;
        $objectData->designation = $designation['designation']->data;
        $objectData->totalcourseenroll = count(get_enrolled_course($userdata->id, 3, $startdate, $enddate));
        $authorArray[] = $objectData;
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $authorArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('departments', 'local_sitereport'),
            get_string('designation', 'local_sitereport'),
            get_string('totalcourseenroll', 'local_sitereport')

        ];



        $filename = clean_filename('Instructor_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            $finaldata['departments'] = $data->departments;
            $finaldata['designation'] = $data->designation;
            $finaldata['totalcourseenroll'] = $data->totalcourseenroll;

            return $finaldata;
        });

        exit;
    }
}

function download_instructor_report($format, $ecn, $startdate, $enddate)
{

    $downloadData = instructor_record_downloaddata($ecn); //get_department_course(0,$date,$type,$subject,true);
    // die(233);
    $authorArray = array();
    foreach ($downloadData as $userdata) {

        $designation =  get_profile_field($userdata->id);

        $objectData = new stdClass;
        $objectData->ecn = $userdata->username;
        $objectData->name = $userdata->firstname;
        $objectData->email = $userdata->email;

        $objectData->designation = $designation['designation']->data;
        $objectData->totalcourseenroll = count(get_enrolled_course($userdata->id, 3, $startdate, $enddate));
        $authorArray[] = $objectData;
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $authorArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('designation', 'local_sitereport'),
            get_string('totalcourseenroll', 'local_sitereport')

        ];



        $filename = clean_filename('Instructor_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            // $finaldata['departments'] = $data->departments;
            $finaldata['designation'] = $data->designation;
            $finaldata['totalcourseenroll'] = $data->totalcourseenroll;
            return $finaldata;
        });

        exit;
    }
}


function ceo_download_non_instructor_report($format, $dpt, $ecn, $startdate, $enddate)
{

    $downloadData = ceo_non_instructor_record_downloadata($dpt, $ecn); //get_department_course(0,$date,$type,$subject,true);

    $authorArray = array();
    foreach ($downloadData as $userdata) {
        $coursecount = count(get_enrolled_course($userdata->id, 4, $startdate, $enddate));
        $designation =  get_profile_field($userdata->id);
        if ($coursecount) {
            $objectData = new stdClass;
            $objectData->ecn = $userdata->username;
            $objectData->name = $userdata->firstname;
            $objectData->email = $userdata->email;
            $objectData->departments = $userdata->dpt_name;
            $objectData->designation = $designation['designation']->data;
            $objectData->totalcourseenroll = count(get_enrolled_course($userdata->id, 4, $startdate, $enddate));
            $authorArray[] = $objectData;
        }
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $authorArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),
            get_string('departments', 'local_sitereport'),
            get_string('designation', 'local_sitereport'),
            get_string('totalcourseenroll', 'local_sitereport')

        ];



        $filename = clean_filename('Non_Instructor_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            $finaldata['departments'] = $data->departments;
            $finaldata['designation'] = $data->designation;
            $finaldata['totalcourseenroll'] = $data->totalcourseenroll;

            return $finaldata;
        });

        exit;
    }
}


function download_non_instructor_report($format, $ecn, $startdate, $enddate)
{

    $downloadData =  non_instructor_record_downloaddata($ecn); //get_department_course(0,$date,$type,$subject,true);
    // die(233);
    $authorArray = array();
    foreach ($downloadData as $userdata) {
        $coursecount = count(get_enrolled_course($userdata->id, 4, $startdate, $enddate));
        if ($coursecount) {
            $designation =  get_profile_field($userdata->id);

            $objectData = new stdClass;
            $objectData->ecn = $userdata->username;
            $objectData->name = $userdata->firstname;
            $objectData->email = $userdata->email;

            $objectData->designation = $designation['designation']->data;
            $objectData->totalcourseenroll = count(get_enrolled_course($userdata->id, 4, $startdate, $enddate));
            $authorArray[] = $objectData;
        }
    }
    ob_clean();
    if ($format) {
        $downloadlogs = $authorArray;

        $fields = [
            get_string('ecn', 'local_sitereport'),
            get_string('name', 'local_sitereport'),
            get_string('email'),

            get_string('designation', 'local_sitereport'),
            get_string('totalcourseenroll', 'local_sitereport')

        ];



        $filename = clean_filename('Non_Instructor_report');
        $downloadusers = new ArrayObject($downloadlogs);
        // print_object($downloadusers);die;
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $format, $fields, $iterator, function ($data) {



            $finaldata = array();

            $finaldata['ecn'] = $data->ecn;
            $finaldata['name'] = $data->name;
            $finaldata['email'] = $data->email;
            // $finaldata['departments'] = $data->departments;
            $finaldata['designation'] = $data->designation;
            $finaldata['totalcourseenroll'] = $data->totalcourseenroll;

            return $finaldata;
        });

        exit;
    }
}


function subject_option($type)
{

    global $USER, $DB;

    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);
    $categorys = array();
    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='" . $cohortdata[0]->idnumber . "'");
        if ($parent_cate) {
            $sql = 'SELECT * FROM {course_categories} WHERE  id != 23 AND idnumber IS NOT NULL AND idnumber LIKE "%' . $type . '%" AND  path LIKE "%' . $parent_cate->id . '%"';
            $categorys = $DB->get_records_sql($sql, null);
        }
    }


    // print_object($sql);die;

    return $categorys;
}

function eco_subject_option($type, $dpt = null)
{

    global $USER, $DB;

    $cohort =  cohort_get_user_cohorts($USER->id);
    $cohortdata = array_values($cohort);

    if ($cohortdata) {
        $parent_cate = $DB->get_record_sql("SELECT id FROM  {course_categories} WHERE idnumber='" . $cohortdata[0]->idnumber . "'");
        if ($parent_cate) {
            $sql = 'SELECT * FROM {course_categories} WHERE  id != ' . $parent_cate->id . ' AND idnumber IS NOT NULL AND idnumber LIKE "%' . $type . '%" AND  path LIKE "%' . $parent_cate->id . '%"';
        }
    }
    $categorys = $DB->get_records_sql($sql, null);

    // print_object($coursedata);die;
    return $categorys;
}


function ceo_department_user_detail($page = 0, $dpt, $ecn, $base, $zone, $rank, $l1, $l2, $downloaddata = null)
{
    global $USER, $DB;

    $filter = '';

    if($dpt){
        $filter .= ' AND cm.cohortid =' . $dpt;
    }
    if($base){
        $filter .= ' AND u.city LIKE  "%' . $base . '%"';
    }
    if($ecn){
        $filter .= ' AND u.username LIKE  "%' . $ecn . '%"';
    }

    $jointable = '';
    if($zone){
        $jointable .= ' JOIN mdl_user_info_data as uif1 ON u.id = uif1.userid ';
        $filter .= ' AND uif1.fieldid = ' . ZONE . ' AND uif1.data LIKE  "%' . $zone . '%"';
    }

    if($rank){
        $jointable .= ' JOIN mdl_user_info_data as uif2 ON u.id = uif2.userid ';
        $filter .= ' AND uif2.fieldid = ' . RANK . ' AND uif2.data LIKE  "%' . $rank . '%"';
    }

    if($l1){
        $jointable .= ' JOIN mdl_user_info_data as uif3 ON u.id = uif3.userid ';
        $filter .= ' AND uif3.fieldid = ' . L1 . ' AND uif3.data LIKE  "%' . $l1 . '%"';
    }

    if($l2){
        $jointable .= ' JOIN mdl_user_info_data as uif4 ON u.id = uif4.userid ';
        $filter .= ' AND uif4.fieldid = ' . L2 . ' AND uif4.data LIKE  "%' . $l2 . '%"';
    }

    $sql = 'SELECT u.id ,u.username ,u.firstname ,u.lastname ,u.email, u.city,ch.name as "dpt_name",
            COUNT(DISTINCT c.id) AS totalcourse,
            COUNT(DISTINCT IF(ccom.timecompleted is not null,c.id,NULL)) AS completed,
            COUNT(DISTINCT IF(ccom.timecompleted is null AND ulast.timeaccess is not null,c.id,NULL)) AS inprogress,
            COUNT(DISTINCT IF(ulast.timeaccess is null AND ccom.timecompleted is null,c.id,NULL)) AS notstarted
            FROM mdl_user as u
            JOIN mdl_cohort_members AS cm ON u.id = cm.userid
            JOIN mdl_cohort as ch ON ch.id = cm.cohortid
            JOIN mdl_role_assignments AS ra ON cm.userid = ra.userid
            JOIN mdl_role r ON r.id = ra.roleid AND ra.roleid = 5 
            JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel=50
            JOIN mdl_course c ON c.id = ct.instanceid
            JOIN mdl_enrol as e ON e.courseid = c.id
            JOIN mdl_user_enrolments as ue ON e.id=ue.enrolid AND ue.userid = u.id
            LEFT JOIN mdl_course_completions as ccom ON ccom.course=c.id AND ccom.userid=u.id
            LEFT JOIN mdl_user_lastaccess as ulast ON ulast.userid=u.id AND ulast.courseid=c.id
            '.$jointable.'
            WHERE c.visible=1 AND e.status =0 AND u.suspended =0 AND u.deleted =0 '.$filter.'
            group by u.id';

            // echo $sql;die;
    if ($page == -1) {
        $Alldata = $DB->get_records_sql($sql, null);
        $Alldata = count($Alldata);
    
    }else if($page == -2){

        $Alldata = $DB->get_records_sql($sql, null);
    
    }else{
    
        if (empty($page)) {
            $page = 0;
        } else {
            $page = $page * PERPAGE_LIMIT;
        }
        $Alldata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    
    }
    
    foreach ($Alldata as $index) {
        $page++;
        $index->index = $page;
    }
    return $Alldata;
}



function get_ceo_course($page = 0, $dpt, $type, $subject, $timetype, $c_startdate, $c_enddate, $m_startdate, $m_enddate)
{

    global $USER, $DB;

    $filter = ' AND c.visible = 1 AND cc.visible=1 ';
    if ($dpt) {
        $filter .= ' AND cc.path LIKE  "%/' . $dpt . '%"';
    }

    if ($type == 1) {
        $filter .= ' AND cc.idnumber LIKE  "%ilt%"';
    } elseif ($type == 2) {
        $filter .= ' AND cc.idnumber LIKE  "%el%"';
    }

    if ($subject) {
        $filter .= ' AND cc.id =' . $subject;
    }

    if ($timetype == 1) {
        $filter .= ' AND c.startdate BETWEEN ' . $c_startdate . ' AND ' . $c_enddate;
    } elseif ($timetype == 2) {
        $filter .= ' AND c.timemodified BETWEEN ' . $m_startdate . ' AND ' . $m_enddate;
    }

    $sql .= "SELECT 
DISTINCT c.id,c.fullname,cc.path, 
COUNT(DISTINCT IF(ccom.timecompleted is not null,u.id,NULL)) AS completed, 
COUNT(DISTINCT IF(ccom.timecompleted is null AND ulast.timeaccess is not null,u.id,NULL)) AS inprogress, 
COUNT(DISTINCT IF(ulast.timeaccess is null AND ccom.timecompleted is null,u.id,NULL)) AS notstarted 
FROM 
mdl_course as c 
JOIN mdl_course_categories as cc ON c.category = cc.id 
JOIN mdl_context ct ON c.id = ct.instanceid
JOIN mdl_role_assignments ra ON ct.id=ra.contextid  
JOIN mdl_role r ON r.id = ra.roleid AND r.shortname='student'
JOIN mdl_user as u ON u.id=ra.userid
LEFT JOIN mdl_course_completions as ccom ON ccom.course=c.id AND ccom.userid=u.id 
LEFT JOIN mdl_user_lastaccess as ulast ON ulast.userid=u.id AND ulast.courseid=c.id 
    WHERE 1=1 ".$filter." GROUP BY c.id ORDER BY c.id asc " ;

    if ($page == -1) {
        $coursedata = $DB->get_records_sql($sql, null);
        $coursedata = count($coursedata);
    
    }else if($page == -2){

        $coursedata = $DB->get_records_sql($sql, null);
    
    }else{
    
        if (empty($page)) {
            $page = 0;
        } else {
            $page = $page * PERPAGE_LIMIT;
        }
        $coursedata = $DB->get_records_sql($sql, null, $page, PERPAGE_LIMIT);
    
    }
    
    foreach ($coursedata as $index) {
        $page++;
        $index->index = $page;
    }
    return $coursedata;

}


function get_course_dpt_name($path)
{

    global $DB;

    $patharray = explode("/", $path);

    $data = $DB->get_record('course_categories', array('id' => $patharray[1]));

    return $data->name;
}

function local_sitereport_execute_task()
{
    global $DB;

    $data = $DB->get_records_sql('SELECT l.id,l.userid,l.courseid,l.timecreated FROM {course} as c JOIN {logstore_standard_log} as l ON c.id = l.courseid LEFT JOIN {local_course_creator} as lcc on lcc.userid=l.userid and lcc.courseid=l.courseid WHERE lcc.userid is null and lcc.courseid is null and l.eventname LIKE "%course_created%"', null);
    if ($data) {
        foreach ($data as $key => $value) {
            $object = new stdClass();
            $object->id = $value->id;
            $object->userid = $value->userid;
            $object->courseid = $value->courseid;
            $object->timecreated = $value->timecreated;
            $insert = $DB->insert_record('local_course_creator', $object);
        }
    }
    // echo "<pre>";print_r($data);die;
}

//zeeshan
function get_ceo_users($courseid, $type)
{

    global $DB;
    $coursecentext = context_course::instance($courseid);
    $intructors = get_enrolled_users($coursecentext, 'mod/quiz:manage');
    $enrol_users = get_enrolled_users($coursecentext, 'mod/assignment:submit');

    $intructorssql = "SELECT u.* FROM mdl_user u 
    INNER JOIN mdl_role_assignments ra ON ra.userid = u.id 
    INNER JOIN mdl_role r ON r.id = ra.roleid 
    INNER JOIN mdl_context ct ON ct.id = ra.contextid
    INNER JOIN mdl_course c ON c.id = ct.instanceid 
    WHERE ra.roleid=3 and c.id=$courseid";

    $intructors = $DB->get_records_sql($intructorssql,null);

   
    $complete = array();
    $inprogress = array();
    $notstarted = array();
    foreach ($intructors as $intructor) {
        $intructor_array[] = array('id' => $intructor->id, 'ecn' => $intructor->username, 'fullname' => $intructor->firstname . ' ' . $intructor->lastname);
    }
    foreach ($enrol_users as $user) {

        $is_course_complete = local_is_course_complete($courseid, $user->id);
        $isnotstarted = $DB->get_record_sql("SELECT id FROM {user_lastaccess} as ul WHERE courseid=$courseid and userid = $user->id");

        if ($is_course_complete) {
            $complete[] = array('id' => $user->id, 'ecn' => $user->username, 'fullname' => $user->firstname . ' ' . $user->lastname);
        } else if ($isnotstarted) {
            $inprogress[] = array('id' => $user->id, 'ecn' => $user->username, 'fullname' => $user->firstname . ' ' . $user->lastname);
        } else {
            $notstarted[] = array('id' => $user->id, 'ecn' => $user->username, 'fullname' => $user->firstname . ' ' . $user->lastname);
        }
    }
    $users = array('instructor' => $intructor_array, 'complete' => $complete, 'inprogress' => $inprogress, 'notstarted' => $notstarted);
    return $users;
    // print_object($users);die;
}

function get_instructors($courseid){
    global $DB;
    $sql = "SELECT COUNT(u.id) as total FROM mdl_user u 
    INNER JOIN mdl_role_assignments ra ON ra.userid = u.id 
    INNER JOIN mdl_role r ON r.id = ra.roleid 
    INNER JOIN mdl_context ct ON ct.id = ra.contextid
    INNER JOIN mdl_course c ON c.id = ct.instanceid 
    WHERE ra.roleid=3 and c.id=$courseid";
    $instructors = $DB->get_record_sql($sql);
    $totalinstructors = 0;
    if ($instructors) {
        $totalinstructors = $instructors->total;
    }
    return $totalinstructors;
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
