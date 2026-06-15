<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname']              = 'TAU AI Provider';
$string['setting_api_url']         = 'Backend API URL';
$string['setting_api_url_desc']    = 'URL base del backend TAU AI (sin barra final). En Docker Desktop usa normalmente http://host.docker.internal:4000/api; fuera de Docker puede ser http://localhost:4000/api.';
$string['setting_api_key']         = 'API Key';
$string['setting_api_key_desc']    = 'Clave de autenticación para el backend TAU AI.';
$string['setting_timeout']         = 'Timeout (segundos)';
$string['setting_timeout_desc']    = 'Tiempo máximo de espera para respuestas del backend AI.';
$string['api_connection_error']    = 'Error de conexión con el backend AI: {$a}';
$string['api_invalid_response']    = 'Respuesta inválida del backend AI.';
$string['api_error']               = 'Error del backend AI: {$a}';
