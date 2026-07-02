<?php
require_once(__DIR__ . '/../../config.php');
global $DB;

// Try to find the user from the mockup config or fallback to admin
$user = $DB->get_record('user', ['email' => 'adsolarte@unicesmag.edu.co']);
if (!$user) {
    $user = $DB->get_record('user', ['id' => 2]); // Usually the main admin
}

if ($user) {
    complete_user_login($user);
    echo "LOGGED_IN_AS: " . $user->username . " (ID: " . $user->id . ")";
} else {
    echo "NO_USER_FOUND";
}
