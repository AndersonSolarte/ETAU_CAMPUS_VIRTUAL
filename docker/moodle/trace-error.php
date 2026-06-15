<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Trigger the front page rendering to see where the error occurs
// Check Moodle's error table
echo "=== Recent Moodle errors from DB ===" . PHP_EOL;
try {
    $errors = $DB->get_records_sql(
        "SELECT * FROM {task_log} ORDER BY timestart DESC LIMIT 5"
    );
    foreach ($errors as $e) {
        echo $e->classname . " | " . ($e->result == 1 ? 'OK' : 'FAIL') . PHP_EOL;
    }
} catch(Exception $e) {
    echo "task_log error: " . $e->getMessage() . PHP_EOL;
}

// Check PHP error log from Apache/PHP inside container
$paths = [
    '/var/log/apache2/error.log',
    '/var/log/php/error.log',
    '/tmp/php_error.log',
    '/proc/1/fd/2',
];
foreach ($paths as $p) {
    if (file_exists($p) && is_readable($p)) {
        echo PHP_EOL . "=== $p (last 30 lines) ===" . PHP_EOL;
        $lines = array_slice(file($p), -30);
        foreach ($lines as $l) {
            if (strpos($l, 'stdClass') !== false || strpos($l, 'Exception') !== false || strpos($l, 'Error') !== false) {
                echo $l;
            }
        }
        break;
    }
}

// Check the front page slider config that might cause stdClass error
echo PHP_EOL . "=== Moove slider config ===" . PHP_EOL;
$count = get_config('theme_moove', 'slidercount');
echo "slidercount: " . var_export($count, true) . PHP_EOL;
$img1 = get_config('theme_moove', 'sliderimage1');
echo "sliderimage1: " . var_export($img1, true) . PHP_EOL;
