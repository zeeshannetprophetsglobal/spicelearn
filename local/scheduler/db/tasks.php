<?php

/**
 * Schedule tasks
 *
 * @package     local_scheduler
 * @author      zeeshan khan 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 $tasks = [
    [
        'classname' => 'local_scheduler\task\user_transcript',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ]
]

?>