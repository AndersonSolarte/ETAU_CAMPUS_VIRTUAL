<?php
require(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT); // Course ID

require_login($id);
$context = context_course::instance($id);

// Check permissions
require_capability('moodle/restore:restorecourse', $context);

// Redirect to native Moodle restore page with the correct context ID
$url = new moodle_url('/backup/restorefile.php', ['contextid' => $context->id]);
redirect($url);
