<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $USER;
$user = \core_user::get_user(2);
\core\session\manager::set_user($user);

// Let's just output the user menu HTML directly
$usermenu = new \core_user_menu();
// But wait, core_user_menu is not a standard class.
