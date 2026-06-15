<?php
/**
 * Test script for the H5P automatic generator.
 *
 * Usage (from host):
 *   docker exec tau_moodle php /scripts/test_h5p_generator.php
 *   docker exec tau_moodle php /scripts/test_h5p_generator.php --type=CoursePresentation
 *   docker exec tau_moodle php /scripts/test_h5p_generator.php --ai --type=QuestionSet --topic="Big Data fundamentals"
 *
 * Modes:
 *   (no flags)  — Pipeline test: builds a .h5p file WITHOUT calling the AI and imports it to Content Bank.
 *   --ai        — Full test: calls the configured AI provider to generate real content.
 */
declare(strict_types=1);
define('CLI_SCRIPT', true);

require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');

// ── Helper ────────────────────────────────────────────────────────────────────

function tlog(string $msg): void {
    fwrite(STDOUT, $msg . "\n");
}

// ── Parse CLI args ────────────────────────────────────────────────────────────

$opts     = getopt('', ['ai', 'type:', 'topic:', 'quantity:', 'courseid:', 'provider:']);
$use_ai   = isset($opts['ai']);
$type     = $opts['type']     ?? 'QuestionSet';
$topic    = $opts['topic']    ?? 'Fundamentos del Big Data: las 5 Vs y sus aplicaciones industriales';
$quantity = (int) ($opts['quantity'] ?? 3);
$courseid = (int) ($opts['courseid'] ?? 0);
$provider = $opts['provider'] ?? null;  // null = use Moodle configured provider

// ── Bootstrap user ────────────────────────────────────────────────────────────

$admin = get_admin();
if (!$admin) {
    fwrite(STDERR, "Admin user not found.\n");
    exit(1);
}
$USER = $admin;
\core\session\manager::set_user($admin);

// ── Choose context ────────────────────────────────────────────────────────────

if ($courseid > 0) {
    $context = \context_course::instance($courseid);
    tlog("Using course context (id={$courseid})");
} else {
    $course = $DB->get_record_sql('SELECT id FROM {course} WHERE id > 1 ORDER BY id DESC', [], IGNORE_MULTIPLE);
    if ($course) {
        $context  = \context_course::instance($course->id);
        $courseid = $course->id;
        tlog("Using last course (id={$courseid}) as Content Bank context");
    } else {
        $context = \context_system::instance();
        tlog("No courses found — using system context for Content Bank");
    }
}

// ── Banner ────────────────────────────────────────────────────────────────────

tlog(str_repeat('─', 60));
tlog("TAU H5P Generator Test");
tlog(str_repeat('─', 60));
tlog("Mode     : " . ($use_ai ? 'AI generation (live)' : 'Pipeline only (no AI)'));
tlog("H5P type : {$type}");
tlog("Topic    : {$topic}");
tlog("Quantity : {$quantity}");
tlog(str_repeat('─', 60));

// ── MODE A: Pipeline test (no AI) ─────────────────────────────────────────────

