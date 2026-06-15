<?php
/**
 * CLI script — Crea el curso "Introducción al Big Data" (BIGDATA101)
 * a partir del blueprint JSON en la raíz del proyecto.
 *
 * Uso desde dentro del contenedor:
 *   php /scripts/create-bigdata101.php
 *   php /scripts/create-bigdata101.php --category="Ingeniería y Tecnología"
 *   php /scripts/create-bigdata101.php --dry-run
 *
 * Uso desde el host (Bash tool / PowerShell):
 *   docker exec tau_moodle php /scripts/create-bigdata101.php
 */
declare(strict_types=1);
define('CLI_SCRIPT', true);

// ── Bootstrap Moodle ─────────────────────────────────────────────────────────

$config_path = '/var/www/html/config.php';
if (!file_exists($config_path)) {
    fwrite(STDERR, "ERROR: config.php not found at {$config_path}. Is Moodle installed?\n");
    exit(1);
}
require($config_path);
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/local/tau_course_creator_ai/classes/course_builder.php');

// ── Parse CLI arguments ───────────────────────────────────────────────────────

$opts = getopt('', ['category:', 'dry-run', 'force', 'blueprint:']);
$category_name = $opts['category'] ?? 'Ingeniería y Tecnología';
$dry_run       = isset($opts['dry-run']);
$force_rebuild = isset($opts['force']);
$blueprint_path = $opts['blueprint'] ?? '/project/BIGDATA101-blueprint.json';

// Fallback blueprint paths (inside container the project root is mounted differently)
$blueprint_candidates = [
    $blueprint_path,
    '/var/www/html/../../BIGDATA101-blueprint.json',
    '/project/BIGDATA101-blueprint.json',
    dirname(__DIR__, 2) . '/BIGDATA101-blueprint.json',
];
$blueprint_file = null;
foreach ($blueprint_candidates as $candidate) {
    if (file_exists($candidate)) {
        $blueprint_file = $candidate;
        break;
    }
}

if (!$blueprint_file) {
    // Last resort: check the scripts dir itself
    $scripts_dir = dirname(__FILE__);
    $alt = $scripts_dir . '/../../BIGDATA101-blueprint.json';
    if (file_exists(realpath($alt) ?: $alt)) {
        $blueprint_file = realpath($alt) ?: $alt;
    }
}

if (!$blueprint_file) {
    fwrite(STDERR, "ERROR: BIGDATA101-blueprint.json not found.\n");
    fwrite(STDERR, "Candidates checked:\n");
    foreach ($blueprint_candidates as $c) {
        fwrite(STDERR, "  {$c}\n");
    }
    fwrite(STDERR, "Mount the project root into the container or pass --blueprint=/path/to/file.json\n");
    exit(1);
}

// ── Load blueprint ────────────────────────────────────────────────────────────

$json_raw  = file_get_contents($blueprint_file);
$blueprint = json_decode($json_raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDERR, "ERROR: Invalid JSON in blueprint: " . json_last_error_msg() . "\n");
    exit(1);
}

log_msg("Blueprint loaded: {$blueprint_file}");
log_msg("Course name    : " . ($blueprint['courseName'] ?? 'N/A'));
log_msg("Short name     : " . ($blueprint['courseShortName'] ?? 'auto'));
log_msg("Sections       : " . count($blueprint['sections'] ?? []));
$total_activities = array_sum(array_map(fn($s) => count($s['activities'] ?? []), $blueprint['sections'] ?? []));
log_msg("Total activities: {$total_activities}");
log_msg("Category       : {$category_name}");

if ($dry_run) {
    log_msg("\n[DRY RUN] No changes will be made to the database.");
    log_section_summary($blueprint['sections'] ?? []);
    exit(0);
}

// ── Set admin user context ────────────────────────────────────────────────────

$admin = get_admin();
if (!$admin) {
    fwrite(STDERR, "ERROR: Admin user not found.\n");
    exit(1);
}
$USER = $admin;
\core\session\manager::set_user($admin);

// ── Find or create course category ───────────────────────────────────────────

$category = $DB->get_record('course_categories', ['name' => $category_name]);
if (!$category) {
    log_msg("Category '{$category_name}' not found — creating it...");
    $cat = core_course_category::create([
        'name'    => $category_name,
        'parent'  => 0,
        'visible' => 1,
    ]);
    $category_id = $cat->id;
    log_msg("  [ok] Category created (id={$category_id})");
} else {
    $category_id = (int) $category->id;
    log_msg("  [ok] Category found (id={$category_id})");
}

// ── Check for existing course ─────────────────────────────────────────────────

$shortname     = $blueprint['courseShortName'] ?? 'BIGDATA101';
$existing      = $DB->get_record('course', ['shortname' => $shortname]);

if ($existing && !$force_rebuild) {
    log_msg("\nCourse '{$shortname}' already exists (id={$existing->id}).");
    log_msg("Use --force to delete and rebuild it, or rename courseShortName in the blueprint.");
    log_msg("Course URL: {$CFG->wwwroot}/course/view.php?id={$existing->id}");
    exit(0);
}

if ($existing && $force_rebuild) {
    log_msg("\n[FORCE] Deleting existing course id={$existing->id}...");
    delete_course($existing, false);
    log_msg("  [ok] Old course deleted.");
}

