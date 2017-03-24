<?php

$capabilities = array(
	/*
	'block/exacam:use' => array(
		'captype' => 'read', // needs to be read, else guest users can't access the library
		'contextlevel' => CONTEXT_SYSTEM,
		'legacy' => array(
			'user' => CAP_ALLOW,
		),
	),
	*/
	'block/exacam:addinstance' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
		'archetypes' => array(
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW,
		),
		'clonepermissionsfrom' => 'moodle/site:manageblocks',
	),
	'block/exacam:myaddinstance' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_SYSTEM,
		'archetypes' => array(
			'user' => CAP_PREVENT,
		),
		'clonepermissionsfrom' => 'moodle/my:manageblocks',
	),
);
