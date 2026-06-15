<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$host  = get_config('local_tau_course_creator_ai', 'ollama_host') ?: 'http://host.docker.internal:11434';
$model = get_config('local_tau_course_creator_ai', 'ollama_model') ?: 'llama3.2:3b';

// Minimal prompt — just get JSON out
$system = 'You are a Moodle course designer. Respond ONLY with a valid JSON object. No extra text, no markdown.

Required JSON format:
{"courseName":"...","courseDescription":"...","sections":[{"title":"...","summary":"...","activities":[{"type":"page","title":"...","description":"..."}]}]}

Rules:
- All text in Spanish
- Exactly 3 sections: section 0 = "Información del Curso" (intro), section 1 = first module, section 2 = second module
- 2-3 activities per section using types: page, quiz, assign, forum
- For quiz activities add: "questions":[{"question":"...","answers":["A","B","C","D"],"correct":0,"feedback":"..."}]
- Keep ALL descriptions under 2 sentences
- Output ONLY the JSON object, starting with { and ending with }';

$payload = [
    'model'       => $model,
    'messages'    => [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user',   'content' => 'Curso de Excel básico para principiantes universitarios'],
    ],
    'temperature' => 0.2,
    'max_tokens'  => 1800,
    'num_predict' => 1800,
    'stream'      => false,
];

echo "Sending request...\n";
$ch = curl_init("{$host}/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 600,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

echo "HTTP: {$code}\n";
if ($err) { echo "Curl error: {$err}\n"; exit(1); }

$data = json_decode($resp, true);
$raw  = $data['choices'][0]['message']['content'] ?? '(empty)';

echo "Raw output (" . strlen($raw) . " chars):\n";
echo substr($raw, 0, 800) . (strlen($raw) > 800 ? "\n[...truncated]\n" : "\n");

// Try parsing
$clean = trim($raw);
if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $clean, $m)) $clean = trim($m[1]);
$pos = strpos($clean, '{');
if ($pos !== false) $clean = substr($clean, $pos);
// strip trailing garbage after last }
$last = strrpos($clean, '}');
if ($last !== false) $clean = substr($clean, 0, $last + 1);

$decoded = json_decode($clean, true);
echo "\nJSON parse: " . (json_last_error() === JSON_ERROR_NONE ? "OK" : "FAIL: " . json_last_error_msg()) . "\n";
if ($decoded && isset($decoded['sections'])) {
    echo "Sections: " . count($decoded['sections']) . "\n";
    echo "Course: " . ($decoded['courseName'] ?? '?') . "\n";
}
