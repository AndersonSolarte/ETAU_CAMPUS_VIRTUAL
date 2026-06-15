<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Check active blocks
echo "=== Active blocks ===" . PHP_EOL;
$blocks = $DB->get_records('block_instances');
foreach ($blocks as $b) {
    echo "  " . $b->blockname . " (page: " . $b->pagetypepattern . ")" . PHP_EOL;
}

// Check if tau blocks exist and have issues
echo PHP_EOL . "=== TAU plugins installed ===" . PHP_EOL;
$plugins = $DB->get_records_sql(
    "SELECT plugin, value FROM {config_plugins} WHERE plugin LIKE 'block_tau%' OR plugin LIKE 'local_tau%' LIMIT 20"
);
foreach ($plugins as $p) {
    echo "  " . $p->plugin . PHP_EOL;
}

// Check auth config
echo PHP_EOL . "=== Auth config ===" . PHP_EOL;
$auth = get_config('core', 'auth');
echo "Active auth: " . $auth . PHP_EOL;
