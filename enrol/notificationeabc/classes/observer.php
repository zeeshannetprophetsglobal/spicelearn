<?php

/**
 * Notificationeabc enrolment plugin.
 *
 * This plugin notifies users when an event occurs on their enrolments (enrol, unenrol, update enrolment)
 *
 * @package    enrol
 * @subpackage notificationeabc
 * @copyright  2016 e-ABC Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/enrol/notificationeabc/lib.php');

class enrol_notificationeabc_observer
{

    public static function user_unenroled(\core\event\user_enrolment_deleted $event)
    {
        global $DB;

        //e-ABC	validate status plugin
        $enable_plugins = get_config(null, 'enrol_plugins_enabled');
        $enable_plugins = explode(',', $enable_plugins);
        $enabled = false;
        foreach ($enable_plugins as $enable_plugin) {
            if ($enable_plugin === 'notificationeabc') {
                $enabled = true;
            }
        }
        if ($enabled) {
            $user = $DB->get_record('user', array('id' => $event->relateduserid));
            $course = $DB->get_record('course', array('id' => $event->courseid));

            $notificationeabc = new enrol_notificationeabc_plugin();

            $activeglobal = $notificationeabc->get_config('activarglobalunenrolalert');
            $activeunenrolalert = $notificationeabc->get_config('activeunenrolalert');

            $enrol = $DB->get_record('enrol', array('enrol' => 'notificationeabc', 'courseid' => $event->courseid));

            /*
            * check the instance status
            * status = 0 enabled and status = 1 disabled
            */
            $instance_enabled = false;
            if (!empty($enrol)) {
                if (!$enrol->status) {
                    $instance_enabled = true;
                }
            }
            if (!empty($enrol) && $instance_enabled) {
                $activeunenrolalert = $enrol->customint4;
            }

            if ($activeglobal == 1 && $activeunenrolalert == 1) {
                $notificationeabc->enviarmail($user, $course, 2);
            } else if (!empty($enrol) && !empty($activeunenrolalert) && $instance_enabled) {
                $notificationeabc->enviarmail($user, $course, 2);
            }
        }
    }

    public static function user_updated(\core\event\user_enrolment_updated $event)
    {
        global $DB;

        //e-ABC	validate plugin status in system context
        $enable_plugins = get_config(null, 'enrol_plugins_enabled');
        $enable_plugins = explode(',', $enable_plugins);
        $enabled = false;
        foreach ($enable_plugins as $enable_plugin) {
            if ($enable_plugin === 'notificationeabc') {
                $enabled = true;
            }
        }
        if ($enabled) {
            $user = $DB->get_record('user', array('id' => $event->relateduserid));
            $course = $DB->get_record('course', array('id' => $event->courseid));

            $notificationeabc = new enrol_notificationeabc_plugin();

            $activeglobal = $notificationeabc->get_config('activarglobalenrolupdated');
            $activeenrolupdatedalert = $notificationeabc->get_config('activeenrolupdatedalert');

            //plugin instance in course
            $enrol = $DB->get_record('enrol', array('enrol' => 'notificationeabc', 'courseid' => $event->courseid));

            /*
            * check the instance status
            * status = 0 enabled and status = 1 disabled
            */
            $instance_enabled = false;
            if (!empty($enrol)) {
                if (!$enrol->status) {
                    $instance_enabled = true;
                }
            }
            if (!empty($enrol) && $instance_enabled) {
                $activeenrolupdatedalert = $enrol->customint5;
            }

            if ($activeglobal == 1 && $activeenrolupdatedalert == 1) {
                $notificationeabc->enviarmail($user, $course, 3);
            } else if (!empty($enrol) && !empty($activeenrolupdatedalert) && $instance_enabled) {
                $notificationeabc->enviarmail($user, $course, 3);
            }
        }
    }

    public static function user_enroled(\core\event\user_enrolment_created $event)
    {
        global $DB;

        //e-ABC	validate plugin status in system context
        $enable_plugins = get_config(null, 'enrol_plugins_enabled');
        $enable_plugins = explode(',', $enable_plugins);
        $enabled = false;
        foreach ($enable_plugins as $enable_plugin) {
            if ($enable_plugin === 'notificationeabc') {
                $enabled = true;
            }
        }
        if ($enabled) {
            $user = $DB->get_record('user', array('id' => $event->relateduserid));
            $course = $DB->get_record('course', array('id' => $event->courseid));

            $notificationeabc = new enrol_notificationeabc_plugin();

            $activeglobal = $notificationeabc->get_config('activarglobal');
            $activeenrolalert = $notificationeabc->get_config('activeenrolalert');

            $enrol = $DB->get_record('enrol', array('enrol' => 'notificationeabc', 'courseid' => $event->courseid));

            /*
            * check the instance status
            * status = 0 enabled and status = 1 disabled
            */
            $instance_enabled = false;
            if (!empty($enrol)) {
                if (!$enrol->status) {
                    $instance_enabled = true;
                }
            }

            if (!empty($enrol) && $instance_enabled) {
                $activeenrolalert = $enrol->customint3;
            }

            if ($activeglobal == 1 && $activeenrolalert == 1) {
                $notificationeabc->enviarmail($user, $course, 1);
            } else if (!empty($enrol) && !empty($activeenrolalert) && $instance_enabled) {
                $notificationeabc->enviarmail($user, $course, 1);
            }
        }
    }
}