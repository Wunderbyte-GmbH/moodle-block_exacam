<?php

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
	global $CFG, $PAGE;

	$wwwroot = preg_replace('!^[^/]+://[^/]+!', '', $CFG->wwwroot);
	$self = str_replace($wwwroot, '', $_SERVER['PHP_SELF']);
	if (strpos($self, '/mod/quiz/') == 0 || strpos($self, '/mod/exacam/') == 0) {
		// $CFG->additionalhtmlfooter = new block_exacam_footer($CFG->additionalhtmlfooter);

		if ($PAGE) {
			$PAGE->requires->jquery();
			$PAGE->requires->js('/blocks/exacam/js/webcam.js');
			$PAGE->requires->js('/blocks/exacam/js/jquery.disablescroll.js');
			$PAGE->requires->js('/blocks/exacam/js/exacam.js');
		}

		register_shutdown_function(function() {
			?>
			<script>
				window.exacam_config = {
					active: true,
					is_teacher: <?=json_encode(block_exacam_is_teacher())?>,
				};
			</script>
			<?php
		});

		if ($self == '/mod/quiz/view.php' && !block_exacam_is_teacher()) {
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

				return $output;
			});
		}

		if ($self == '/mod/quiz/attempt.php' && !block_exacam_is_teacher()) {
			// während dem quiz:
			// page-content nur zeigen, wenn js aktiv ist
			ob_start(function($output) {
				$output = str_replace('</head>',
					'<style>
						#page-content{
							display: none;
						}
						.jsenabled #page-content{
							display: block;
						}
					</style></head>', $output);

				return $output;
			});
		}
	}
});
