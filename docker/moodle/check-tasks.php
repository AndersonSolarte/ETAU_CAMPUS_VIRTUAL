<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$tasks = $DB->get_records('task_adhoc');
if ($tasks) {
    echo "=== Current Ad-hoc Tasks ===" . PHP_EOL;
    foreach ($tasks as $t) {
        echo "ID: {$t->id} | Class: {$t->classname} | Fail delay: {$t->faildelay} | Next run: " . date('Y-m-d H:i:s', $t->nextruntime) . PHP_EOL;
    }
} else {
    echo "No ad-hoc tasks in queue." . PHP_EOL;
}
