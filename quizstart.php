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

		#exacam-content > div,
	 	#exacam-content > input {
			margin: 5px 0;
		}

		#exacam-content a:target::before {
		  display: inline-block !important;
		}

		a#fn1 {
		  font-size: 16px;
		}

		#preview,
		#btnwrapper {
		  display: flex;
                  flex-direction: row;
                  flex-wrap: nowrap;
                  justify-content: center;
                }

		#description {
		  max-width: 640px;
		  margin-left: auto;
		  margin-right: auto;		
		}

		.column {
	          flex: 50%;
		}

		#checklist,
		#desctxt {
		  text-align: left;
		}

		#checklist li {
		  margin: 3px 0;
		}

		#desctxt {
		  margin: 1rem 0;
		  border-top: 1px solid rgba(128,128,128,.5);
		  padding-top: 1rem;
		}

		.column video, .column img {
		  max-width: 640px !important;
                  width: 100% !important;
	          height: auto !important;
		}

		#idshot {
		  display: none;
		}		

		div#my_camera,
		div#idshot {
                  margin: 0 5px;
		  max-width: 640px;
		  max-height: 480px;
                  width: auto !important;
		  height: auto !important;
		}

		div#my_camera {
			border: 1px solid #666;			
			background: url(data:image/png;base64,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) no-repeat center center;
		}
	</style>
	<!-- Configure a few settings and attach camera -->
	<script language="JavaScript">
		$(function () {
			Webcam.set({
				width: 640,
				height: 480,
				flip_horiz: false,
				dest_width: 640,
				dest_height: 480,
				image_format: 'jpeg',
				jpeg_quality: 85,
				enable_flash: false
				//swfURL: M.cfg.wwwroot + '/blocks/exacam/js/webcam.swf',
			});
			Webcam.attach('#my_camera');

			function webcam_error(err) {
				$('#exacam-error').html('<?php echo get_string('exacam:jswebcamerror', 'block_exacam');?>' + err);
			}

			Webcam.on('error', function (err) {
				webcam_error(err);
			});

			Webcam.on('live', function (err) {
				//$('#submit').show();
				$('#submit_idcheck').prop("disabled", false);
			});

			$('#submit_idcheck').click(function () {
				Webcam.snap(function (data_uri) {
					// snap complete, image data is in 'data_uri'

					Webcam.upload(data_uri, M.cfg.wwwroot + '/blocks/exacam/upload.php?fp=quizstart&cmid=' + block_exacam.get_param('cmid'), function (code, text) {
						// Upload complete!
						// 'code' will be the HTTP response code from the server, e.g. 200
						// 'text' will be the raw response content

						if (code != 200) {
							return webcam_error('<?php echo get_string('exacam:jssaveerror', 'block_exacam');?>');
						}

						console.log(text);
						if (text !== 'ok') {
							if (text.match(/\n/)) {
								return webcam_error('<?php echo get_string('exacam:jsunknownerror', 'block_exacam');?>');
							} else {
								return webcam_error(text);
							}
						}

						$('#idshotimg').attr('src', data_uri);
						$('#idshot').show();
						$('#submit').prop("disabled", false);
					});
				});
			});
			  
			$('#submit').click(function () {
				Webcam.snap(function (data_uri) {
					// snap complete, image data is in 'data_uri'

					Webcam.upload(data_uri, M.cfg.wwwroot + '/blocks/exacam/upload.php?fp=quizstart&cmid=' + block_exacam.get_param('cmid'), function (code, text) {
						// Upload complete!
						// 'code' will be the HTTP response code from the server, e.g. 200
						// 'text' will be the raw response content

						if (code != 200) {
							return webcam_error('<?php echo get_string('exacam:jssaveerror', 'block_exacam');?>');
						}

						console.log(text);
						if (text !== 'ok') {
							if (text.match(/\n/)) {
								return webcam_error('<?php echo get_string('exacam:jsunknownerror', 'block_exacam');?>');
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
		<h3><?php echo get_string('exacam:popuph3', 'block_exacam');?><sup>&nbsp;<a href="#fnt1" id="fn1">[1]</a></sup></h3>
                <div id="preview">
		  <div id="idshot" class="column">
                    <img id="idshotimg" src=""/>
                  </div>
		  <div id="my_camera" class="column"></div>
		</div>
		<div id="description">
		  <div id="btnwrapper">
		    <div class="column">
		      <input type=button value="<?php echo get_string('exacam:idcheck', 'block_exacam');?>" id="submit_idcheck" disabled="true" /> <!--style="display: none;"-->
		    </div>
		    <div class="column">
		      <input type=button value="<?php echo get_string('exacam:icanseeme', 'block_exacam');?>" id="submit" disabled="true" /> <!--style="display: none;"-->
		    </div>
		  </div>
		  <div id="exacam-error" style="color: red; font-weight: bold;"></div>
                  <div id="exacam-dsgvo"><?php echo get_string('exacam:dsgvo', 'block_exacam');?></div>
		  <ol id="checklist">
		    <li>
                      <span><?php echo get_string('exacam:checkdesc_ol1', 'block_exacam');?></span>
		    </li>
		    <li>
                      <span><?php echo get_string('exacam:checkdesc_ol2', 'block_exacam');?></span>
		    </li>
                    <li>
                      <span><?php echo get_string('exacam:checkdesc_ol3', 'block_exacam');?></span>
                    </li>
	          </ol>
		  <div><?php echo get_string('exacam:faqs', 'block_exacam');?></div>
		  <div id="desctxt"><sup><a href="#fn1" id="fnt1">[1]</a></sup>&nbsp;<?php echo get_string('exacam:checkdesc', 'block_exacam');?></div>
		</div>
	</center>
<?php

echo $OUTPUT->footer();
