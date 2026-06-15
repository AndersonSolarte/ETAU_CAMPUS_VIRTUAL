<?php
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/local/tau_course_creator_ai/classes/course_builder.php');

global $DB, $USER;

$admin = get_admin();
if (!$admin) {
    fwrite(STDERR, 'Admin user not found.' . PHP_EOL);
    exit(1);
}
$USER = $admin;
\core\session\manager::set_user($admin);

$timestamp = date('Ymd_His');
$coursename = '[TEST] Plantilla nativa ' . $timestamp;
$shortname = 'test_native_' . $timestamp;

$blueprint = [
    'courseName' => $coursename,
    'courseShortName' => $shortname,
    'courseDescription' => 'Curso de verificacion automatica para la plantilla nativa.',
    'sections' => [
        [
            'title' => 'Modulo 1 - Bienvenida',
            'summary' => 'Apertura y orientacion inicial.',
            'activities' => [
                ['type' => 'page', 'title' => 'Guia inicial', 'description' => 'Presentacion del modulo.'],
                ['type' => 'url', 'title' => 'Recurso externo', 'description' => 'Lectura complementaria.', 'externalurl' => 'https://example.com'],
            ],
        ],
        [
            'title' => 'Modulo 2 - Desarrollo',
            'summary' => 'Trabajo guiado del tema central.',
            'activities' => [
                ['type' => 'forum', 'title' => 'Foro base', 'description' => 'Discusion principal.'],
                ['type' => 'assign', 'title' => 'Entrega corta', 'description' => 'Actividad de evidencia.'],
            ],
        ],
    ],
];

$course = create_course((object)[
    'fullname' => $coursename,
    'shortname' => $shortname,
    'category' => 1,
    'summary' => 'Curso base para verificar la estructura.',
    'summaryformat' => FORMAT_HTML,
    'format' => 'topics',
    'numsections' => 2,
    'visible' => 0,
]);

$builder = new \local_tau_course_creator_ai\course_builder(function(string $message) {
    fwrite(STDOUT, $message . PHP_EOL);
});

$builder->apply_to_existing_course((int)$course->id, $blueprint);

$sections = $DB->get_records('course_sections', ['course' => $course->id], 'section ASC', 'section,name,summary');
$modules = $DB->get_records_sql(
    'SELECT cm.id, m.name AS modname, cm.section
       FROM {course_modules} cm
       JOIN {modules} m ON m.id = cm.module
      WHERE cm.course = ?
      ORDER BY cm.section, cm.id',
    [$course->id]
);

fwrite(STDOUT, PHP_EOL . 'Verification summary' . PHP_EOL);
fwrite(STDOUT, 'Course ID: ' . $course->id . PHP_EOL);
fwrite(STDOUT, 'Course name: ' . $course->fullname . PHP_EOL);

foreach ($sections as $section) {
    if ((int)$section->section === 0) {
        continue;
    }
    fwrite(STDOUT, 'Section ' . $section->section . ': ' . ($section->name ?: '[sin nombre]') . PHP_EOL);
}

foreach ($modules as $module) {
    fwrite(STDOUT, 'Module type ' . $module->modname . ' in section record ' . $module->section . PHP_EOL);
}

fwrite(STDOUT, 'Course module records: ' . count($modules) . PHP_EOL);
foreach (['page', 'url', 'forum', 'assign'] as $table) {
    if ($DB->get_manager()->table_exists($table)) {
        fwrite(STDOUT, $table . ' instances: ' . $DB->count_records($table, ['course' => $course->id]) . PHP_EOL);
    }
}

fwrite(STDOUT, PHP_EOL . 'Verification complete.' . PHP_EOL);
