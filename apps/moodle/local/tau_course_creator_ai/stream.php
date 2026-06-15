<?php
// SSE streaming endpoint — handles both 'plan' and 'chat' actions.
// Returns text/event-stream; each event is a JSON object:
//   {"token":"..."}                   — partial token from the model
//   {"done":true,"blueprint":{...}}   — final parsed blueprint
//   {"error":"..."}                   — something went wrong
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();

if (!is_siteadmin()) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Solo administradores.']);
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

$context = context_system::instance();
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

        $blueprint = $ai->stream_refine($blueprint, $instruction, $language, $on_token);

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

        $blueprint = $ai->stream_plan($prompt, $language, $sys_instr, $options, $on_token);
    }

    $SESSION->tau_course_blueprint = $blueprint;

    sse(['done' => true, 'blueprint' => $blueprint]);

} catch (\moodle_exception $e) {
    sse(['error' => $e->getMessage()]);
} catch (\Exception $e) {
    sse(['error' => $e->getMessage()]);
}
