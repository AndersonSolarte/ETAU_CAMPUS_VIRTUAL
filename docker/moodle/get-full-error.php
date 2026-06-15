<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Get full Apache error log
$log = '/var/log/apache2/error.log';
if (file_exists($log)) {
    $content = file_get_contents($log);
    $lines   = explode("\n", $content);
    $last    = array_slice($lines, -60);
    echo implode("\n", $last);
} else {
    echo "Log not found: $log" . PHP_EOL;
    // Try stderr
    echo shell_exec('tail -60 /var/log/apache2/error.log 2>/dev/null || tail -60 /dev/stderr 2>/dev/null');
}
