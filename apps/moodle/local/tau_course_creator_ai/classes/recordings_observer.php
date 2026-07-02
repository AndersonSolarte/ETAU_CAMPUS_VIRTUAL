<?php
namespace local_tau_course_creator_ai;

defined('MOODLE_INTERNAL') || die();

class recordings_observer {
    public static function handle_course_created(\core\event\course_created $event): void {
        try {
            recordings_manager::ensure_recordings_section((int)$event->objectid);
        } catch (\Throwable $e) {
            debugging('tau_course_creator_ai recordings observer failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
