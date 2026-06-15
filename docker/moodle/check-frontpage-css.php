<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$css = file_get_contents('http://127.0.0.1/local/tau_course_creator_ai/assets/css/tau_frontpage.css?v=20260615g');
echo "CSS Length: " . strlen($css) . "\n";
echo "First 200 chars:\n" . substr($css, 0, 200) . "\n";
echo "Position of .tau-logo-deco: " . strpos($css, '.tau-logo-deco') . "\n";
