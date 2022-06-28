<?php

/**
 * Schedule tasks
 *
 * @package     local_sitereport
 * @author      zeeshan khan 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 $tasks = [
    [
        'classname' => 'local_sitereport\task\course_creator',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ]
]

?>