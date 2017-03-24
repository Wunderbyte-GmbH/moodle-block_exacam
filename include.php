<?php

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

if (strpos($_SERVER['PHP_SELF'], '/mod/quiz') || strpos($_SERVER['PHP_SELF'], '/blocks/dukcam')) {
	// $CFG->additionalhtmlfooter = new block_dukcam_footer($CFG->additionalhtmlfooter);

	if ($PAGE) {
		$PAGE->requires->jquery();
		$PAGE->requires->js('/blocks/dukcam/js/webcam.js');
		$PAGE->requires->js('/blocks/dukcam/js/jquery.disablescroll.js');
		$PAGE->requires->js('/blocks/dukcam/js/dukcam.js');
	}
}

/*
register_shutdown_function(function(){
	echo 'x';
});
*/