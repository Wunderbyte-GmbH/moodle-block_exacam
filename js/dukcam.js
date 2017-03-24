!function () {

	var block_dukcam = window.block_dukcam = {
		popup_iframe: function (config) {

			// allow passing of an url
			if (typeof config == 'string') {
				config = {
					url: config
				};
			}

			var popup = this.last_popup = new M.core.dialogue({
				headerContent: config.headerContent || config.title || 'Popup', // M.str.moodle.loadinghelp, // previousimagelink + '<div id=\"imagenumber\" class=\"imagetitle\"><h1> Image '
				// + screennumber + ' / ' + this.imageidnumbers[imageid] + ' </h1></div>' + nextimagelink,

				bodyContent: '<iframe src="' + config.url + '" width="100%" height="100%" frameborder="0"></iframe>',
				visible: true, //by default it is not displayed
				modal: false, // sollte true sein, aber wegen moodle bug springt dann das fenster immer nach oben
				zIndex: 1000,
				// ok: width: '80%',
				// ok: width: '500px',
				// ok: width: null, = automatic
				height: config.height || '80%',
				width: config.width || '85%',
			});

			// disable scrollbars
			$(window).disablescroll();

			// hack my own overlay, because moodle dialogue modal is not working
			var overlay = $('<div style="opacity:0.7; filter: alpha(opacity=20); background-color:#000; width:100%; height:100%; z-index:10; top:0; left:0; position:fixed;"></div>')
				.appendTo('body');
			// hide popup when clicking overlay
			overlay.click(function () {
				popup.hide();
			});

			popup.justHide = popup.hide;
			var orig_hide = popup.hide;
			popup.hide = function () {

				if (config.onhide) {
					config.onhide();
				}

				// remove overlay, when hiding popup
				overlay.remove();

				// enable scrolling
				$(window).disablescroll('undo');

				// call original popup.hide()
				orig_hide.call(popup);
			};

			popup.remove = function () {
				if (this.$body.is(':visible')) {
					this.hide();
				}

				this.destroy();
			};

			return popup;
		},

		body_param: function (param) {
			return $('body').attr('class').replace(new RegExp('^(.*\\s)?' + param + '-([^\\s]+)(\\s.*)?$'), '$2');
		}
	};

	$(document).on('submit', 'form[action*="startattempt.php"]', function (e) {
		e.preventDefault();

		var form = this;
		var popup;

		window.dukcam_webcamtest_finished = function () {
			popup.justHide();
			form.submit();
		};

		popup = block_dukcam.popup_iframe({
			url: M.cfg.wwwroot + '/blocks/dukcam/quizstart.php?courseid=' + block_dukcam.body_param('course')
		});
	});

	$(function () {
		if ($('body#page-mod-quiz-attempt').length) {
			var layer = $('<div id="my_camera" style="position: fixed; top: 0; right: 0; width: 100px; height: 100px; border: 1px solid black; background: white; z-index: 100000"></div>');

			layer.appendTo('body');

			Webcam.set({
				width: 100,
				height: 100,
				dest_width: 640,
				dest_height: 480,
				image_format: 'jpeg',
				jpeg_quality: 85
			});
			Webcam.attach('#my_camera');

			Webcam.on('live', function () {
				// camera is live, showing preview image
				// (and user has allowed access)

				function snap() {
					console.log('snap');
					Webcam.snap(function (data_uri) {
						// snap complete, image data is in 'data_uri'

						console.log('upload');
						Webcam.upload(data_uri, M.cfg.wwwroot + '/blocks/dukcam/upload.php?cmid=' + block_dukcam.body_param('cmid'), function (code, text) {
							console.log('ok');
							// Upload complete!
							// 'code' will be the HTTP response code from the server, e.g. 200
							// 'text' will be the raw response content
						});

					});
				}

				snap();
				window.setInterval(snap, 10000);
			});
		}
	});
}();