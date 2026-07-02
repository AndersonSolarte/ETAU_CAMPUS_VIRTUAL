<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$users = $DB->get_records_select('user', "firstname LIKE '%Gestión de Procesos%' OR lastname LIKE '%Planeación%'");
$role = $DB->get_record('role', ['shortname' => 'editingteacher']);

foreach ($users as $user) {
    echo "Enrolling: {$user->firstname} {$user->lastname}\n";
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
}
echo "Done.\n";
