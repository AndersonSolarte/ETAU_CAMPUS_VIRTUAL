<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/edit_form.php');

global $DB, $PAGE;

$course = $DB->get_record('course', array('id' => 17));
if (!$course) {
    echo "Course 17 not found!\n";
    exit(1);
}

$category = $DB->get_record('course_categories', array('id' => $course->category));
$context = context_course::instance($course->id);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/course/edit.php', array('id' => $course->id)));

$form = new course_edit_form(null, array(
    'course' => $course,
    'category' => $category,
    'editoroptions' => array()
));

ob_start();
$form->display();
$html = ob_get_clean();

// Find all fieldsets in the HTML
preg_match_all('/<fieldset[^>]*>/i', $html, $matches);
echo "Fieldset tags found:\n";
foreach ($matches[0] as $match) {
    echo $match . "\n";
}

// Find a specific fieldset container and print its inner wrapper
if (preg_match('/<fieldset[^>]*id="id_category_1"[^>]*>(.*?)<\/fieldset>/is', $html, $m)) {
    echo "\nContent wrapper of id_category_1:\n";
    echo substr($m[1], 0, 1500) . "...\n";
} else {
    // Try printing a portion of the first fieldset found
    if (preg_match('/<fieldset[^>]*class="[^"]*collapsible[^"]*"[^>]*>(.*?)<\/fieldset>/is', $html, $m)) {
        echo "\nContent wrapper of first collapsible fieldset:\n";
        echo substr($m[1], 0, 1500) . "...\n";
    }
}
