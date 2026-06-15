<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

$prefs = $DB->get_records('user_preferences', ['userid' => 2]);
echo "Admin Preferences:\n";
foreach ($prefs as $p) {
    if (strpos($p->name, 'edit') !== false || strpos($p->name, 'mode') !== false) {
        echo "  - Name: {$p->name} | Value: {$p->value}\n";
    }
}
