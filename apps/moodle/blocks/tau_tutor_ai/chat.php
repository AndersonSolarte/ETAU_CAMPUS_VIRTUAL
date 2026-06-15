<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

header('Content-Type: application/json');

$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];

$courseid = (int)($params['courseid'] ?? 0);
$message  = clean_param($params['message'] ?? '', PARAM_TEXT);
$history  = $params['history'] ?? [];

try {
    $course = get_course($courseid);
    $api    = new \local_tau_ai_provider\client();

    $result = $api->post('ai/tutor/chat', [
        'courseId'   => $courseid,
        'courseName' => $course->fullname,
        'message'    => $message,
        'history'    => array_slice($history, -10),
    ]);

    echo json_encode(['reply' => $result['reply'] ?? $result['message'] ?? '']);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
