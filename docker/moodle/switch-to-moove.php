<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Switch active theme to Moove
set_config('theme', 'moove');
echo "Theme switched to: moove" . PHP_EOL;

// Purge theme caches so the change takes effect immediately
theme_reset_all_caches();
echo "Theme caches purged" . PHP_EOL;

echo PHP_EOL . "Moodle now uses Moove theme." . PHP_EOL;
echo "Run configure-moove.php next to apply TAU branding." . PHP_EOL;
