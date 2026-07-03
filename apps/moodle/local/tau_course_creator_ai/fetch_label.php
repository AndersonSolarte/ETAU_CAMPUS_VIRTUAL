<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$labels = $DB->get_records_sql("SELECT id, intro FROM {course_modules} cm JOIN {label} l ON l.id = cm.instance WHERE l.intro LIKE '%E-TAU CAMPUS VIRTUAL%'");
foreach ($labels as $l) {
    echo "ID: " . $l->id . "\n";
    echo $l->intro . "\n";
    break;
}
