<?php

/**
 * Schedule tasks
 *
 * @package     local_sitereport
 * @author      zeeshan khan 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 

namespace local_sitereport\task;

defined('MOODLE_INTERNAL') || die;

class course_creator extends \core\task\scheduled_task {
     public function get_name() {
          // Shown on admin screens
         return get_string('course_creator', 'local_sitereport'); //get the string from lang/en/ 
     }

     public function execute() {  
          global $CFG;
          mtrace("course creator task started");
          require_once($CFG->dirroot . '/local/sitereport/locallib.php');
          local_sitereport_execute_task();
          mtrace("course creator task finished");
     }
}
?>