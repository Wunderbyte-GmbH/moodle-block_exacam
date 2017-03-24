<?php

require __DIR__.'/inc.php';

require_login();

$filerecord = new stdClass();
$filerecord->contextid = context_module::instance(required_param('cmid', PARAM_INT))->id;
$filerecord->component = 'block_dukcam';
$filerecord->filearea = 'quizshot';
$filerecord->filepath = '/';
$filerecord->filename = time().'.jpg';
$filerecord->itemid = $USER->id;
$filerecord->userid = $USER->id;

$fs = get_file_storage();
$fs->create_file_from_pathname($filerecord, $_FILES['webcam']['tmp_name']);

// move_uploaded_file($_FILES['webcam']['tmp_name'], __DIR__.'/temp/'.time().'.jpg');

echo 'ok';
