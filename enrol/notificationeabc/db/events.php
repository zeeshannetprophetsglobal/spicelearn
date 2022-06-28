<?php

/**
 * Notificationeabc enrolment plugin.
 *
 * This plugin notifies users when an event occurs on their enrolments (enrol, unenrol, update enrolment)
 *
 * @package    enrol_notificationeabc
 * @copyright  2016 e-ABC Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = array(

    array(
        'eventname'   => '\core\event\user_enrolment_deleted',
        'callback'    => 'enrol_notificationeabc_observer::user_unenroled',
    ),

    array(
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => 'enrol_notificationeabc_observer::user_enroled',
    ),

    array(
        'eventname'   => '\core\event\user_enrolment_updated',
        'callback'    => 'enrol_notificationeabc_observer::user_updated',
    )

);