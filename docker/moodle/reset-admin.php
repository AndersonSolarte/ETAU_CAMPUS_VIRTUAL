<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$user = $DB->get_record('user', ['username' => 'admin']);
if (!$user) {
    // Try to find first site admin
    $admins = get_admins();
    $user = reset($admins);
}
if ($user) {
    $user->password = hash_internal_user_password('Admin@2026!');
    $DB->update_record('user', $user);
    echo "OK - Password set to: Admin@2026!" . PHP_EOL;
    echo "Username: " . $user->username . PHP_EOL;
    echo "Login at: http://localhost:8080/login/index.php" . PHP_EOL;
} else {
    echo "ERROR: No admin user found" . PHP_EOL;
}
