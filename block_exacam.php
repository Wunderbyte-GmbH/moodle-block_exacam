<?php

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
