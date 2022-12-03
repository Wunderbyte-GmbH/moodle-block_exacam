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

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/inc.php';

function block_exacam_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
	// Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
	require_login($course, true, $cm);

	// Check the relevant capabilities - these may vary depending on the filearea being accessed.

	// Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
	$itemid = array_shift($args); // The first item in the $args array.

	// Extract the filename / filepath from the $args array.
	$filename = array_pop($args); // The last item in the $args array.

	if ($filearea == 'quizshot') {
		if (!block_exacam_is_teacher($context)) {
			throw new moodle_exception('no teacher');
		}

		$fs = get_file_storage();
		$file = $fs->get_file($context->id, 'block_exacam', $filearea, $itemid, '/', $filename);

		if (!$file) {
			return false;
		}

		$options['filename'] = $filename;
	} else {
		// wrong filearea
		return false;
	}

	send_stored_file($file, 0, 0, $forcedownload, $options);
	exit;
}
