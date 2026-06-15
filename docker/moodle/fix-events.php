<?php
// Fix events.php
$events_file = '/var/www/html/local/tau_course_creator_ai/db/events.php';
$events_code = "<?php\ndefined('MOODLE_INTERNAL') || die();\n\n\$observers = [\n    [\n        'eventname' => '\core\event\course_created',\n        'callback' => '\local_tau_course_creator_ai\native_course_template_manager::handle_course_created',\n        'priority' => 9999,\n        'internal' => false,\n    ],\n    [\n        'eventname' => '\core\event\course_updated',\n        'callback' => '\local_tau_course_creator_ai\native_course_template_manager::handle_course_updated',\n        'priority' => 9999,\n        'internal' => false,\n    ],\n];\n";
file_put_contents($events_file, $events_code);

// Fix native_course_template_manager.php
$manager_file = '/var/www/html/local/tau_course_creator_ai/classes/native_course_template_manager.php';
$manager_code = file_get_contents($manager_file);

if (strpos($manager_code, 'handle_course_updated') === false) {
    $updated_method = "

    public static function handle_course_updated(\core\event\course_updated \$event): void {
        global \$SESSION, \$DB;

        \$payload = \$SESSION->{self::SESSION_KEY} ?? null;
        if (empty(\$payload['blueprint']) || empty(\$payload['matchdata'])) {
            return;
        }

        \$course = \$DB->get_record('course', ['id' => \$event->objectid], '*', IGNORE_MISSING);
        if (!\$course) {
            return;
        }

        try {
            \$builder = new course_builder();
            \$builder->apply_to_existing_course((int)\$course->id, (array)\$payload['blueprint']);
        } catch (\Throwable \$e) {
            debugging('tau_course_creator_ai native template update failed: ' . \$e->getMessage(), DEBUG_DEVELOPER);
        } finally {
            unset(\$SESSION->{self::SESSION_KEY});
        }
    }
}
";
    $manager_code = str_replace("}\n}", "}" . $updated_method, $manager_code);
    file_put_contents($manager_file, $manager_code);
}
echo "OK\n";
