<?php

require_once __DIR__.'/common.php';

function block_exacam_get_context_from_courseid($courseid) {
	global $COURSE;

	if ($courseid instanceof context) {
		// already context
		return $courseid;
	} else if (is_numeric($courseid)) { // don't use is_int, because eg. moodle $COURSE->id is a string!
		return context_course::instance($courseid);
	} else if ($courseid === null) {
		return context_course::instance($COURSE->id);
	} else {
		throw new \moodle_exception('wrong courseid type '.gettype($courseid));
	}
}

function block_exacam_is_teacher($context = null) {
	$context = block_exacam_get_context_from_courseid($context);

	return has_capability('mod/quiz:addinstance', $context);
}
