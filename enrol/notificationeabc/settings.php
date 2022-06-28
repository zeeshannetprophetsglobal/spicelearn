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

if ($ADMIN->fulltree) {

    //--- general settings -----------------------------------------------------------------------------------

    //notificacion de matriculacion

    $settings->add(new admin_setting_configcheckbox('enrol_notificationeabc/activeenrolalert', get_string('activeenrolalert', 'enrol_notificationeabc'), get_string('activeenrolalert_help', 'enrol_notificationeabc'), '', '1'));

    $settings->add(new admin_setting_configcheckbox('enrol_notificationeabc/activarglobal', get_string('activarglobal', 'enrol_notificationeabc'), get_string('activarglobal_help', 'enrol_notificationeabc'), ''));

    $settings->add(new admin_setting_heading('enrol_notificationeabc_settings', '', get_string('pluginname_desc', 'enrol_notificationeabc'), ''));

    $settings->add(new admin_setting_confightmleditor('enrol_notificationeabc/location', get_string('location', 'enrol_notificationeabc'), get_string('location_help', 'enrol_notificationeabc'),''));


   //notificacion de desmatriculacion
    
    $settings->add(new admin_setting_configcheckbox('enrol_notificationeabc/activeunenrolalert', get_string('activeunenrolalert', 'enrol_notificationeabc'), get_string('activeunenrolalert_help', 'enrol_notificationeabc'), '', '1'));

    $settings->add(new admin_setting_configcheckbox('enrol_notificationeabc/activarglobalunenrolalert', get_string('activarglobalunenrolalert', 'enrol_notificationeabc'), get_string('activarglobalunenrolalert_help', 'enrol_notificationeabc'), ''));

    $settings->add(new admin_setting_confightmleditor('enrol_notificationeabc/unenrolmessage', get_string('unenrolmessage', 'enrol_notificationeabc'), get_string('unenrolmessage_help', 'enrol_notificationeabc'),''));


	   //notificacion de actualizacion de matriculacion 
    
    $settings->add(new admin_setting_configcheckbox('enrol_notificationeabc/activeenrolupdatedalert', get_string('activeenrolupdatedalert', 'enrol_notificationeabc'), get_string('activeenrolupdatedalert_help', 'enrol_notificationeabc'), '', '1'));

    $settings->add(new admin_setting_configcheckbox('enrol_notificationeabc/activarglobalenrolupdated', get_string('activarglobalenrolupdated', 'enrol_notificationeabc'), get_string('activarglobalenrolupdated_help', 'enrol_notificationeabc'), ''));

    $settings->add(new admin_setting_confightmleditor('enrol_notificationeabc/updatedenrolmessage', get_string('updatedenrolmessage', 'enrol_notificationeabc'), get_string('updatedenrolmessage_help', 'enrol_notificationeabc'),''));    







   $settings->add(new admin_setting_configtext('enrol_notificationeabc/emailsender', get_string('emailsender', 'enrol_notificationeabc'), get_string('emailsender_help', 'enrol_notificationeabc'), ''));

   $settings->add(new admin_setting_configtext('enrol_notificationeabc/namesender', get_string('namesender', 'enrol_notificationeabc'), get_string('namesender_help', 'enrol_notificationeabc'), ''));
    //--- mapping -------------------------------------------------------------------------------------------
}
