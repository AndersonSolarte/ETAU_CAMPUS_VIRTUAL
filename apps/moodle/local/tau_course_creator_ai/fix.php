<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$sql1 = "UPDATE {label} SET intro = REPLACE(intro, 'acompaÃ±arte', 'acompañarte')";
$DB->execute($sql1);
$sql2 = "UPDATE {label} SET intro = REPLACE(intro, 'cercanÃ­a', 'cercanía')";
$DB->execute($sql2);
$sql3 = "UPDATE {course_sections} SET summary = REPLACE(summary, 'acompaÃ±arte', 'acompañarte')";
$DB->execute($sql3);
$sql4 = "UPDATE {course_sections} SET summary = REPLACE(summary, 'cercanÃ­a', 'cercanía')";
$DB->execute($sql4);

$user = $DB->get_record_select('user', "firstname LIKE '%Gestión%' OR lastname LIKE '%Planeación%'", null, '*', IGNORE_MULTIPLE);
if ($user) {
    $role = $DB->get_record('role', ['shortname' => 'editingteacher']);
    $courses = $DB->get_records('course');
    foreach ($courses as $c) {
        if ($c->id == 1) continue;
        $context = context_course::instance($c->id);
        $enrol = $DB->get_record('enrol', ['courseid' => $c->id, 'enrol' => 'manual']);
        if ($enrol) {
            $plugin = enrol_get_plugin('manual');
            $plugin->enrol_user($enrol, $user->id, $role->id);
            role_assign($role->id, $user->id, $context->id);
        }
    }
    echo "User {$user->firstname} enrolled as editingteacher in all courses.\n";
} else {
    echo "User not found.\n";
}
echo "Done.\n";
