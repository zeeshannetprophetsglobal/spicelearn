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
defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2021051700;        // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2021051100;        // Requires this Moodle version
$plugin->component = 'enrol_notificationeabc';  // Full name of the plugin (used for diagnostics)
$plugin->cron      = 30;
