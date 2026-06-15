<?php
namespace local_tau_course_creator_ai;

defined('MOODLE_INTERNAL') || die();

class native_course_template_manager {
    private const SESSION_KEY = 'tau_course_native_template';

    public static function store(array $blueprint, array $matchdata): void {
        global $SESSION;

        $SESSION->{self::SESSION_KEY} = [
            'blueprint' => $blueprint,
            'matchdata' => [
                'fullname' => trim((string)($matchdata['fullname'] ?? '')),
                'shortname' => trim((string)($matchdata['shortname'] ?? '')),
                'categoryid' => (int)($matchdata['categoryid'] ?? 0),
            ],
            'timecreated' => time(),
        ];
    }

    public static function clear(): void {
        global $SESSION;
        unset($SESSION->{self::SESSION_KEY});
    }

    public static function handle_course_created(\core\event\course_created $event): void {
        global $SESSION, $DB;

        $payload = $SESSION->{self::SESSION_KEY} ?? null;
        if (empty($payload['blueprint']) || empty($payload['matchdata'])) {
            return;
        }

        $course = $DB->get_record('course', ['id' => $event->objectid], '*', IGNORE_MISSING);
        if (!$course) {
            return;
        }

        $match = $payload['matchdata'];
        $recent = !empty($payload['timecreated']) && ((time() - (int)$payload['timecreated']) < 1800);
        $samecourse = $recent
            && trim((string)$course->fullname) === (string)$match['fullname']
            && trim((string)$course->shortname) === (string)$match['shortname']
            && ((int)$course->category === (int)$match['categoryid'] || (int)$match['categoryid'] === 0);

        if (!$samecourse) {
            return;
        }

        try {
            $builder = new course_builder();
            $builder->apply_to_existing_course((int)$course->id, (array)$payload['blueprint']);
        } catch (\Throwable $e) {
            debugging('tau_course_creator_ai native template failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
        } finally {
            unset($SESSION->{self::SESSION_KEY});
        }
    }
}
