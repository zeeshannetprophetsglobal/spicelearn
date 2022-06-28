<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Handles viewing a customcert.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once("{$CFG->libdir}/completionlib.php");
global $DB, $CFG, $SESSION;

$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$downloadown = optional_param('downloadown', false, PARAM_BOOL);
$downloadtable = optional_param('download', null, PARAM_ALPHA);
$downloadissue = optional_param('downloadissue', 0, PARAM_INT);
$deleteissue = optional_param('deleteissue', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', \mod_customcert\certificate::CUSTOMCERT_PER_PAGE, PARAM_INT);

$cm = $DB->get_record_sql("SELECT * FROM {customcert} WHERE id=$id");
if ($cm) {
    $check_enrol =  $DB->get_record_sql("SELECT  {enrol}.courseid, {course}.fullname FROM {enrol} INNER JOIN {user_enrolments} ON {user_enrolments}.enrolid = {enrol}.id INNER JOIN {course} ON {course}.id = {enrol}.courseid   WHERE {user_enrolments}.userid = $userid and {enrol}.courseid = $cm->course");
    if ($check_enrol) {
      
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $customcert = $DB->get_record('customcert', array('id' => $cm->id), '*', MUST_EXIST);
        $template = $DB->get_record('customcert_templates', array('id' => $customcert->templateid), '*', MUST_EXIST);

        // Check if the user can view the certificate based on time spent in course.
        if ($customcert->requiredtime && !$canmanage) {
            if (\mod_customcert\certificate::get_course_time($course->id) < ($customcert->requiredtime * 60)) {
                $a = new stdClass;
                $a->requiredtime = $customcert->requiredtime;
                notice(get_string('requiredtimenotmet', 'customcert', $a), "$CFG->wwwroot/course/view.php?id=$course->id");
                die;
            }
        }

        // Check that we are not downloading a certificate PDF.
        if ($downloadown ){ 
            $userid = $userid;
            if ($downloadown) {
                // Create new customcert issue record if one does not already exist.
                if (!$DB->record_exists('customcert_issues', array('userid' => $userid, 'customcertid' => $customcert->id))) {
                    \mod_customcert\certificate::issue_certificate($customcert->id, $userid);
                }

                // Set the custom certificate as viewed.
                $completion = new completion_info($course);
                $completion->set_module_viewed($cm);
            } else if ($downloadissue && $canviewreport) {
                $userid = $downloadissue;
            }

            // Hack alert - don't initiate the download when running Behat.
            if (defined('BEHAT_SITE_RUNNING')) {
                redirect(new moodle_url('/mod/customcert/view.php', array('id' => $cm->id)));
            }

            // Now we want to generate the PDF.
            $template = new \mod_customcert\template($template);
            // ob_end_clean();
            $template->generate_pdf(false, $userid);
            exit();
        }
    }else{
        echo "user not enrol in this course";
    }
}else{
    echo "not found";
}
