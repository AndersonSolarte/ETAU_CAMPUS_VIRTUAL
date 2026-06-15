<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$key   = getenv('OPENAI_API_KEY') ?: '';
$model = getenv('OPENAI_MODEL')   ?: 'gpt-4o';

set_config('ai_provider',    'openai', 'local_tau_course_creator_ai');
set_config('openai_api_key', $key,     'local_tau_course_creator_ai');
set_config('openai_model',   $model,   'local_tau_course_creator_ai');

echo "Proveedor: openai\n";
echo "Modelo: {$model}\n";
echo "Key: " . (empty($key) ? 'VACÍA' : substr($key, 0, 14) . '...') . "\n";

// Quick test
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'model'      => $model,
        'messages'   => [['role' => 'user', 'content' => 'Responde solo: OK']],
        'max_tokens' => 5,
    ]),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $key],
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$cerr = curl_error($ch);
curl_close($ch);

echo "Test OpenAI: HTTP {$code}\n";
if ($cerr) echo "Curl error: {$cerr}\n";
if ($resp) {
    $d = json_decode($resp, true);
    echo "Respuesta: " . ($d['choices'][0]['message']['content'] ?? $d['error']['message'] ?? 'sin contenido') . "\n";
}
