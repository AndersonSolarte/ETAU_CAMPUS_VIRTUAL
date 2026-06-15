<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

header('Content-Type: application/json');

$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];
$action = $params['action'] ?? '';

try {
    if ($action === 'generate_description') {
        $api    = new \local_tau_ai_provider\client();
        $result = $api->post('ai/certificate/description', [
            'certName' => clean_param($params['certName'] ?? '', PARAM_TEXT),
            'orgName'  => clean_param($params['orgName']  ?? '', PARAM_TEXT),
            'courseId' => (int)($params['courseid'] ?? 0),
        ]);
        echo json_encode(['description' => $result['description'] ?? '']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
