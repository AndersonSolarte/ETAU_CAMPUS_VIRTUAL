<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
global $DB;
$courses = $DB->get_records('course', [], '', 'id, hiddensections');
foreach ($courses as $c) {
    echo "Course {$c->id} hiddensections: {$c->hiddensections}\n";
}
