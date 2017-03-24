<?php

require __DIR__.'/inc.php';

$courseid = required_param('courseid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
	print_error('invalidcourse', 'block_exacomp', $courseid);
}

require_login($course);

$context = context_course::instance($courseid);

$PAGE->set_url('/blocks/dukquiz/quizstart.php', array('courseid' => $courseid));
$PAGE->set_heading('');
$PAGE->set_pagelayout('embedded');

echo $OUTPUT->header();
?>
	<style>
		body {
			margin: 0 !important;
			padding: 0 !important;
		}
	</style>
	<!-- Configure a few settings and attach camera -->
	<script language="JavaScript">
		$(function () {
			Webcam.set({
				width: 320,
				height: 240,
				dest_width: 640,
				dest_height: 480,
				image_format: 'jpeg',
				jpeg_quality: 85
			});
			Webcam.attach('#my_camera');

			$('#submit').click(function () {
				Webcam.snap(function (data_uri) {
					// snap complete, image data is in 'data_uri'

					Webcam.upload(data_uri, M.cfg.wwwroot + '/blocks/dukquiz/upload.php?cmid='+block_dukquiz.body_param('cmid'), function (code, text) {
						// Upload complete!
						// 'code' will be the HTTP response code from the server, e.g. 200
						// 'text' will be the raw response content
						parent.dukquiz_webcamtest_finished();
					});
				});
			});
		});
	</script>

	<center>
		<h3>Webcamtest</h3>
		<div>Wenn Sie eine Webcam besitzen sollten sie unten das aktuelle Webcambild sehen.<br/>
			Bitte prüfen Sie, ob Ihr Gesicht auch erkennbar ist.
		</div>
		<div id="my_camera"></div>
		<input type=button value="Ich sehe mich selbst" id="submit">
	</center>
<?php

echo $OUTPUT->footer();
