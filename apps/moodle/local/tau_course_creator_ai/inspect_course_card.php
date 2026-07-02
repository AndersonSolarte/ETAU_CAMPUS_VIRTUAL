<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB, $USER;
// Set a user
$user = \core_user::get_user(2); // Admin
\core\session\manager::set_user($user);

// Let's get the enrolled courses block HTML
// Actually, it's easier to just query the DOM or output something.
// But we can just grep the Moodle source code for the course card mustache template!
