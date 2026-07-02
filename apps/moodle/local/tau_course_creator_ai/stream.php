<?php
// SSE streaming endpoint — handles both 'plan' and 'chat' actions.
// Returns text/event-stream; each event is a JSON object:
//   {"token":"..."}                   — partial token from the model
//   {"done":true,"blueprint":{...}}   — final parsed blueprint
//   {"error":"..."}                   — something went wrong
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
if (!is_siteadmin() && !has_capability('local/tau_course_creator_ai:use', $context)) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado.']);
    exit;
}

$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];

if (!confirm_sesskey($params['sesskey'] ?? '')) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => get_string('invalidsesskey', 'error')]);
    exit;
}

function tau_ccai_text_key(string $value): string {
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii === false) {
        $ascii = $value;
    }
    $ascii = strtolower(trim($ascii));
    return preg_replace('/\s+/', ' ', $ascii);
}

function tau_ccai_activity_key(array $activity): string {
    $title = tau_ccai_text_key((string)($activity['title'] ?? ''));
    if (strpos($title, 'bienvenida') !== false) return 'welcome';
    if (strpos($title, 'estamos contigo') !== false) return 'support';
    if (strpos($title, 'noticias') !== false || strpos($title, 'comunicados') !== false) return 'news';
    if (strpos($title, 'herramientas de inteligencia artificial') !== false || strpos($title, 'biblioteca de herramientas') !== false) return 'ai-library';
    if (strpos($title, 'biblioteca digital') !== false) return 'digital-library';
    if (strpos($title, 'presentacion del docente') !== false || strpos($title, 'presentacion profesor') !== false) return 'teacher-intro';
    if (strpos($title, 'reglamento estudiantil') !== false) return 'reglamento';
    if (strpos($title, 'microcurriculo') !== false) return 'microcurriculo';
    if (strpos($title, 'ficha de desarrollo') !== false) return 'ficha-desarrollo';
    if (strpos($title, 'horario') !== false) return 'horarios';
    if (strpos($title, 'canales de comunicacion') !== false || strpos($title, 'acompanamiento') !== false) return 'communication';
    return '';
}

function tau_ccai_front_bucket(array $section): string {
    $title = tau_ccai_text_key((string)($section['title'] ?? ''));
    if ($title === 'general') {
        return 'general';
    }
    if (strpos($title, 'informacion general') !== false) {
        return 'information';
    }
    $hasgeneral = false;
    $hasinformation = false;
    foreach (($section['activities'] ?? []) as $activity) {
        $key = tau_ccai_activity_key(is_array($activity) ? $activity : []);
        if (in_array($key, ['welcome', 'support', 'news', 'ai-library', 'digital-library'], true)) {
            $hasgeneral = true;
        }
        if (in_array($key, ['teacher-intro', 'reglamento', 'microcurriculo', 'ficha-desarrollo', 'horarios', 'communication'], true)) {
            $hasinformation = true;
        }
    }
    if ($hasgeneral && !$hasinformation) return 'general';
    if ($hasinformation) return 'information';
    return '';
}

function tau_ccai_cleanup_front_activities(array $activities): array {
    $seen = [];
    $clean = [];
    foreach ($activities as $activity) {
        if (!is_array($activity)) {
            continue;
        }
        $title = tau_ccai_text_key((string)($activity['title'] ?? ''));
        if (strpos($title, 'foro de inquietudes') !== false || strpos($title, 'foro de dudas') !== false || strpos($title, 'tips de plataforma') !== false || $title === 'general' || $title === 'informacion general') {
            continue;
        }
        $key = tau_ccai_activity_key($activity);
        if ($key !== '') {
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
        }
        $clean[] = $activity;
    }
    return array_values($clean);
}

