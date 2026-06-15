<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

$ai = new \local_tau_course_creator_ai\ai_service();
try {
    echo "Starting plan generation test...\n";
    
    $lang = 'es';
    $prompt = 'Ética y responsabilidad social empresarial';
    
    echo "Sending raw chat request to OpenAI...\n";
    $ref = new ReflectionClass($ai);
    $chat_method = $ref->getMethod('chat');
    $chat_method->setAccessible(true);
    
    // We will extract system instructions using plan_system_prompt
    $system_method = $ref->getMethod('plan_system_prompt');
    $system_method->setAccessible(true);
    $system = $system_method->invoke($ai, 'Spanish', []);
    
    $raw = $chat_method->invoke($ai, [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user',   'content' => $prompt]
    ]);
    
    echo "--- RAW RESPONSE START ---\n";
    echo $raw . "\n";
    echo "--- RAW RESPONSE END ---\n";
    
    echo "Parsing raw response...\n";
    $parsed = $ai->plan($prompt);
    echo "Successfully parsed plan:\n";
    print_r($parsed);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
