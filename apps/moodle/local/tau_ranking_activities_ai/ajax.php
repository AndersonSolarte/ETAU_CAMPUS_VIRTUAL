<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

header('Content-Type: application/json');

$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];

$courseid = (int)($params['courseid'] ?? 0);
require_capability('moodle/course:manageactivities', context_course::instance($courseid));

try {
    $api    = new \local_tau_ai_provider\client();
    $result = $api->post('ai/ranking/analyze', [
        'courseId'   => $courseid,
        'activities' => $params['activities'] ?? [],
    ]);
    echo json_encode($result);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
