<?php

require __DIR__.'/inc.php';

$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);

$PAGE->set_url('/blocks/dukcam/quizstart.php', array('courseid' => $courseid));
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

					Webcam.upload(data_uri, M.cfg.wwwroot + '/blocks/dukcam/upload.php?cmid='+block_dukcam.body_param('cmid'), function (code, text) {
						// Upload complete!
						// 'code' will be the HTTP response code from the server, e.g. 200
						// 'text' will be the raw response content
						parent.dukcam_webcamtest_finished();
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
