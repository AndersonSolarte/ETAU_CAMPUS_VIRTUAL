<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$courses = $DB->get_records_select('course', 'id > 1', [], 'id', 'id,fullname,shortname,category');
foreach ($courses as $c) {
    echo $c->id . ' | ' . $c->shortname . ' | ' . $c->fullname . "\n";
}
