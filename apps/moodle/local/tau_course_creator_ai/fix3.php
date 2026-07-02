<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$users = $DB->get_records_select('user', "firstname LIKE '%Gestión de Procesos%' OR lastname LIKE '%Planeación%'");
$student_role = $DB->get_record('role', ['shortname' => 'student']);
$teacher_role = $DB->get_record('role', ['shortname' => 'editingteacher']);

foreach ($users as $user) {
    echo "Cleaning up roles for: {$user->firstname} {$user->lastname}\n";
    $courses = $DB->get_records('course');
    foreach ($courses as $c) {
        if ($c->id == 1) continue;
        $context = context_course::instance($c->id);
        
        // Unassign student role completely from this user in this course context
        role_unassign($student_role->id, $user->id, $context->id);
        
        // Ensure they have the teacher role
        role_assign($teacher_role->id, $user->id, $context->id);
    }
}
echo "Done cleaning up roles.\n";
