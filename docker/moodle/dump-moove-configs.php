<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
global $DB;
$configs = $DB->get_records('config_plugins', ['plugin' => 'theme_moove']);
foreach ($configs as $c) {
    echo $c->name . ': ' . $c->value . PHP_EOL;
}