// ── Build the course ──────────────────────────────────────────────────────────

log_msg("\nBuilding course from blueprint...");
$start = microtime(true);

$builder = new \local_tau_course_creator_ai\course_builder(function(string $msg) {
    log_msg("  > {$msg}");
});

try {
    $course_id = $builder->build($blueprint, $category_id);
} catch (\Throwable $e) {
    fwrite(STDERR, "\nFATAL ERROR during course build:\n");
    fwrite(STDERR, $e::class . ': ' . $e->getMessage() . "\n");
    fwrite(STDERR, $e->getTraceAsString() . "\n");
    exit(1);
}

$elapsed = round(microtime(true) - $start, 1);

// ── Post-build configuration ──────────────────────────────────────────────────

log_msg("\nApplying post-build configuration...");

// Enable course completion
$DB->set_field('course', 'enablecompletion', 1, ['id' => $course_id]);
$DB->set_field('course', 'showgrades', 1, ['id' => $course_id]);
$DB->set_field('course', 'showreports', 1, ['id' => $course_id]);
$DB->set_field('course', 'lang', 'es', ['id' => $course_id]);

// Set course dates (16-week semester starting next Monday)
$start_date = strtotime('next Monday');
$end_date   = $start_date + (16 * 7 * 24 * 3600);
$DB->set_field('course', 'startdate', $start_date, ['id' => $course_id]);
$DB->set_field('course', 'enddate',   $end_date,   ['id' => $course_id]);

log_msg("  [ok] Completion tracking enabled");
log_msg("  [ok] Semester dates set: " . date('Y-m-d', $start_date) . " → " . date('Y-m-d', $end_date));

// Configure grading: Topics format already set by builder, verify
$course_record = $DB->get_record('course', ['id' => $course_id]);
log_msg("  [ok] Course format: " . $course_record->format);

// Rebuild course cache
rebuild_course_cache($course_id, true);
log_msg("  [ok] Course cache rebuilt");

// ── Summary report ────────────────────────────────────────────────────────────

$sections = $DB->get_records('course_sections', ['course' => $course_id], 'section ASC');
$modules  = $DB->get_records_sql(
    'SELECT cm.id, m.name AS modname, cm.section, cm.visible
       FROM {course_modules} cm
       JOIN {modules} m ON m.id = cm.module
      WHERE cm.course = ?
      ORDER BY cm.section, cm.id',
    [$course_id]
);

$modcount_by_type = [];
foreach ($modules as $m) {
    $modcount_by_type[$m->modname] = ($modcount_by_type[$m->modname] ?? 0) + 1;
}

log_msg("\n" . str_repeat('─', 60));
log_msg("COURSE BUILT SUCCESSFULLY in {$elapsed}s");
log_msg(str_repeat('─', 60));
log_msg("Course ID    : {$course_id}");
log_msg("Short name   : " . $course_record->shortname);
log_msg("Full name    : " . $course_record->fullname);
log_msg("Sections     : " . count($sections));
log_msg("Modules      : " . count($modules));
log_msg("URL          : {$CFG->wwwroot}/course/view.php?id={$course_id}");
log_msg("Gradebook    : {$CFG->wwwroot}/grade/report/grader/index.php?id={$course_id}");
log_msg("Completion   : {$CFG->wwwroot}/course/completion.php?id={$course_id}");
log_msg("");
log_msg("Module breakdown:");
foreach ($modcount_by_type as $type => $count) {
    log_msg(sprintf("  %-12s %d", $type, $count));
}
log_msg(str_repeat('─', 60));

log_msg("\nNEXT STEPS:");
log_msg("1. Import H5P content files to the Content Bank:");
log_msg("   {$CFG->wwwroot}/contentbank/index.php");
log_msg("2. Edit the H5P placeholder activities to link Content Bank items:");
log_msg("   Sections 2 and 3 contain H5P placeholder activities.");
log_msg("3. Set up grade weights in the gradebook:");
log_msg("   {$CFG->wwwroot}/grade/edit/tree/index.php?id={$course_id}");
log_msg("4. Configure course completion criteria:");
log_msg("   {$CFG->wwwroot}/course/completion.php?id={$course_id}");
log_msg("5. Set up the TAU certificate for this course:");
log_msg("   {$CFG->wwwroot}/local/tau_certificate/view.php?courseid={$course_id}");
log_msg("6. Add Smart Rules for automated alerts:");
log_msg("   {$CFG->wwwroot}/local/tau_smart_rules_ai/index.php?courseid={$course_id}");
log_msg("7. Enroll teachers and students:");
log_msg("   {$CFG->wwwroot}/enrol/users.php?id={$course_id}");

exit(0);

// ── Helpers ───────────────────────────────────────────────────────────────────

function log_msg(string $msg): void {
    fwrite(STDOUT, $msg . "\n");
}

function log_section_summary(array $sections): void {
    log_msg("\nSection structure preview:");
    foreach ($sections as $i => $section) {
        $activities = $section['activities'] ?? [];
        log_msg(sprintf("  [%d] %s (%d activities)", $i + 1, $section['title'] ?? 'Sin título', count($activities)));
        foreach ($activities as $a) {
            log_msg(sprintf("       %-14s %s", ($a['type'] ?? 'label') . ':', $a['title'] ?? ''));
        }
    }
}
