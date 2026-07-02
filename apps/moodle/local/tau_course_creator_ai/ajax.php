<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
if (!is_siteadmin() && !has_capability('local/tau_course_creator_ai:use', $context)) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado.']);
    exit;
}

header('Content-Type: application/json');
set_time_limit(600); // Local LLMs (Ollama) can take several minutes

// Read JSON body once (php://input can only be read once)
$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];

// Validate sesskey from JSON body (not $_POST since Content-Type is application/json)
if (!confirm_sesskey($params['sesskey'] ?? '')) {
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

$action = clean_param($params['action'] ?? '', PARAM_ALPHA);

try {
    require_capability('local/tau_course_creator_ai:use', $context);
    switch ($action) {

        // ── Generate course plan ─────────────────────────────────────────────
        case 'plan':
            $prompt      = clean_param($params['prompt']       ?? '', PARAM_TEXT);
            $language    = clean_param($params['language']     ?? 'es', PARAM_ALPHA);
            $sys_instr   = clean_param($params['systemPrompt'] ?? '', PARAM_TEXT);
            $raw_options = $params['options'] ?? [];
            $options     = [];
            foreach (['page', 'forum', 'quiz', 'assign', 'glossary', 'feedback'] as $k) {
                $options[$k] = !empty($raw_options[$k]);
            }

            if (!$prompt) {
                throw new \Exception('Prompt is required.');
            }

            $ai        = new \local_tau_course_creator_ai\ai_service();
            $blueprint = tau_ccai_normalize_blueprint($ai->plan($prompt, $language, $sys_instr, $options));

            // Store blueprint in session for build step
            $SESSION->tau_course_blueprint = $blueprint;

            echo json_encode([
                'ok'        => true,
                'blueprint' => $blueprint,
            ]);
            break;

        // ── Generate welcome intro via AI ─────────────────────────────────────
        case 'generatewelcome':
            $prompt   = clean_param($params['prompt']   ?? '', PARAM_TEXT);
            $teacher  = clean_param($params['teacher']  ?? '', PARAM_TEXT);
            $language = clean_param($params['language'] ?? 'es', PARAM_ALPHA);
            if (!$prompt) {
                throw new \Exception('Por favor, escribe la temática o nombre del curso primero.');
            }

            $ai = new \local_tau_course_creator_ai\ai_service('openai');
            $llm_prompt = "Escribe una frase de bienvenida e introducción profesional, atractiva y muy corta (máximo 2 oraciones) para un curso de Moodle titulado '{$prompt}', dictado por el docente '{$teacher}'. Debe estar en español y sonar muy motivador. No agregues comillas ni explicaciones, solo la frase de bienvenida directamente.";
            
            $welcome = $ai->generate_text($llm_prompt, "Escribe solo la frase de bienvenida de 1 o 2 oraciones.");

            echo json_encode([
                'ok'      => true,
                'welcome' => trim($welcome, "\"' \t\n\r\0\x0B")
            ]);
            break;

        // ── Refine plan via chat ──────────────────────────────────────────────
        case 'chat':
            $instruction = clean_param($params['instruction'] ?? '', PARAM_TEXT);
            $language    = clean_param($params['language']    ?? 'es', PARAM_ALPHA);
            $blueprint   = $params['blueprint'] ?? ($SESSION->tau_course_blueprint ?? null);

            if (!$blueprint || !$instruction) {
                throw new \Exception('Blueprint and instruction are required.');
            }

            $ai        = new \local_tau_course_creator_ai\ai_service();
            $blueprint = tau_ccai_normalize_blueprint($ai->refine($blueprint, $instruction, $language));

            $SESSION->tau_course_blueprint = $blueprint;

            echo json_encode([
                'ok'        => true,
                'blueprint' => $blueprint,
            ]);
            break;

        // ── Build the Moodle course ───────────────────────────────────────────
        case 'build':
            $blueprint   = $params['blueprint'] ?? ($SESSION->tau_course_blueprint ?? null);
            $category_id = (int)($params['category'] ?? 1);

            if (!$blueprint || empty($blueprint['sections'])) {
                throw new \Exception('Blueprint is required. Generate a plan first.');
            }
            $blueprint = tau_ccai_normalize_blueprint($blueprint);

            $log      = [];
            $builder  = new \local_tau_course_creator_ai\course_builder(function (string $msg) use (&$log) {
                $log[] = $msg;
            });

            $course_id = $builder->build($blueprint, $category_id);

            $course_url = (new \moodle_url('/course/view.php', ['id' => $course_id]))->out(false);

            unset($SESSION->tau_course_blueprint);

            echo json_encode([
                'ok'        => true,
                'courseId'  => $course_id,
                'courseUrl' => $course_url,
                'log'       => $log,
            ]);
            break;

        case 'savecoursetemplate':
            $blueprint = $params['blueprint'] ?? null;
            $matchdata = $params['matchdata'] ?? [];

            if (!$blueprint || empty($blueprint['sections']) || empty($matchdata['shortname'])) {
                throw new \Exception('La plantilla del curso no es válida.');
            }

            \local_tau_course_creator_ai\native_course_template_manager::store(
                $blueprint,
                is_array($matchdata) ? $matchdata : []
            );

            echo json_encode(['ok' => true]);
            break;

        case 'clearcoursetemplate':
            \local_tau_course_creator_ai\native_course_template_manager::clear();
            echo json_encode(['ok' => true]);
            break;

        case 'bulkdelete':
            $courseids = $params['courseids'] ?? [];
            if (!is_array($courseids)) {
                throw new \Exception('Cursos no válidos.');
            }

            require_once($CFG->dirroot . '/course/lib.php');
            $deleted = [];
            $errors = [];

            foreach ($courseids as $id) {
                $id = (int)$id;
                if ($id <= 1) {
                    continue; // No borrar el curso del sitio
                }

                try {
                    $course = $DB->get_record('course', ['id' => $id]);
                    if ($course) {
                        delete_course($id, false);
                        $deleted[] = $id;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Curso {$id}: " . $e->getMessage();
                }
            }

            echo json_encode([
                'ok' => true,
                'deleted' => $deleted,
                'errors' => $errors
            ]);
            break;

        case 'bulkdeletecategories':
            $categoryids = $params['categoryids'] ?? [];
            if (!is_array($categoryids)) {
                throw new \Exception('Categorías no válidas.');
            }

            $deleted = [];
            $errors = [];

            foreach ($categoryids as $id) {
                $id = (int)$id;
                if ($id <= 0) {
                    continue;
                }

                try {
                    $category = core_course_category::get($id, IGNORE_MISSING);
                    if ($category && $category->can_delete_full()) {
                        $category->delete_full(false);
                        $deleted[] = $id;
                    } else if ($category) {
                        $errors[] = "Categoría {$id}: No tiene permisos para borrarla.";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Categoría {$id}: " . $e->getMessage();
                }
            }

            echo json_encode([
                'ok' => true,
                'deleted' => $deleted,
                'errors' => $errors
            ]);
            break;

        case 'getsections':
            $courseid = (int)($params['courseid'] ?? 0);
            if ($courseid <= 1) {
                throw new \Exception('ID de curso no válido.');
            }
            $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
            $modinfo = get_fast_modinfo($course);
            $sections = $modinfo->get_section_info_all();
            $result_sections = [];
            foreach ($sections as $s) {
                if ($s->section >= 0) {
                    $name = $s->name;
                    if (empty($name)) {
                        $name = ($s->section == 0) ? 'General' : 'Módulo ' . $s->section;
                    }
                    $result_sections[] = [
                        'id' => $s->id,
                        'section' => $s->section,
                        'name' => $name
                    ];
                }
            }
            echo json_encode([
                'ok' => true,
                'sections' => $result_sections
            ]);
            break;

        case 'generatewithia':
            $courseid = (int)($params['courseid'] ?? 0);
            $section  = (int)($params['section'] ?? 0);
            $prompt   = clean_param($params['prompt'] ?? '', PARAM_TEXT);
            if ($courseid <= 1) {
                throw new \Exception('ID de curso no válido.');
            }
            if (!$prompt) {
                throw new \Exception('Escribe lo que deseas generar primero.');
            }

            // Call AI service
            $ai = new \local_tau_course_creator_ai\ai_service('openai');
            $activities = $ai->generate_activities_for_section($prompt, 'es');

            // Build activities in section
            $builder = new \local_tau_course_creator_ai\course_builder();
            $builder->add_activities_to_section($courseid, $section, $activities);

            echo json_encode([
                'ok' => true,
                'message' => '¡Recursos creados exitosamente en el curso!'
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action: ' . $action]);
    }

} catch (\moodle_exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