if (!$use_ai) {
    tlog("\n[1/3] Building H5P content JSON (hardcoded data)...");

    $type_key = strtolower(preg_replace('/[.\- _]/', '', $type));

    switch ($type_key) {
        case 'questionset':
            $meta    = \local_tau_course_creator_ai\h5p\types\question_set::build_h5p_json('Test QuestionSet', 'es');
            $content = \local_tau_course_creator_ai\h5p\types\question_set::build_content_json([
                'title'          => 'Test QuestionSet',
                'introduction'   => '<p>Prueba del generador.</p>',
                'passPercentage' => 60,
                'questions'      => [
                    [
                        'question' => '<p>¿Qué significa la "V" de Velocidad en Big Data?</p>',
                        'answers'  => ['La rapidez de generación y procesamiento de datos', 'El volumen total de datos', 'La variedad de fuentes', 'La veracidad de los datos'],
                        'correct'  => 0,
                        'feedback' => 'Velocidad se refiere a la rapidez con que se generan y procesan los datos.',
                    ],
                    [
                        'question' => '<p>¿Cuál es el sistema de almacenamiento distribuido de Hadoop?</p>',
                        'answers'  => ['HDFS', 'RAID', 'NFS', 'GFS'],
                        'correct'  => 0,
                        'feedback' => 'HDFS (Hadoop Distributed File System) es el sistema de archivos distribuido de Hadoop.',
                    ],
                    [
                        'question' => '<p>¿Qué framework implementó MapReduce a gran escala?</p>',
                        'answers'  => ['Apache Hadoop', 'Apache Spark', 'Apache Kafka', 'Apache Cassandra'],
                        'correct'  => 0,
                        'feedback' => 'Hadoop fue el primero en implementar MapReduce a escala masiva.',
                    ],
                ],
            ]);
            break;

        case 'coursepresentation':
            $meta    = \local_tau_course_creator_ai\h5p\types\course_presentation::build_h5p_json('Intro al Big Data', 'es');
            $content = \local_tau_course_creator_ai\h5p\types\course_presentation::build_content_json([
                'title'  => 'Intro al Big Data',
                'slides' => [
                    [
                        'title'    => '¿Qué es el Big Data?',
                        'content'  => '<h2>¿Qué es el Big Data?</h2><p>Conjuntos de datos de enorme volumen, variedad y velocidad.</p><ul><li>Volumen: terabytes a exabytes</li><li>Velocidad: tiempo real</li><li>Variedad: estructurado y no estructurado</li></ul>',
                        'keywords' => ['definición'],
                    ],
                    [
                        'title'    => 'Las 5 Vs',
                        'content'  => '<h2>Las 5 Vs del Big Data</h2><ul><li><strong>Volumen</strong></li><li><strong>Velocidad</strong></li><li><strong>Variedad</strong></li><li><strong>Veracidad</strong></li><li><strong>Valor</strong></li></ul>',
                        'keywords' => ['5Vs'],
                    ],
                    [
                        'title'    => 'Conclusiones',
                        'content'  => '<h2>Conclusiones</h2><p>El Big Data transforma la toma de decisiones empresariales, científicas y sociales.</p>',
                        'keywords' => ['conclusión'],
                    ],
                ],
            ]);
            break;

        case 'interactivebook':
            $meta    = \local_tau_course_creator_ai\h5p\types\interactive_book::build_h5p_json('Libro Big Data', 'es');
            $content = \local_tau_course_creator_ai\h5p\types\interactive_book::build_content_json([
                'title'            => 'Libro Big Data',
                'coverTitle'       => 'Introducción al Big Data',
                'coverSubtitle'    => 'Guía de aprendizaje',
                'coverDescription' => 'Explora los fundamentos del Big Data.',
                'chapters'         => [
                    [
                        'title'      => 'Capítulo 1: ¿Qué es el Big Data?',
                        'textBlocks' => [
                            ['heading' => 'Definición', 'body' => '<p>El Big Data engloba tecnologías para almacenar y procesar datos masivos.</p>'],
                        ],
                        'questions'  => [
                            ['question' => '<p>¿Cuántas Vs se asocian al Big Data?</p>', 'answers' => ['5', '3', '4', '7'], 'correct' => 0, 'feedback' => '5 Vs: Volumen, Velocidad, Variedad, Veracidad y Valor.'],
                        ],
                    ],
                ],
            ]);
            break;

        default:
            tlog("Tipo no soportado en modo pipeline: {$type}");
            tlog("Opciones válidas: QuestionSet, CoursePresentation, InteractiveBook");
            exit(1);
    }

    tlog("[2/3] Empaquetando como .h5p ZIP...");
    $path = \local_tau_course_creator_ai\h5p\h5p_content_builder::build($meta, $content);
    tlog("  ZIP creado: " . basename($path) . " (" . number_format(filesize($path)) . " bytes)");

    tlog("[3/3] Publicando en el Banco de Contenido...");
    $publisher = new \local_tau_course_creator_ai\h5p\h5p_publisher();
    $cb_id     = $publisher->publish($path, "TEST_{$type}_" . date('His'), $context);
    unlink($path);

    tlog("  ✓ Publicado → contentbank_content.id = {$cb_id}");
    tlog("\n" . str_repeat('─', 60));
    tlog("RESULTADO: EXITO");
    tlog("Ve el archivo en el Banco de Contenido:");
    tlog("  {$CFG->wwwroot}/contentbank/index.php" . ($courseid ? "?contextid={$context->id}" : ''));
    tlog(str_repeat('─', 60));
    exit(0);
}

// ── MODE B: Full AI generation ────────────────────────────────────────────────

$ai_provider = get_config('local_tau_course_creator_ai', 'ai_provider') ?: 'ollama';
$ai_model    = match ($ai_provider) {
    'claude' => get_config('local_tau_course_creator_ai', 'claude_model') ?: 'claude-sonnet-4-6',
    'openai' => get_config('local_tau_course_creator_ai', 'openai_model') ?: 'gpt-4o',
    default  => get_config('local_tau_course_creator_ai', 'ollama_model') ?: 'llama3.2:3b',
};
tlog("AI provider : {$ai_provider} ({$ai_model})");
tlog("\n[1/3] Llamando a la IA para generar contenido H5P...");

$start = microtime(true);
$spec  = [
    'h5p_type'    => $type,
    'ai_generate' => true,
    'ai_topic'    => $topic,
    'ai_quantity' => $quantity,
    'ai_language' => 'es',
    'title'       => "AI-Generated {$type}",
];

try {
    $generator = new \local_tau_course_creator_ai\h5p\h5p_generator(function (string $msg) {
        tlog("  > {$msg}");
    }, $provider);

    tlog("[2/3] Procesando + empaquetando...");
    $cb_id   = $generator->generate_from_spec($spec, $context);
    $elapsed = round(microtime(true) - $start, 1);

    tlog("[3/3] Publicado!");
    tlog("\n" . str_repeat('─', 60));
    tlog("RESULTADO: EXITO en {$elapsed}s");
    tlog("contentbank_content.id = {$cb_id}");
    tlog("Banco de Contenido:");
    tlog("  {$CFG->wwwroot}/contentbank/index.php" . ($courseid ? "?contextid={$context->id}" : ''));
    tlog(str_repeat('─', 60));

} catch (\Throwable $e) {
    tlog("\nERROR: " . $e->getMessage());
    tlog($e->getTraceAsString());
    exit(1);
}
