<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid = 9; // Ciberseguridad
$course = get_course($courseid);

// ── 1. Quitar matrícula CESMAG (pago) ────────────────────────────────────────
$enrols = $DB->get_records('enrol', ['courseid' => $courseid, 'enrol' => 'cesmag']);
$plugin_cesmag = enrol_get_plugin('cesmag');
foreach ($enrols as $enrol) {
    $plugin_cesmag->delete_instance($enrol);
    echo "Matrícula CESMAG eliminada.\n";
}

// ── 2. Habilitar auto-matrícula gratuita ─────────────────────────────────────
$self_plugin = enrol_get_plugin('self');
$existing_self = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'self']);
if (!$existing_self) {
    $self_plugin->add_instance($course, [
        'status'          => ENROL_INSTANCE_ENABLED,
        'enrolperiod'     => 0,
        'roleid'          => 5, // student
        'customint6'      => 1, // allow new enrolments
    ]);
    echo "Auto-matrícula gratuita activada.\n";
} else {
    $DB->set_field('enrol', 'status', ENROL_INSTANCE_ENABLED, ['id' => $existing_self->id]);
    echo "Auto-matrícula ya existía — habilitada.\n";
}

// ── 3. Mantener matrícula manual para el admin ────────────────────────────────
$manual_plugin = enrol_get_plugin('manual');
if (!$DB->record_exists('enrol', ['courseid' => $courseid, 'enrol' => 'manual'])) {
    $manual_plugin->add_instance($course);
    echo "Matrícula manual añadida.\n";
}

rebuild_course_cache($courseid, true);
echo "Curso actualizado a gratuito.\n";
echo "URL: {$CFG->wwwroot}/course/view.php?id={$courseid}\n";
