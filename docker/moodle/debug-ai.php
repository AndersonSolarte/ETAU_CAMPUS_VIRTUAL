<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$host  = get_config('local_tau_course_creator_ai', 'ollama_host') ?: 'http://host.docker.internal:11434';
$model = get_config('local_tau_course_creator_ai', 'ollama_model') ?: 'llama3.2:3b';

$system = 'Output ONLY a valid JSON object. No text before or after. No markdown.

{"courseName":"Name in Spanish","courseDescription":"Brief overview.","sections":[{"title":"Información del Curso","summary":"Course intro.","activities":[{"type":"page","title":"Bienvenida","description":"Overview page."}]},{"title":"Módulo 1","summary":"First module.","activities":[{"type":"page","title":"Contenido","description":"Main content."},{"type":"quiz","title":"Quiz","questions":[{"question":"Q?","answers":["A","B","C","D"],"correct":0,"feedback":"Why."}]}]},{"title":"Módulo 2","summary":"Second module.","activities":[{"type":"page","title":"Contenido","description":"Main content."}]}]}

Replace all values with content related to the course topic. ALL text in Spanish. Allowed types: page, forum, quiz, assign. Output ONLY the JSON.';

$payload = [
    'model'       => $model,
    'messages'    => [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user',   'content' => 'Curso de Excel básico para principiantes'],
    ],
    'temperature' => 0.3,
    'max_tokens'  => 1800,
    'num_predict' => 1800,
    'stream'      => false,
];

$ch = curl_init("{$host}/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 300,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

echo "HTTP: {$code}\n";
if ($err) { echo "Error: {$err}\n"; exit; }

$data = json_decode($resp, true);
$raw  = $data['choices'][0]['message']['content'] ?? '(vacío)';

echo "--- RAW OUTPUT (" . strlen($raw) . " chars) ---\n";
echo $raw . "\n";
echo "--- END ---\n";

// Try to parse JSON
$clean = trim($raw);
if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $clean, $m)) {
    $clean = trim($m[1]);
}
$pos = strpos($clean, '{');
if ($pos !== false) $clean = substr($clean, $pos);

$decoded = json_decode($clean, true);
echo "\nJSON valid: " . (json_last_error() === JSON_ERROR_NONE ? "YES" : "NO — " . json_last_error_msg()) . "\n";
if ($decoded) {
    echo "Sections: " . count($decoded['sections'] ?? []) . "\n";
}
