<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$key   = get_config('local_tau_course_creator_ai', 'anthropic_api_key');
$model = get_config('local_tau_course_creator_ai', 'claude_model') ?: 'claude-sonnet-4-6';

echo "Key prefix : " . substr($key, 0, 12) . "...\n";
echo "Model      : {$model}\n";

$payload = json_encode([
    'model'      => $model,
    'max_tokens' => 64,
    'messages'   => [['role' => 'user', 'content' => 'Responde exactamente: Claude conectado OK']],
]);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'x-api-key: '      . $key,
        'anthropic-version: 2023-06-01',
        'content-type: application/json',
    ],
    CURLOPT_TIMEOUT        => 30,
]);

$resp = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) { echo "cURL error: {$err}\n"; exit(1); }

$data = json_decode($resp, true);
echo "HTTP: {$code}\n";
echo "Resp: " . ($data['content'][0]['text'] ?? $data['error']['message'] ?? $resp) . "\n";
