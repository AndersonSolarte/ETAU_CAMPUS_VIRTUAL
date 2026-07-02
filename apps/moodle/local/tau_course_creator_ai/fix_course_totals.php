<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/gradelib.php');

global $DB;

$courses = $DB->get_records('course');
foreach ($courses as $c) {
    if ($c->id == 1) continue;
    
    $course_item = grade_item::fetch_course_item($c->id);
    if ($course_item) {
        $course_item->grademax = 5.00000;
        $course_item->grademin = 0.00000;
        $course_item->update();
        
        // Ensure the category max is also 5
        $category = grade_category::fetch_course_category($c->id);
        if ($category) {
            $cat_item = $category->get_grade_item();
            if ($cat_item) {
                $cat_item->grademax = 5.00000;
                $cat_item->update();
            }
        }
    }
    
    grade_regrade_final_grades($c->id);
    echo "Fixed course total max grade for course {$c->id}\n";
}
