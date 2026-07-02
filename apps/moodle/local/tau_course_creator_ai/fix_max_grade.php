<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/gradelib.php');

global $DB;

// Update max grades in the activity tables
$DB->execute("UPDATE {assign} SET grade = 5 WHERE grade > 5");
$DB->execute("UPDATE {quiz} SET grade = 5 WHERE grade > 5");
$DB->execute("UPDATE {forum} SET grade_forum = 5 WHERE grade_forum > 5");
$DB->execute("UPDATE {h5pactivity} SET grade = 5 WHERE grade > 5");

// Update grade items in the central gradebook
$DB->execute("UPDATE {grade_items} SET grademax = 5 WHERE grademax > 5 AND itemtype = 'mod'");

// Trigger a gradebook recalculation for all courses just to be safe
$courses = $DB->get_records('course');
foreach ($courses as $c) {
    if ($c->id == 1) continue;
    grade_regrade_final_grades($c->id);
    echo "Regraded course {$c->id}\n";
}
echo "Max grades updated to 5.\n";
