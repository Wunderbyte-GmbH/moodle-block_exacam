<?php

require __DIR__.'/inc.php';

$cmid = required_param('cmid', PARAM_INT);

if (!$cm = get_coursemodule_from_id('quiz', $cmid)) {
	print_error('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
	print_error('coursemisconf');
}
$context = context_course::instance($course->id);

require_login($course);

$PAGE->set_url('/blocks/exacam/quizstart.php', array('courseid' => $course->id));
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

			function webcam_error(err) {
				$('#exacam-error').html('Fehler: ' + err);
			}

			Webcam.on('error', function (err) {
				webcam_error(err);
			});

			Webcam.on('live', function (err) {
				$('#submit').show();
			});

			$('#submit').click(function () {
				Webcam.snap(function (data_uri) {
					// snap complete, image data is in 'data_uri'

					Webcam.upload(data_uri, M.cfg.wwwroot + '/blocks/exacam/upload.php?cmid=' + block_exacam.get_param('cmid'), function (code, text) {
						// Upload complete!
						// 'code' will be the HTTP response code from the server, e.g. 200
						// 'text' will be the raw response content

						if (code != 200) {
							return webcam_error('Fehler beim speichern des Webcam Bildes');
						}

						console.log(text);
						if (text !== 'ok') {
							if (text.match(/\n/)) {
								return webcam_error('Unbekannter fehler');
							} else {
								return webcam_error(text);
							}
						}

						parent.exacam_webcamtest_finished();
					});
				});
			});
		});
	</script>

	<center id="exacam-content">
		<h3>Webcamtest</h3>
		<div>Wenn Sie eine Webcam besitzen sollten sie unten das aktuelle Webcambild sehen.<br/>
			Bitte prüfen Sie, ob Ihr Gesicht auch erkennbar ist.
		</div>
		<div id="my_camera"></div>

		<input type=button value="Ich sehe mich selbst" id="submit" style="display: none;">
		<div id="exacam-error" style="color: red; font-weight: bold;"></div>
	</center>
<?php

echo $OUTPUT->footer();
