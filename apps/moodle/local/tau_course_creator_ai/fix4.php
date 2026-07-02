<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$courses = $DB->get_records('course');
foreach ($courses as $c) {
    if ($c->id == 1) continue;
    $sections = $DB->get_records('course_sections', ['course' => $c->id], 'section ASC');
    $academic_count = 0;
    foreach ($sections as $sec) {
        $title = mb_strtolower(trim(strip_tags($sec->name)));
        $is_general = ($sec->section == 0 || $title === 'general' || $title === 'información general' || $title === 'informacion general');
        
        if ($is_general) {
            $DB->set_field('course_sections', 'visible', 1, ['id' => $sec->id]);
        } else {
            $academic_count++;
            if ($academic_count === 1) {
                $DB->set_field('course_sections', 'visible', 1, ['id' => $sec->id]);
            } else {
                $DB->set_field('course_sections', 'visible', 0, ['id' => $sec->id]);
            }
        }
    }
}
rebuild_course_cache(0, true);
echo "Hidden sections applied to all existing courses.\n";
