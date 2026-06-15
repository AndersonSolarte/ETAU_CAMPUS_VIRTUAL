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

    if ($action === 'generate_debate') {
        $courseid = (int)($params['courseid'] ?? 0);
        $forumid  = (int)($params['forumid']  ?? 0);
        $topic    = clean_param($params['topic'] ?? '', PARAM_TEXT);

        $context = context_course::instance($courseid);
        require_capability('moodle/course:manageactivities', $context);

        $result = $api->post('ai/forum/generate-debate', [
            'courseId' => $courseid,
            'forumId'  => $forumid,
            'topic'    => $topic,
        ]);

        // Post the generated discussion to Moodle forum.
        if (!empty($result['discussion'])) {
            $d = $result['discussion'];
            $forum = $DB->get_record('forum', ['id' => $forumid], '*', MUST_EXIST);
            $cm    = get_coursemodule_from_instance('forum', $forumid, $courseid, false, MUST_EXIST);
            $ctx   = context_module::instance($cm->id);

            $discussion = new \stdClass();
            $discussion->course        = $courseid;
            $discussion->forum         = $forumid;
            $discussion->name          = clean_param($d['title'] ?? $topic, PARAM_TEXT);
            $discussion->intro         = '';
            $discussion->message       = clean_param($d['body'] ?? '', PARAM_CLEANHTML);
            $discussion->messageformat = FORMAT_HTML;
            $discussion->messagetrust  = 0;
            $discussion->userid        = $USER->id;
            $discussion->timestart     = 0;
            $discussion->timeend       = 0;

            forum_add_discussion($discussion);
        }

        echo json_encode(['ok' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
