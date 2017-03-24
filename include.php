<?php

if (defined('ABORT_AFTER_CONFIG')) {
	// for javascript.php etc don't load the webcam plugin
	return;
}

require __DIR__.'/inc.php';

/*
class block_dukcam_footer {
	private $originalOutput;

	function __construct($originalOutput) {
		$this->originalOutput = $originalOutput;
	}

	function output() {
		global $CFG;

		return '<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dukcam/js/webcam.js"></script>'
		.'<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dukcam/js/jquery.disablescroll.js"></script>'
		.'<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dukcam/js/dukcam.js"></script>';
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
	if (in_array($self, [
		'/mod/quiz/attempt.php',
		'/blocks/dukcam/',
	])) {
		// $CFG->additionalhtmlfooter = new block_dukcam_footer($CFG->additionalhtmlfooter);

		if ($PAGE) {
			$PAGE->requires->jquery();
			$PAGE->requires->js('/blocks/dukcam/js/webcam.js');
			$PAGE->requires->js('/blocks/dukcam/js/jquery.disablescroll.js');
			$PAGE->requires->js('/blocks/dukcam/js/dukcam.js');
		}

		register_shutdown_function(function() {
			?>
			<script>
				window.dukcam_config = {
					active: true,
					is_teacher: <?=json_encode(block_dukcam_is_teacher())?>,
				};
			</script>
			<?php
		});
	}
});
