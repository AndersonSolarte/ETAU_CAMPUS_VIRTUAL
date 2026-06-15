<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();

if (!is_siteadmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Solo administradores.']);
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

$context = context_system::instance();
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
            $blueprint = $ai->plan($prompt, $language, $sys_instr, $options);

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
            $blueprint = $ai->refine($blueprint, $instruction, $language);

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
