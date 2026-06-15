<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$email = 'adsolarte@unicesmag.edu.co';

// Find user by email
$user = $DB->get_record('user', ['email' => $email, 'deleted' => 0]);

if (!$user) {
    echo "User not found: $email" . PHP_EOL;
    echo "Listing all OAuth2 users to find the right account..." . PHP_EOL;
    $users = $DB->get_records_sql(
        "SELECT u.id, u.username, u.email, u.firstname, u.lastname, u.auth
         FROM {user} u WHERE u.deleted=0 AND u.auth='oauth2' ORDER BY u.id"
    );
    foreach ($users as $u) {
        echo "  ID:{$u->id} | {$u->email} | {$u->firstname} {$u->lastname} | auth:{$u->auth}" . PHP_EOL;
    }
    echo PHP_EOL . "Run again with the correct email or update script." . PHP_EOL;
    exit(1);
}

echo "User found: ID={$user->id} | {$user->firstname} {$user->lastname} | {$user->email}" . PHP_EOL;

// Get current siteadmins
$current_admins = get_config('core', 'siteadmins');
$admin_ids = array_filter(explode(',', $current_admins));

if (in_array((string)$user->id, $admin_ids)) {
    echo "User is ALREADY a site administrator." . PHP_EOL;
} else {
    $admin_ids[] = $user->id;
    $new_admins = implode(',', $admin_ids);
    set_config('siteadmins', $new_admins);
    echo "OK - User added as site administrator!" . PHP_EOL;
    echo "siteadmins: " . $new_admins . PHP_EOL;
}

purge_caches();
echo PHP_EOL . "Done. Try logging in with $email" . PHP_EOL;
