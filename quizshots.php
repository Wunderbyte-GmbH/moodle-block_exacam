<?php
// This file is part of Exabis Quiz Camera
//
// (c) 2017 GTN - Global Training Network GmbH <office@gtn-solutions.com>
//
// Exabis Competence Grid is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You can find the GNU General Public License at <http://www.gnu.org/licenses/>.
//
// This copyright notice MUST APPEAR in all copies of the script!

require __DIR__.'/inc.php';

$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);

if (!block_exacam_is_teacher()) {
	throw new moodle_exception('no teacher');
}

$PAGE->set_url('/blocks/exacam/quizstart.php', array('courseid' => $courseid));
$PAGE->set_heading('');

$quizzes = get_coursemodules_in_course('quiz', $courseid);

$userid = optional_param('userid', 0, PARAM_INT);
$quizid = optional_param('quizid', 0, PARAM_INT);
$quiz = null;

if ($quizid) {
	if (!isset($quizzes[$quizid])) {
		throw new \Exception('quiz not found');
	}

	$quiz = $quizzes[$quizid];
}

echo $OUTPUT->header();

if ($userid && $quiz) {
	$user = $DB->get_record('user', ['id' => $userid]);

	echo '<h2>Quiz '.$quiz->name.' / Benutzer '.fullname($user).'</h2>';

	$fs = get_file_storage();
	$files = $fs->get_area_files(context_module::instance($quiz->id)->id, 'block_exacam', 'quizshot', $userid, 'timemodified DESC', false);

	echo '<div>';
	?>
	<style>
		.exacam-img {
			float: left;
			padding: 5px;
			margin: 5px;
			border: 1px solid black;
		}
		.exacam-img span {
			display: block;
			text-align: center;
			margin: 3px 0 -3px 0;
		}
	</style>
	<?php
	foreach ($files as $file) {

		$imageurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
		$img = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => ''));
		echo '<div class="exacam-img"><div>'.$img.'</div><span>'.userdate($file->get_timemodified()).'</span></div>';
	}
	echo '</div>';
} else {
	if ($quiz) {
		// loop one quiz
		$loop = [$quiz];
	} else {
		$loop = $quizzes;
	}
	foreach ($loop as $quiz) {
		echo '<h2>Quiz '.$quiz->name.'</h2>';

		$users = $DB->get_records_sql("
			SELECT u.*
			FROM {user} u
			WHERE u.id IN (
				SELECT DISTINCT userid
				FROM {files}
				WHERE component='block_exacam' AND filearea='quizshot' AND filename<>'.'
				AND contextid = ?
			)
		", [context_module::instance($quiz->id)->id]);

		foreach ($users as $user) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?courseid='.$courseid.'&quizid='.$quiz->id.'&userid='.$user->id.'">'.fullname($user).'</a><br/>';
		}
	}
}

echo $OUTPUT->footer();
