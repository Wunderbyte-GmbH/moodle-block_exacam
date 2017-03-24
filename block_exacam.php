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

require_once __DIR__.'/inc.php';
require_once __DIR__.'/../moodleblock.class.php';

class block_exacam extends block_list {

	function init() {
		$this->title = block_exacam_get_string('blocktitle');
	}

	function instance_allow_multiple() {
		return false;
	}

	function has_config() {
		return true;
	}

	function instance_allow_config() {
		return false;
	}

	function get_content() {
		global $CFG, $COURSE, $OUTPUT;

		$this->content = new stdClass;
		$this->content->items = array();
		$this->content->icons = array();
		$this->content->footer = '';

		if (block_exacam_is_teacher()) {
			/*
			$icon = '<img src="'.$OUTPUT->pix_url('i/settings').'" class="icon" alt="" />';
			$this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/exacam/quizshots.php?courseid='.$COURSE->id.'">'.$icon.'Webcambilder anzeigen'.'</a>';
			*/

			$icon = '<img src="'.$OUTPUT->pix_url('i/users').'" class="icon" alt="" />';
			$this->content->items[] = '<a href="'.$CFG->wwwroot.'/blocks/exacam/quizshots.php?courseid='.$COURSE->id.'">'.$icon.'Webcambilder anzeigen'.'</a>';
		}
		return $this->content;
	}
}
