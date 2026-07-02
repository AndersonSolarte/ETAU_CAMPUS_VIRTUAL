<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/gradelib.php');

global $DB;

// Ensure Simple Weighted Mean (2) is allowed in site settings
$config = get_config('moodle', 'grade_aggregations_visible');
if ($config !== false && strpos($config, '2') === false) {
    set_config('grade_aggregations_visible', $config . ',2', 'moodle');
}

$courses = $DB->get_records('course');
foreach ($courses as $c) {
    if ($c->id == 1) continue;
    
    $category = grade_category::fetch_course_category($c->id);
    if ($category) {
        $category->aggregation = 2; // Simple weighted mean
        $category->update();
        echo "Updated course {$c->id} gradebook to Simple Weighted Mean.\n";
    }
}
echo "Done.\n";
