<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');
echo "Initial SCSS length: " . strlen($scss) . "\n";
echo "Initial count of #0d4f8b: " . substr_count(strtolower($scss), '#0d4f8b') . "\n";

$scss = str_ireplace('#0d4f8b', '#c62b3a', $scss);
$scss = str_ireplace('#0c4880', '#a32230', $scss);
$scss = str_ireplace('#083259', '#8d182a', $scss);
$scss = str_ireplace('#052040', '#4b0f18', $scss);
$scss = str_ireplace('#063050', '#6f1520', $scss);
$scss = str_ireplace('#0a3d6b', '#9e1b2e', $scss);
$scss = str_ireplace('#2d7dd2', '#d63d4d', $scss);
$scss = str_ireplace('#4d9de0', '#e87a84', $scss);
$scss = str_ireplace('#eff5ff', '#fff5f6', $scss);

echo "Modified SCSS length: " . strlen($scss) . "\n";
echo "Modified count of #0d4f8b: " . substr_count(strtolower($scss), '#0d4f8b') . "\n";

set_config('scss', $scss, 'theme_moove');
$check = get_config('theme_moove', 'scss');
echo "Check from DB count of #0d4f8b: " . substr_count(strtolower($check), '#0d4f8b') . "\n";
