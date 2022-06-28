<?php

/**
 * Schedule tasks
 *
 * @package     local_scheduler
 * @author      zeeshan khan 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 

namespace local_scheduler\task;

defined('MOODLE_INTERNAL') || die;

class user_transcript extends \core\task\scheduled_task {
     public function get_name() {
          // Shown on admin screens
         return get_string('user_transcript', 'local_scheduler'); //get the string from lang/en/ 
     }

     public function execute() {  
          global $CFG;
          mtrace("user transcript task started");
          // require_once($CFG->dirroot . '/local/scheduler/locallib.php');
          // local_course_created();
          // local_user_enrolment_created();
          mtrace("user transcript task finished");
     }
}
?>