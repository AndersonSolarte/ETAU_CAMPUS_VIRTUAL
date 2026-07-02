<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$user = $DB->get_record_select('user', "firstname LIKE '%Gestión%' AND lastname LIKE '%Planeación%'");
if ($user) {
    $student_role = $DB->get_record('role', ['shortname' => 'student']);
    $teacher_role = $DB->get_record('role', ['shortname' => 'editingteacher']);
    
    // In Urbanismo (47)
    $context = context_course::instance(47);
    
    // Unassign teacher
    role_unassign($teacher_role->id, $user->id, $context->id);
    
    // Assign student
    role_assign($student_role->id, $user->id, $context->id);
    
    echo "User {$user->firstname} is now a STUDENT again in Urbanismo.\n";
} else {
    echo "User not found.\n";
}
