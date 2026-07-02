<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$sections = $DB->get_records('course_sections', ['name' => 'Grabaciones de clase', 'visible' => 0]);
$count = 0;
foreach ($sections as $sec) {
    $sec->visible = 1;
    $DB->update_record('course_sections', $sec);
    rebuild_course_cache($sec->course, true);
    $count++;
}
echo "Fixed $count hidden sections.\n";
