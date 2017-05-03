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

/*
function block_exacam_get_active_cmids() {
	$active_cmids = trim(get_config('block_exacam', 'active_cmids'), ',');
	if ($active_cmids) {
		$active_cmids = explode(',', $active_cmids);

		return array_combine($active_cmids, $active_cmids);
	} else {
		return [];
	}
}

function block_exacam_cmid_is_active($cm) {
	$active_cmids = block_exacam_get_active_cmids();
	return in_array($cm->id, $active_cmids);
}

function block_exacam_set_cmid_active_state($cmid, $active) {
	$active_cmids = block_exacam_get_active_cmids();

	if ($active) {
		$active_cmids[$cmid] = $cmid;
	} else {
		unset($active_cmids[$cmid]);
	}

	set_config('active_cmids', join(',', $active_cmids), 'block_exacam');
}
*/

function block_exacam_cmid_is_active($cm) {
	global $DB;
	return $DB->record_exists('quiz', ['useexacam' => 1, 'id' => $cm->instance]);
}

function block_exacam_print_config($cm) {
	$config = [
		'cmid' => $cm->id,
		'active' => block_exacam_cmid_is_active($cm),
		'is_teacher' => block_exacam_is_teacher($cm->course),
	];

	register_shutdown_function(function() use ($config) {
		?>
		<script>
					window.exacam_config = <?=json_encode($config)?>;
		</script>
		<?php
	});
}

function block_exacam_install_or_update() {
	global $DB;

	$dbman = $DB->get_manager();

	// Define field periodid to be added to block_exastudclass
	$table = new xmldb_table('quiz');
	$field = new xmldb_field('useexacam', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'showuserpicture' );

	// Conditionally launch add field
	if (!$dbman->field_exists($table, $field)) {
		$dbman->add_field($table, $field);

		// not needed: dbman resets the cache already
		// purge_all_caches();
	}
}
