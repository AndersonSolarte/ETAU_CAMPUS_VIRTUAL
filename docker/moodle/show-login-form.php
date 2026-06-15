<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Clear the head CSS injection so the manual login form is visible
set_config('additionalhtmlhead', '');
echo "OK - Manual login form is now visible." . PHP_EOL;
echo "Go to: http://localhost:8080/login/index.php" . PHP_EOL;
echo "Username: admin" . PHP_EOL;
echo "Password: Admin@2026!" . PHP_EOL;