function tau_ccai_normalize_blueprint(array $blueprint): array {
    $teacher = trim((string)($blueprint['teacherName'] ?? ''));
    if ($teacher === '') {
        $teacher = 'Docente de la Institucion';
    }
    $defaults = [
        'general' => [
            'title' => 'General',
            'summary' => 'Espacio inicial de acogida, orientación, noticias y recursos institucionales del curso.',
            'activities' => [
                ['type' => 'page', 'title' => 'Bienvenida', 'description' => 'Mensaje de acogida institucional para el estudiante.'],
                ['type' => 'page', 'title' => 'Estamos Contigo', 'description' => 'Acompañamiento y orientación permanente para el estudiante.'],
                ['type' => 'forum', 'title' => 'Noticias y Comunicados', 'description' => 'Foro para la publicación de avisos, novedades, fechas importantes y comunicados del curso.', 'forumtype' => 'news'],
                ['type' => 'page', 'title' => 'Biblioteca de Herramientas de Inteligencia Artificial', 'description' => 'Accesos directos a herramientas de IA de apoyo al aprendizaje.'],
                ['type' => 'url', 'title' => 'Biblioteca Digital', 'description' => 'Consulta académica, bases de datos, libros digitales y recursos institucionales.', 'externalurl' => 'https://www.unicesmag.edu.co/biblioteca/'],
            ],
        ],
        'information' => [
            'title' => 'Información General',
            'summary' => 'Documentos, lineamientos y orientaciones clave para comprender el desarrollo del curso.',
            'activities' => [
                ['type' => 'page', 'title' => 'Presentación del Docente ' . $teacher, 'description' => 'Bienvenida profesional y perfil del docente.'],
                ['type' => 'url', 'title' => 'Reglamento Estudiantil', 'description' => 'Consulta las normas y lineamientos institucionales del curso.'],
                ['type' => 'resource', 'title' => 'Microcurrículo', 'description' => 'Adjunta el microcurrículo oficial en formato PDF.', 'uploadedfile' => null],
                ['type' => 'resource', 'title' => 'Ficha de desarrollo temático', 'description' => 'Adjunta la ficha de desarrollo temático en formato PDF.', 'uploadedfile' => null],
                ['type' => 'resource', 'title' => 'Horarios', 'description' => 'Adjunta el horario en PDF o cambia este recurso a URL si corresponde.', 'uploadedfile' => null],
                ['type' => 'page', 'title' => 'Canales de comunicación o acompañamiento', 'description' => 'Publica aquí los medios, horarios y orientaciones de contacto para el estudiante.'],
            ],
        ],
    ];
    $sections = is_array($blueprint['sections'] ?? null) ? $blueprint['sections'] : [];
    $collected = ['general' => [], 'information' => []];
    $summaries = ['general' => '', 'information' => ''];
    $others = [];

    foreach ($sections as $section) {
        if (!is_array($section)) {
            continue;
        }
        $bucket = tau_ccai_front_bucket($section);
        if ($bucket === '') {
            $others[] = $section;
            continue;
        }
        if (!empty($section['summary']) && $summaries[$bucket] === '') {
            $summaries[$bucket] = (string)$section['summary'];
        }
        foreach (($section['activities'] ?? []) as $activity) {
            if (!is_array($activity)) {
                continue;
            }
            $key = tau_ccai_activity_key($activity);
            if (in_array($key, ['welcome', 'support', 'news', 'ai-library', 'digital-library'], true)) {
                $collected['general'][] = $activity;
            } else if (in_array($key, ['teacher-intro', 'reglamento', 'microcurriculo', 'ficha-desarrollo', 'horarios', 'communication'], true)) {
                $collected['information'][] = $activity;
            } else {
                $collected[$bucket][] = $activity;
            }
        }
    }

    foreach (['general', 'information'] as $bucket) {
        $used = [];
        $merged = [];
        foreach ($defaults[$bucket]['activities'] as $definition) {
            $wantedkey = tau_ccai_activity_key($definition);
            $match = null;
            foreach ($collected[$bucket] as $idx => $activity) {
                if (isset($used[$idx])) {
                    continue;
                }
                if (tau_ccai_activity_key($activity) === $wantedkey) {
                    $match = $activity;
                    $used[$idx] = true;
                    break;
                }
            }
            if ($match) {
                $merged[] = array_merge($definition, $match, [
                    'type' => in_array($wantedkey, ['microcurriculo', 'ficha-desarrollo', 'horarios'], true) ? 'resource' : ($match['type'] ?? $definition['type']),
                    'title' => $match['title'] ?? $definition['title'],
                    'description' => $match['description'] ?? $definition['description'],
                ]);
            } else {
                $merged[] = $definition;
            }
        }
        foreach ($collected[$bucket] as $idx => $activity) {
            if (!isset($used[$idx])) {
                $merged[] = $activity;
            }
        }
        $blueprint['sections_' . $bucket] = [
            'title' => $defaults[$bucket]['title'],
            'summary' => $summaries[$bucket] !== '' ? $summaries[$bucket] : $defaults[$bucket]['summary'],
            'activities' => tau_ccai_cleanup_front_activities($merged),
        ];
    }

    $blueprint['sections'] = array_merge(
        [$blueprint['sections_general'], $blueprint['sections_information']],
        $others
    );
    unset($blueprint['sections_general'], $blueprint['sections_information']);
    return $blueprint;
}

$action  = clean_param($params['action'] ?? 'plan', PARAM_ALPHA);

// Flush any Moodle output buffers before switching to SSE mode.
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache, no-store');
header('X-Accel-Buffering: no');   // tells nginx not to buffer this response
header('Connection: keep-alive');
set_time_limit(600);

ob_implicit_flush(true);

function sse(array $data): void {
    echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
    flush();
}

try {
    require_capability('local/tau_course_creator_ai:use', $context);

    $ai = new \local_tau_course_creator_ai\ai_service('openai');
    $on_token = function (string $token) {
        sse(['token' => $token]);
    };

    if ($action === 'chat') {
        $instruction = clean_param($params['instruction'] ?? '', PARAM_TEXT);
        $language    = clean_param($params['language']    ?? 'es', PARAM_ALPHA);
        $blueprint   = $params['blueprint'] ?? ($SESSION->tau_course_blueprint ?? null);

        if (!$blueprint || !$instruction) {
            sse(['error' => 'Blueprint and instruction are required.']);
            exit;
        }

        $blueprint = tau_ccai_normalize_blueprint($ai->stream_refine($blueprint, $instruction, $language, $on_token));

    } else {
        // Default: plan
        $prompt    = clean_param($params['prompt']       ?? '', PARAM_TEXT);
        $language  = clean_param($params['language']     ?? 'es', PARAM_ALPHA);
        $sys_instr = clean_param($params['systemPrompt'] ?? '', PARAM_TEXT);
        $raw_opts  = $params['options'] ?? [];
        $options   = [];
        foreach (['page', 'forum', 'quiz', 'assign', 'glossary', 'feedback'] as $k) {
            $options[$k] = !empty($raw_opts[$k]);
        }

        if (!$prompt) {
            sse(['error' => 'Prompt is required.']);
            exit;
        }

        $blueprint = tau_ccai_normalize_blueprint($ai->stream_plan($prompt, $language, $sys_instr, $options, $on_token));
    }

    $SESSION->tau_course_blueprint = $blueprint;

    sse(['done' => true, 'blueprint' => $blueprint]);

} catch (\moodle_exception $e) {
    sse(['error' => $e->getMessage()]);
} catch (\Exception $e) {
    sse(['error' => $e->getMessage()]);
}
