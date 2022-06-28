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

$string['filelockedmail'] = 'You has been enroled in {$a->fullname} ({$a->url})';
$string['location'] = 'Message';
$string['messageprovider:notificationeabc_enrolment'] = 'Enrol notification messages';
$string['notificationeabc:manage'] = 'Manage notificationeabc';
$string['pluginname'] = 'Enrol Notification';
$string['pluginname_desc'] = 'Enrol notifications via mail';
$string['location_help'] = 'Personalize the message that users will come to be enrolled. This field accepts the following markers which then will be replaced by the corresponding values ​​dynamically
<pre>
{COURSENAME} = course fullname
{USERNAME} = username
{NOMBRE} = firstname
{APELLIDO} = lastname
{URL} = course url 
</pre>';
$string['fecha_help'] = 'Place the period for which you want to perform the first virificación';
$string['fecha'] = 'Period for verification of users enrolled courses';
$string['activar'] = 'Enable initial verification';
$string['activar_help'] = 'When activated will be verified by the immediate execution of cron later, users who were enrolled for the period specified above';
$string['activarglobal'] = 'Active global';
$string['activarglobal_help'] = 'Active enrol notification for all site';
$string['emailsender'] = 'Email sender ';
$string['emailsender_help'] = 'By default set to take the email user support ';
$string['namesender'] = 'Name sender ';
$string['namesender_help'] = 'By default it takes the name set to the user support';
$string['status'] = 'Active enrol notification';
$string['subject'] = 'Enrollment Notification';
$string['activeenrolalert'] = 'Active enrol alert';
$string['activeenrolalert_help'] = 'Active enrol alert';


//unenrol notifications
$string['activeunenrolalert'] = 'Active unenrol notifications';
$string['activeunenrolalert_help'] = 'Active unenrol alert';
$string['activarglobalunenrolalert'] = 'Active global';
$string['activarglobalunenrolalert_help'] = 'Active enrol notifications for all site';
$string['unenrolmessage'] = 'Custom Message';
$string['unenrolmessage_help'] = 'Personalize the message that users will come to be unenrolled. This field accepts the following markers which then will be replaced by the corresponding values ​​dynamically
<pre>
{COURSENAME} = course fullname
{USERNAME} = username
{NOMBRE} = firstname
{APELLIDO} = lastname
{URL} = course url 
</pre>';
$string['unenrolmessagedefault'] = 'You has been unenrolled from {$a->fullname} ({$a->url})';


//Update enrol notifications
$string['activeenrolupdatedalert'] = 'Active update enrol notifications';
$string['activeenrolupdatedalert_help'] = 'Active update enrol notifications';
$string['activarglobalenrolupdated'] = 'Active global';
$string['activarglobalenrolupdated_help'] = 'Active enrol updated notifications for all site';
$string['updatedenrolmessage'] = 'Mensaje personalizado';
$string['updatedenrolmessage_help'] = 'Personalize the message that users will come to be updated. This field accepts the following markers which then will be replaced by the corresponding values ​​dynamically
<pre>
{COURSENAME} = course fullname
{USERNAME} = username
{NOMBRE} = firstname
{APELLIDO} = lastname
{URL} = course url 
</pre>';
$string['updatedenrolmessagedefault'] = 'Your enrolment from {$a->fullname} has been updated ({$a->url})';