<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
global $DB;
$blocks = $DB->get_records('block_instances');
foreach ($blocks as $b) {
    echo "Block: {$b->blockname} | Page: {$b->pagetype} | Region: {$b->defaultregion}" . PHP_EOL;
}
