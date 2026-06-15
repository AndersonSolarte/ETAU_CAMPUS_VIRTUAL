<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require('/var/www/html/config.php');

/**
 * Parse a boolean environment variable with a default fallback.
 */
function tau_env_bool(string $name, bool $default): bool {
    $value = getenv($name);
    if ($value === false || trim($value) === '') {
        return $default;
    }

    $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    return $parsed ?? $default;
}

/**
 * Patch Moodle's Ollama processor so empty request URIs do not get blocked before base_uri is applied.
 */
function tau_patch_ollama_processor(string $filepath): void {
    if (!is_file($filepath)) {
        fwrite(STDOUT, "Ollama patch skipped: {$filepath} not found.\n");
        return;
    }

    $contents = file_get_contents($filepath);
    if ($contents === false) {
        throw new RuntimeException("Unable to read {$filepath}.");
    }

    if (str_contains($contents, "withUri(\$this->get_endpoint())")) {
        fwrite(STDOUT, "Ollama core patch already present.\n");
        return;
    }

    $search = <<<'PHP'
        try {
            // Call the external AI service.
            $response = $client->send($request, [
                'base_uri' => $this->get_endpoint(),
                RequestOptions::HTTP_ERRORS => false,
            ]);
PHP;

    $replace = <<<'PHP'
        try {
            // Moodle's HTTP security layer can reject empty request URIs before base_uri is applied.
            // Ensure Ollama requests always carry the absolute endpoint.
            if ((string)$request->getUri() === '') {
                $request = $request->withUri($this->get_endpoint());
                $requestoptions = [
                    RequestOptions::HTTP_ERRORS => false,
                ];
            } else {
                $requestoptions = [
                    'base_uri' => $this->get_endpoint(),
                    RequestOptions::HTTP_ERRORS => false,
                ];
            }

            // Call the external AI service.
            $response = $client->send($request, $requestoptions);
PHP;

    $patched = str_replace($search, $replace, $contents, $count);
    if ($count === 0) {
        fwrite(STDOUT, "Ollama core patch skipped: upstream code did not match expected block.\n");
        return;
    }

    if (file_put_contents($filepath, $patched) === false) {
        throw new RuntimeException("Unable to write {$filepath}.");
    }

    fwrite(STDOUT, "Ollama core patch applied.\n");
}

/**
 * Ensure the configured Ollama port is allowed by Moodle's outgoing HTTP security.
 */
function tau_allow_ollama_port(string $endpoint): void {
    $parts = parse_url($endpoint);
    $scheme = $parts['scheme'] ?? 'http';
    $port = (int)($parts['port'] ?? ($scheme === 'https' ? 443 : 80));

    $current = trim((string)get_config('core', 'curlsecurityallowedport'));
    $ports = preg_split('/\s+/', $current) ?: [];
    $ports = array_values(array_filter(array_map('trim', $ports), static fn(string $value): bool => $value !== ''));

    if (!in_array((string)$port, $ports, true)) {
        $ports[] = (string)$port;
        set_config('curlsecurityallowedport', implode(PHP_EOL, $ports));
        fwrite(STDOUT, "Allowed Moodle outbound port {$port} for Ollama.\n");
    } else {
        fwrite(STDOUT, "Moodle outbound port {$port} already allowed.\n");
    }
}

/**
 * Prepare action settings, preserving existing system instructions when present.
 *
 * @param array<string, mixed> $settings
 * @return array<string, mixed>
 */
function tau_merge_action_settings(array $settings, string $model): array {
    $settings['model'] = $model;
    return $settings;
}

/**
 * Build an action configuration for the Ollama provider.
 *
 * @param array<string, mixed> $seed
 * @return array<string, mixed>
 */
function tau_build_action_config(array $seed, string $model): array {
    $textactions = [
        'core_ai\\aiactions\\generate_text',
        'core_ai\\aiactions\\summarise_text',
        'core_ai\\aiactions\\explain_text',
    ];

    foreach ($textactions as $actionname) {
        $existing = $seed[$actionname] ?? [];
        $settings = [];
        if (isset($existing['settings']) && is_array($existing['settings'])) {
            $settings = $existing['settings'];
        }

        $seed[$actionname] = [
            'enabled' => true,
            'settings' => tau_merge_action_settings($settings, $model),
        ];
    }

    if (isset($seed['core_ai\\aiactions\\generate_image']) && is_array($seed['core_ai\\aiactions\\generate_image'])) {
        $seed['core_ai\\aiactions\\generate_image']['enabled'] = false;
    }

    return $seed;
}

/**
 * Extract a reusable action configuration seed from an existing provider record.
 *
 * @return array<string, mixed>
 */
function tau_decode_action_config(?stdClass $provider): array {
    if (!$provider || empty($provider->actionconfig)) {
        return [];
    }

    $decoded = json_decode((string)$provider->actionconfig, true);
    return is_array($decoded) ? $decoded : [];
}

try {
    global $CFG, $DB;

    $enabled = tau_env_bool('OLLAMA_ENABLE', true);
    if (!$enabled) {
        fwrite(STDOUT, "Native Ollama configuration skipped: OLLAMA_ENABLE is false.\n");
        exit(0);
    }

    $endpoint = trim((string)(getenv('OLLAMA_ENDPOINT') ?: 'http://host.docker.internal:11434'));
    $model = trim((string)(getenv('OLLAMA_MODEL') ?: 'llama3.2:3b'));
    $providername = trim((string)(getenv('OLLAMA_PROVIDER_NAME') ?: 'Ollama Local'));
    $preferollama = tau_env_bool('OLLAMA_PREFER', true);
    $disableopenai = tau_env_bool('OLLAMA_DISABLE_OPENAI', true);
    $usebasicauth = tau_env_bool('OLLAMA_ENABLE_BASIC_AUTH', false);
    $username = trim((string)(getenv('OLLAMA_USERNAME') ?: ''));
    $password = trim((string)(getenv('OLLAMA_PASSWORD') ?: ''));

    tau_patch_ollama_processor('/var/www/html/ai/provider/ollama/classes/abstract_processor.php');
    tau_allow_ollama_port($endpoint);

    $ollamaprovider = $DB->get_record('ai_providers', ['provider' => 'aiprovider_ollama\\provider']) ?: null;
    $openaiprovider = $DB->get_record('ai_providers', ['provider' => 'aiprovider_openai\\provider']) ?: null;

    $actionseed = tau_decode_action_config($ollamaprovider);
    if ($actionseed === []) {
        $actionseed = tau_decode_action_config($openaiprovider);
    }
    $actionconfig = tau_build_action_config($actionseed, $model);

    $config = [
        'name' => $providername,
        'endpoint' => $endpoint,
        'password' => $password,
        'username' => $username,
        'returnurl' => $CFG->wwwroot . '/admin/settings.php?section=aiprovider',
        'aiprovider' => 'aiprovider_ollama',
        'enablebasicauth' => $usebasicauth ? 1 : 0,
        'updateandreturn' => 'Actualizar instancia',
    ];

    $record = (object)[
        'name' => $providername,
        'provider' => 'aiprovider_ollama\\provider',
        'enabled' => 1,
        'config' => json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'actionconfig' => json_encode($actionconfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ];

    if ($ollamaprovider) {
        $record->id = $ollamaprovider->id;
        $DB->update_record('ai_providers', $record);
        $ollamaid = (int)$ollamaprovider->id;
        fwrite(STDOUT, "Updated native Ollama provider #{$ollamaid}.\n");
    } else {
        $ollamaid = (int)$DB->insert_record('ai_providers', $record);
        fwrite(STDOUT, "Created native Ollama provider #{$ollamaid}.\n");
    }

    if ($preferollama) {
        $currentorder = (string)get_config('core_ai', 'provider_order');
        $ids = array_values(array_filter(array_map('intval', explode(',', $currentorder))));
        $ids = array_values(array_filter($ids, static fn(int $id): bool => $id !== $ollamaid));
        if ($disableopenai && $openaiprovider) {
            $ids = array_values(array_filter($ids, static fn(int $id): bool => $id !== (int)$openaiprovider->id));
        }
        array_unshift($ids, $ollamaid);
        set_config('provider_order', ',' . implode(',', $ids), 'core_ai');
        fwrite(STDOUT, "Set Ollama provider as first AI provider.\n");
    }

    if ($disableopenai && $openaiprovider) {
        if ((int)$openaiprovider->enabled !== 0) {
            $openaiprovider->enabled = 0;
            $DB->update_record('ai_providers', $openaiprovider);
        }
        fwrite(STDOUT, "Disabled native OpenAI provider #{$openaiprovider->id}.\n");
    }

    purge_caches();
    fwrite(STDOUT, "Native Ollama configuration completed using model {$model} at {$endpoint}.\n");
} catch (Throwable $e) {
    fwrite(STDERR, $e::class . ': ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
