<?php

/*
class block_dukquiz_footer {
	private $originalOutput;

	function __construct($originalOutput) {
		$this->originalOutput = $originalOutput;
	}

	function output() {
		global $CFG;

		return '<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dukquiz/js/webcam.js"></script>'
		.'<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dukquiz/js/jquery.disablescroll.js"></script>'
		.'<script type="text/javascript" src="'.$CFG->wwwroot.'/blocks/dukquiz/js/dukquiz.js"></script>';
	}

	function __toString() {
		return $this->originalOutput.$this->output();
	}
}
*/

if (strpos($_SERVER['PHP_SELF'], '/mod/quiz') || strpos($_SERVER['PHP_SELF'], '/blocks/dukquiz')) {
	// $CFG->additionalhtmlfooter = new block_dukquiz_footer($CFG->additionalhtmlfooter);

	if ($PAGE) {
		$PAGE->requires->jquery();
		$PAGE->requires->js('/blocks/dukquiz/js/webcam.js');
		$PAGE->requires->js('/blocks/dukquiz/js/jquery.disablescroll.js');
		$PAGE->requires->js('/blocks/dukquiz/js/dukquiz.js');
	}
}

/*
register_shutdown_function(function(){
	echo 'x';
});
*/