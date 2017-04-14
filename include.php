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

if (@constant('ABORT_AFTER_CONFIG') || @constant('AJAX_SCRIPT')) {
	// for javascript.php etc don't load the webcam plugin
	return;
}

require __DIR__.'/inc.php';

/*
class block_exacam_footer {
	private $originalOutput;

	function __construct($originalOutput) {
		$this->originalOutput = $originalOutput;
	}

	function output() {
		global $CFG;

		return '<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/exacam/js/webcam.js"></script>'
		.'<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/exacam/js/jquery.disablescroll.js"></script>'
		.'<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/exacam/js/exacam.js"></script>';
	}

	function __toString() {
		return $this->originalOutput.$this->output();
	}
}
*/

call_user_func(function() {
	global $CFG, $DB, $PAGE;

	$wwwroot = preg_replace('!^[^/]+://[^/]+!', '', $CFG->wwwroot);
	$self = str_replace($wwwroot, '', $_SERVER['PHP_SELF']);
	if (strpos($self, '/mod/quiz/') === 0 || strpos($self, '/mod/exacam/') == 0) {
		// $CFG->additionalhtmlfooter = new block_exacam_footer($CFG->additionalhtmlfooter);

		if ($PAGE) {
			$PAGE->requires->jquery();
			$PAGE->requires->js('/blocks/exacam/js/webcam.js');
			$PAGE->requires->js('/blocks/exacam/js/jquery.disablescroll.js');
			$PAGE->requires->js('/blocks/exacam/js/exacam.js');
		}

		if ($self == '/mod/quiz/view.php') {
			$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or ...
			$q = optional_param('q', 0, PARAM_INT);  // Quiz ID.

			if ($id) {
				if (!$cm = get_coursemodule_from_id('quiz', $id)) {
					print_error('invalidcoursemodule');
				}
				//if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
				//	print_error('coursemisconf');
				//}
			} else {
				if (!$quiz = $DB->get_record('quiz', array('id' => $q))) {
					print_error('invalidquizid', 'quiz');
				}
				if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
					print_error('invalidcourseid');
				}
				if (!$cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
					print_error('invalidcoursemodule');
				}
			}

			// config immer ausgeben
			block_exacam_print_config($cm);

			if (!block_exacam_cmid_is_active($cm)) {
				return;
			}

			// auf der quiz-start seite:
			// starten nur erlauben, wenn js aktiv ist
			ob_start(function($output) {
				$output = str_replace('</head>',
					'<style>
							form[action*="startattempt.php"]{
								display: none;
							}
							.jsenabled form[action*="startattempt.php"]{
								display: block;
							}
							.jsenabled #exacam-jsnotice {
								display: none;
							}
						</style></head>', $output);

				$jsnotice = '<div id="exacam-jsnotice">Bitte aktivieren Sie JavaScript</div>';
				$output = preg_replace('!<form.{0,200}startattempt\.php!', $jsnotice.'$0', $output);
				// $output = preg_replace('!(<form.{0,200}startattempt\.php.*)"submit"!U', '$1"button"', $output);

				return $output;
			});
		}

		if ($self == '/mod/quiz/attempt.php') {
			$attemptid = optional_param('attempt', 0, PARAM_INT);
			if (!$attemptid) {
				return;
			}

			require_once($CFG->dirroot . '/mod/quiz/locallib.php');
			$attemptobj = quiz_attempt::create($attemptid);

			// config immer ausgeben
			block_exacam_print_config($attemptobj->get_cm());

			if (!block_exacam_cmid_is_active($attemptobj->get_cm())) {
				return;
			}

			// während dem quiz:
			// page-content nur zeigen, wenn js aktiv ist
			ob_start(function($output) {
				$output = str_replace('</head>',
					'<style>
						#page-content{
							display: none;
						}
					</style></head>', $output);

				return $output;
			});
		}
	}

	/*
	if (strpos($self, '/course/modedit.php') === 0) {
		$add    = optional_param('add', '', PARAM_ALPHA);     // module name
		$update = optional_param('update', 0, PARAM_INT);

		$addtoform = false;
		if ($add === 'quiz') {
			$addtoform = true;
		} elseif ($update) {
		    $cm = get_coursemodule_from_id('', $update, 0, false, MUST_EXIST);
		    if ($cm && $cm->modname === 'quiz') {
		    	$addtoform = true;
			}
		}

		var_dump($addtoform);
	}
	*/
});
