<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();
require_capability('moodle/user:viewdetails', context_system::instance());

header('Content-Type: application/json');

$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];

try {
    $api    = new \local_tau_ai_provider\client();
    $result = $api->post('ai/student/profile', [
        'studentData' => $params['studentData'] ?? [],
    ]);
    echo json_encode($result);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
