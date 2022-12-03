<?php
// 20210104 harald.bamberger@donau-uni.ac.at add task to cleanup quizshots

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'block_exacam\task\delete_old_quizshots',
        'blocking' => 0,
	'minute' => '*/10',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    )
);
