<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');

$replacements = [
    '#0d4f8b' => '#c62b3a',
    '#0d4f8b' => '#c62b3a',
    '#0D4F8B' => '#c62b3a',
    '#0c4880' => '#a32230',
    '#0C4880' => '#a32230',
    '#083259' => '#8d182a',
    '#052040' => '#4b0f18',
    '#052040' => '#4b0f18',
    '#063050' => '#6f1520',
    '#0a3d6b' => '#9e1b2e',
    '#0a3d6b' => '#9e1b2e',
    '#0A3D6B' => '#9e1b2e',
    '#2d7dd2' => '#d63d4d',
    '#2D7DD2' => '#d63d4d',
    '#4d9de0' => '#e87a84',
    '#4D9DE0' => '#e87a84',
    '#eff5ff' => '#fff5f6',
    '#eff5ff' => '#fff5f6',
    '#EFF5FF' => '#fff5f6'
];

foreach ($replacements as $search => $replace) {
    $scss = str_ireplace($search, $replace, $scss);
}

// Ensure database configuration fields are also set to vinotinto
set_config('brandcolor',         '#c62b3a', 'theme_moove');
set_config('buttonbrandcolor',   '#c62b3a', 'theme_moove');
set_config('linkcolor',          '#c62b3a', 'theme_moove');
set_config('secondarymenucolor', '#c62b3a', 'theme_moove');

set_config('scss', $scss, 'theme_moove');
echo "Colors replaced successfully in theme Moove SCSS.\n";

theme_reset_all_caches();
purge_all_caches();
echo "Theme caches reset and all caches purged.\n";
