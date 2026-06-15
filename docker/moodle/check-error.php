<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Check last Moodle errors/exceptions
$errors = $DB->get_records_sql(
    'SELECT id, component, message, url, timeoccurred
     FROM {tool_log_store_standard_log}
     WHERE action = \'error\'
     ORDER BY timeoccurred DESC LIMIT 10'
);
if ($errors) {
    foreach ($errors as $e) {
        echo $e->component . " | " . $e->message . PHP_EOL;
    }
} else {
    echo "No errors in log table." . PHP_EOL;
}

// Check debug log file
$logfile = $CFG->dataroot . '/php_errorlog';
if (file_exists($logfile)) {
    $lines = array_slice(file($logfile), -20);
    echo PHP_EOL . "=== Error log (last 20 lines) ===" . PHP_EOL;
    echo implode('', $lines);
} else {
    echo "No php_errorlog file at: " . $logfile . PHP_EOL;
}

// Check for stdClass error in blocks/plugins
echo PHP_EOL . "=== Checking active blocks on frontpage ===" . PHP_EOL;
$blocks = $DB->get_records('block_instances', ['pagetypepattern' => 'site-index']);
foreach ($blocks as $b) {
    echo "Block: " . $b->blockname . PHP_EOL;
}
