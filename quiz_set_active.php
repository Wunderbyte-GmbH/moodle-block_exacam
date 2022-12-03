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

$cmid = required_param('cmid', PARAM_INT);

if (!$cm = get_coursemodule_from_id('quiz', $cmid)) {
	print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record("course", array("id" => $cm->course))) {
	print_error('coursemisconf');
}

require_login($course);

if (!block_exacam_is_teacher()) {
	throw new moodle_exception('no teacher');
}

$active = required_param('active', PARAM_BOOL);

block_exacam_set_cmid_active_state($cmid, $active);

header("Location: ".required_param('back', PARAM_TEXT));
