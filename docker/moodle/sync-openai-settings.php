<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
global $DB;

// Get the native OpenAI provider record
$provider = $DB->get_record('ai_providers', ['provider' => 'aiprovider_openai\\provider']);
if (!$provider) {
    echo "No native OpenAI provider found in Moodle.\n";
    exit(0);
}

$config = json_decode($provider->config, true);
if (empty($config['apikey'])) {
    echo "Native OpenAI provider exists but has no API Key configured.\n";
    exit(0);
}

$key = $config['apikey'];
$model = 'gpt-4o-mini'; // Use gpt-4o-mini as it is ultra-fast and cheap

// Set config for custom course creator plugin
set_config('ai_provider', 'openai', 'local_tau_course_creator_ai');
set_config('openai_api_key', $key, 'local_tau_course_creator_ai');
set_config('openai_model', $model, 'local_tau_course_creator_ai');

// Enable native Moodle OpenAI provider
if (!$provider->enabled) {
    $provider->enabled = 1;
    $DB->update_record('ai_providers', $provider);
    echo "Enabled native Moodle OpenAI provider.\n";
}

// Prioritize OpenAI over Ollama in native Moodle settings
$openaiprovider = $DB->get_record('ai_providers', ['provider' => 'aiprovider_openai\\provider']);
$ollamaprovider = $DB->get_record('ai_providers', ['provider' => 'aiprovider_ollama\\provider']);

$order = [];
if ($openaiprovider) {
    $order[] = $openaiprovider->id;
}
if ($ollamaprovider) {
    $order[] = $ollamaprovider->id;
}
set_config('provider_order', ',' . implode(',', $order), 'core_ai');

// Ensure all text generation actions are configured to use OpenAI
$textactions = [
    'core_ai\\aiactions\\generate_text',
    'core_ai\\aiactions\\summarise_text',
    'core_ai\\aiactions\\explain_text',
];

$actionconfig = json_decode($openaiprovider->actionconfig, true) ?: [];
foreach ($textactions as $actionname) {
    $actionconfig[$actionname] = [
        'enabled' => true,
        'settings' => [
            'model' => 'gpt-4o-mini'
        ]
    ];
}
$openaiprovider->actionconfig = json_encode($actionconfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$DB->update_record('ai_providers', $openaiprovider);

// Clear Moodle caches to make sure all changes take effect immediately
purge_caches();

echo "SUCCESS: Synchronised Moodle OpenAI settings to TAU Course Creator AI plugin!\n";
echo "AI Provider: openai\n";
echo "Model: {$model}\n";
echo "Key: " . substr($key, 0, 12) . "...\n";
