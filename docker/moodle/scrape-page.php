<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Fetch frontpage HTML
$html = file_get_contents('http://127.0.0.1/');
echo "HTML Length: " . strlen($html) . "\n";

// Find all css link tags
preg_match_all('/<link[^>]+rel=["\']stylesheet["\'][^>]+>/i', $html, $matches);
echo "\n--- Stylesheets ---\n";
foreach ($matches[0] as $m) {
    echo $m . "\n";
}

// Find all scripts matching local/tau
preg_match_all('/<script[^>]+src=["\'][^"\']*tau[^"\']*["\'][^>]*>/i', $html, $matches_js);
echo "\n--- Custom Scripts ---\n";
foreach ($matches_js[0] as $m) {
    echo $m . "\n";
}
