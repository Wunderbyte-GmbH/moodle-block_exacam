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

define('AJAX_SCRIPT', true);

require __DIR__.'/inc.php';

require_login();

if (!@$_FILES['webcam']['tmp_name'] || filesize($_FILES['webcam']['tmp_name']) <= 1000) {
	die('no file');
}

// 20210105 harald.bamberger@donau-uni.ac.at begin
$fileprefix = optional_param('fp', '', PARAM_TEXT);
if( !empty($fileprefix) ) {
  if( $fileprefix !== 'quizstart' ) {
    $fileprefix = 'quizstart';
  }
  $fileprefix .= '_';
}
// 20210105 harald.bamberger@donau-uni.ac.at end

$filerecord = new stdClass();
$filerecord->contextid = context_module::instance(required_param('cmid', PARAM_INT))->id;
$filerecord->component = 'block_exacam';
$filerecord->filearea = 'quizshot';
$filerecord->filepath = '/';
//$filerecord->filename = time().'.jpg'; // original
$filerecord->filename = $fileprefix . time() . '.jpg';
$filerecord->itemid = $USER->id;
$filerecord->userid = $USER->id;

$fs = get_file_storage();
$fs->create_file_from_pathname($filerecord, $_FILES['webcam']['tmp_name']);

// move_uploaded_file($_FILES['webcam']['tmp_name'], __DIR__.'/temp/'.time().'.jpg');

echo 'ok';

