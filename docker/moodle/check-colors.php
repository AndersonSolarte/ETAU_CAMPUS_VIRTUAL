<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$keys = [
    'brandcolor',
    'buttonbrandcolor',
    'headerbg',
    'navbarbg',
    'secondarymenucolor',
    'logo',
    'favicon'
];

foreach ($keys as $k) {
    echo "$k: " . get_config('theme_moove', $k) . "\n";
}
