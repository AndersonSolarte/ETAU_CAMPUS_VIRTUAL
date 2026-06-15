<?php
namespace local_tau_forum_ai;

defined('MOODLE_INTERNAL') || die();

class observer {

    public static function post_created(\mod_forum\event\post_created $event): void {
        global $DB;

        $postid   = $event->objectid;
        $courseid = $event->courseid;
        $post     = $DB->get_record('forum_posts', ['id' => $postid]);

        if (!$post || empty(trim($post->message ?? ''))) {
            return;
        }

        try {
            $api    = new \local_tau_ai_provider\client();
            $result = $api->post('ai/forum/moderate', [
                'postId'   => $postid,
                'courseId' => $courseid,
                'subject'  => $post->subject ?? '',
                'text'     => strip_tags($post->message),
            ]);

            $verdict = $result['verdict'] ?? 'approved';
            if ($verdict === 'rejected') {
                $DB->set_field('forum_posts', 'mailed', -1, ['id' => $postid]);
            }

            // Store AI suggestion reply as a metadata field or log.
            if (!empty($result['suggestedReply'])) {
                $log = new \stdClass();
                $log->postid     = $postid;
                $log->courseid   = $courseid;
                $log->verdict    = $verdict;
                $log->ai_reply   = $result['suggestedReply'];
                $log->timecreated = time();
                $DB->insert_record('local_tau_forum_ai_log', $log, false);
            }
        } catch (\Exception $e) {
            debugging('Forum AI moderation error: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
