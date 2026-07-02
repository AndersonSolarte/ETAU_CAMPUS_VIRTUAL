<?php
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/tau_course_creator_ai/classes/course_builder.php');

global $DB;

$courseids = [];
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^\d+$/', $arg)) {
        $courseids[] = (int)$arg;
    }
}

if ($courseids) {
    list($insql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_QM);
    $courses = $DB->get_records_sql(
        "SELECT id, fullname, shortname
           FROM {course}
          WHERE id {$insql}
       ORDER BY id ASC",
        $params
    );
} else {
    $courses = $DB->get_records_sql(
        "SELECT id, fullname, shortname
           FROM {course}
       ORDER BY id ASC"
    );
}

$builder = new \local_tau_course_creator_ai\course_builder();
$migrated = 0;
$skipped = 0;

foreach ($courses as $course) {
    if ((int)$course->id === SITEID) {
        $skipped++;
        continue;
    }

    try {
        $changed = $builder->migrate_legacy_duplicate_general_section((int)$course->id);
        if ($changed) {
            $migrated++;
            fwrite(STDOUT, "[migrated] {$course->id} :: {$course->shortname} :: {$course->fullname}\n");
        } else {
            $skipped++;
        }
    } catch (\Throwable $e) {
        fwrite(STDERR, "[error] {$course->id} :: {$course->shortname} :: {$e->getMessage()}\n");
    }
}

fwrite(STDOUT, "Done. Migrated: {$migrated}. Skipped: {$skipped}.\n");
