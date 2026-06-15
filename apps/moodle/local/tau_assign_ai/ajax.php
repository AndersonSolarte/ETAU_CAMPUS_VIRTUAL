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
    $api = new \local_tau_ai_provider\client();

    switch ($action) {
        case 'grade':
            $cmid     = (int)($params['cmid']     ?? 0);
            $assignid = (int)($params['assignid'] ?? 0);
            $courseid = (int)($params['courseid'] ?? 0);

            $context = context_module::instance($cmid);
            require_capability('local/tau_assign_ai:use', $context);

            // Collect submissions.
            $submissions = $DB->get_records_sql(
                "SELECT s.id, s.userid, u.firstname, u.lastname, st.onlinetext
                   FROM {assign_submission} s
                   JOIN {user} u ON u.id = s.userid
              LEFT JOIN {assignsubmission_onlinetext} st ON st.submission = s.id
                  WHERE s.assignment = :assignid AND s.status = 'submitted'",
                ['assignid' => $assignid]
            );

            $payload = [];
            foreach ($submissions as $sub) {
                $payload[] = [
                    'submissionId' => $sub->id,
                    'userId'       => $sub->userid,
                    'studentName'  => fullname($sub),
                    'text'         => strip_tags($sub->onlinetext ?? ''),
                ];
            }

            $result = $api->post('ai/assign/grade', [
                'assignmentId' => $assignid,
                'submissions'  => $payload,
            ]);
            echo json_encode(['suggestions' => $result['suggestions'] ?? []]);
            break;

        case 'apply_one':
            $suggestion = $params['suggestion'] ?? [];
            $grade = new stdClass();
            $grade->userid     = (int)($suggestion['userId'] ?? 0);
            $grade->rawgrade   = (float)($suggestion['grade'] ?? 0);
            $grade->feedback   = clean_param($suggestion['feedback'] ?? '', PARAM_TEXT);
            $grade->feedbackformat = FORMAT_PLAIN;

            $assign = new assign(null, null, null);
            $assign->set_instance($DB->get_record('assign', ['id' => (int)($suggestion['assignmentId'] ?? 0)], '*', MUST_EXIST));
            $assign->save_grade($grade->userid, $grade);
            echo json_encode(['ok' => true]);
            break;

        case 'apply_all':
            $suggestions = $params['suggestions'] ?? [];
            foreach ($suggestions as $suggestion) {
                $grade = new stdClass();
                $grade->userid   = (int)($suggestion['userId'] ?? 0);
                $grade->rawgrade = (float)($suggestion['grade'] ?? 0);
                $grade->feedback = clean_param($suggestion['feedback'] ?? '', PARAM_TEXT);
                $grade->feedbackformat = FORMAT_PLAIN;

                $assign = new assign(null, null, null);
                $assign->set_instance($DB->get_record('assign', ['id' => (int)($suggestion['assignmentId'] ?? 0)], '*', MUST_EXIST));
                $assign->save_grade($grade->userid, $grade);
            }
            echo json_encode(['ok' => true, 'count' => count($suggestions)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Unknown action']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
