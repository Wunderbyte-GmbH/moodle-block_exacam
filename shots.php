<?php

require __DIR__.'/inc.php';

$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);


require_capability('moodle/grade:viewall', $context);

$PAGE->set_url('/blocks/dukcam/quizstart.php', array('courseid' => $courseid));
$PAGE->set_heading('');

echo $OUTPUT->header();

$quizzes = get_coursemodules_in_course('quiz', $courseid);

$userid = optional_param('userid', 0, PARAM_INT);
$quizid = optional_param('quizid', 0, PARAM_INT);

if ($userid && $quizid) {
	if (!isset($quizzes[$quizid])) {
		throw new \Exception('quiz not found');
	}

	$user = $DB->get_record('user', ['id' => $userid]);

	$quiz = $quizzes[$quizid];
	echo '<h2>Quiz '.$quiz->name.' / Benutzer '.fullname($user).'</h2>';

	$fs = get_file_storage();
	$files = $fs->get_area_files(context_module::instance($quiz->id)->id, 'block_dukcam', 'quizshot', $userid, 'timemodified DESC');

	echo '<table>';
	foreach ($files as $file) {
		echo '<tr><td>'.userdate($file->get_timemodified()).'</td><td>'.'file</td></tr>';
	}
	echo '</table>';
} else {
	foreach ($quizzes as $quiz) {
		echo '<h2>Quiz '.$quiz->name.'</h2>';

		$users = $DB->get_records_sql("
			SELECT u.*
			FROM {user} u
			WHERE u.id IN (
				SELECT DISTINCT userid
				FROM {files}
				WHERE component='block_dukcam' AND filearea='quizshot' AND filename<>'.'
				AND contextid = ?
			)
		", [context_module::instance($quiz->id)->id]);

		foreach ($users as $user) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?courseid='.$courseid.'&quizid='.$quiz->id.'&userid='.$user->id.'">'.fullname($user).'</a><br/>';
		}
	}
}

echo $OUTPUT->footer();
