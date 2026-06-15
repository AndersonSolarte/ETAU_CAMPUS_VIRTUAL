<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

header('Content-Type: application/json');

$raw    = file_get_contents('php://input');
$params = json_decode($raw, true) ?: [];

$userid = (int)($params['userid'] ?? $USER->id);

try {
    $user       = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);
    $enrollments = enrol_get_all_users_courses($userid, true, null, 'ul.timeaccess DESC');

    $enrolled_ids = array_map(fn($c) => $c->id, $enrollments);

    // Available (non-enrolled) courses to recommend from.
    $available = $DB->get_records_sql(
        "SELECT c.id, c.fullname, c.summary, c.category
           FROM {course} c
          WHERE c.visible = 1 AND c.id != 1
            AND c.id NOT IN (" . implode(',', array_merge([0], $enrolled_ids)) . ")
       ORDER BY c.timecreated DESC
          LIMIT 30"
    );

    $api    = new \local_tau_ai_provider\client();
    $result = $api->post('ai/courses/recommend', [
        'userId'           => $userid,
        'enrolledCourseIds' => $enrolled_ids,
        'availableCourses'  => array_values(array_map(fn($c) => [
            'id'       => $c->id,
            'name'     => $c->fullname,
            'summary'  => strip_tags($c->summary ?? ''),
            'category' => $c->category,
        ], $available)),
    ]);

    $recommendations = [];
    foreach (($result['recommendations'] ?? []) as $rec) {
        $courseid = (int)($rec['courseId'] ?? 0);
        $recommendations[] = [
            'courseId'   => $courseid,
            'courseName' => $rec['courseName'] ?? '',
            'reason'     => $rec['reason'] ?? '',
            'matchScore' => $rec['matchScore'] ?? 0,
            'courseUrl'  => (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
        ];
    }

    echo json_encode(['recommendations' => $recommendations]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
