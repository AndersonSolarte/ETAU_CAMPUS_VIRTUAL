<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

$provider = get_config('local_tau_course_creator_ai', 'ai_provider');
$openai_key = get_config('local_tau_course_creator_ai', 'openai_api_key');
$openai_model = get_config('local_tau_course_creator_ai', 'openai_model');
$ollama_host = get_config('local_tau_course_creator_ai', 'ollama_host');
$ollama_model = get_config('local_tau_course_creator_ai', 'ollama_model');
$claude_key = get_config('local_tau_course_creator_ai', 'anthropic_api_key');
$claude_model = get_config('local_tau_course_creator_ai', 'claude_model');

echo "Current Configuration:\n";
echo "  - ai_provider: " . ($provider ?: 'not set') . "\n";
echo "  - openai_api_key: " . ($openai_key ? 'SET (Length: ' . strlen($openai_key) . ')' : 'NOT SET') . "\n";
echo "  - openai_model: " . ($openai_model ?: 'not set') . "\n";
echo "  - anthropic_api_key: " . ($claude_key ? 'SET' : 'NOT SET') . "\n";
echo "  - claude_model: " . ($claude_model ?: 'not set') . "\n";
echo "  - ollama_host: " . ($ollama_host ?: 'not set') . "\n";
echo "  - ollama_model: " . ($ollama_model ?: 'not set') . "\n";
