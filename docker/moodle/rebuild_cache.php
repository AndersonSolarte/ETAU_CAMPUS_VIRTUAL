<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');
global $DB;

$courses = $DB->get_records('course', ['id' => 47]);
foreach ($courses as $c) {
    rebuild_course_cache($c->id, true);
    echo "Rebuilt cache for course $c->id\n";
}

$all_courses = $DB->get_records_select('course', 'id > 1');
foreach ($all_courses as $c) {
    rebuild_course_cache($c->id, true);
}
echo "Rebuilt cache for all courses.\n";
