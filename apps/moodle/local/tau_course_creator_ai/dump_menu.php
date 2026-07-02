<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $USER, $PAGE, $OUTPUT;
// Login as Anderson
$user = \core_user::get_user(2);
\core\session\manager::set_user($user);

$PAGE->set_url('/');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$usermenu = new \core\output\user_menu($USER);
echo $OUTPUT->render($usermenu);
