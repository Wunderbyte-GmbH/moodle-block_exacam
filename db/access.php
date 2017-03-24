<?php

$capabilities = array(
	/*
	'block/dukcam:use' => array(
		'captype' => 'read', // needs to be read, else guest users can't access the library
		'contextlevel' => CONTEXT_SYSTEM,
		'legacy' => array(
			'user' => CAP_ALLOW,
		),
	),
	*/
	'block/dukcam:addinstance' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
		'archetypes' => array(
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW,
		),
		'clonepermissionsfrom' => 'moodle/site:manageblocks',
	),
	'block/dukcam:myaddinstance' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_SYSTEM,
		'archetypes' => array(
			'user' => CAP_PREVENT,
		),
		'clonepermissionsfrom' => 'moodle/my:manageblocks',
	),
);
