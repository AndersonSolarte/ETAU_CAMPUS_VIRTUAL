<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once('/var/www/html/local/tau_course_creator_ai/classes/ai_service.php');

$provider = get_config('local_tau_course_creator_ai', 'ai_provider') ?: 'ollama';
$host     = get_config('local_tau_course_creator_ai', 'ollama_host') ?: 'http://host.docker.internal:11434';
$model    = get_config('local_tau_course_creator_ai', 'ollama_model') ?: 'llama3.2:3b';

echo "Provider: {$provider}\n";
echo "Ollama host: {$host}\n";
echo "Ollama model: {$model}\n";

// Test connectivity
$ch = curl_init("{$host}/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'model'      => $model,
        'messages'   => [['role'=>'user','content'=>'Say OK']],
        'max_tokens' => 10,
        'stream'     => false,
    ]),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

echo "HTTP code: {$code}\n";
if ($err)  echo "Curl error: {$err}\n";
if ($resp) {
    $data = json_decode($resp, true);
    $text = $data['choices'][0]['message']['content'] ?? 'NO CONTENT';
    echo "Response: {$text}\n";
}

// Now test the actual service
echo "\nTesting ai_service::plan()...\n";
try {
    $svc    = new \local_tau_course_creator_ai\ai_service();
    $result = $svc->plan('Curso de Excel básico', 'es', '', ['quiz'=>false,'forum'=>false,'assign'=>false]);
    echo "SUCCESS - sections: " . count($result['sections']) . "\n";
    echo "Name: " . ($result['courseName'] ?? 'N/A') . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
