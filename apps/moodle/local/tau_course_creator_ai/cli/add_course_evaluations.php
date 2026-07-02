<?php
define('CLI_SCRIPT', true);
require_once(dirname(__DIR__, 3) . '/config.php');
require_once($CFG->dirroot . '/local/tau_course_creator_ai/lib.php');

global $DB, $CFG;
require_once($CFG->libdir . '/clilib.php');

cli_heading('Retrofitting Existing Courses with Course Evaluation Surveys');

$admin = get_admin();
\core\session\manager::set_user($admin);

// Find all courses except site frontpage
$courses = $DB->get_records_select('course', 'id > 1', null, 'id ASC');

$builder = new \local_tau_course_creator_ai\course_builder(function($msg) {
    echo "  > $msg\n";
});

$count = 0;
foreach ($courses as $course) {
    echo "Processing course: {$course->id} - {$course->fullname}\n";
    
    // Check if course already has the feedback
    $has_feedback = $DB->record_exists_sql("
        SELECT cm.id 
        FROM {course_modules} cm
        JOIN {modules} m ON cm.module = m.id
        JOIN {feedback} f ON cm.instance = f.id
        WHERE cm.course = ? AND m.name = 'feedback' AND f.name = 'Encuesta de Satisfacción del Curso'
    ", [$course->id]);
    
    if ($has_feedback) {
        echo "  - Course already has the survey. Skipping.\n";
        continue;
    }
    
    // Force enable completion for the course if not enabled
    if (!$course->enablecompletion) {
        $DB->set_field('course', 'enablecompletion', 1, ['id' => $course->id]);
        echo "  - Enabled course completion.\n";
    }

    // Find the last graded activity to place the feedback before it
    $sql = "
        SELECT cm.id, cm.section, cm.course, cm.instance, m.name as modname, cs.section as sectionnum
        FROM {course_modules} cm
        JOIN {modules} m ON cm.module = m.id
        JOIN {course_sections} cs ON cm.section = cs.id
        WHERE cm.course = ? AND m.name IN ('assign', 'quiz', 'forum', 'h5pactivity', 'page', 'resource')
        ORDER BY cs.section DESC, cm.id DESC
    ";
    
    $activities = $DB->get_records_sql($sql, [$course->id], 0, 20);
    
    if (empty($activities)) {
        echo "  - No activities found to restrict. Skipping.\n";
        continue;
    }
    
    // Get the absolute last graded activity
    $last_activity = reset($activities);
    
    // And get the activity immediately preceding it to restrict the feedback
    $prev_activity = null;
    $is_next = false;
    foreach ($activities as $act) {
        if ($is_next) {
            $prev_activity = $act;
            break;
        }
        if ($act->id == $last_activity->id) {
            $is_next = true;
        }
    }

    echo "  - Last activity found: {$last_activity->modname} (cmid: {$last_activity->id})\n";
    
    $sectionnum = $last_activity->sectionnum;
    
    // Create the feedback activity using the builder's logic via a dummy array
    // Since create_activity is private, we can use reflection or duplicate the creation logic.
    // Given it's a CLI script for retrofitting, doing it directly is safest.
    
    require_once($CFG->dirroot . '/mod/feedback/lib.php');
    require_once($CFG->dirroot . '/course/lib.php');
    
    $mod = new stdClass();
    $mod->modulename = 'feedback';
    $mod->name = 'Encuesta de Satisfacción del Curso';
    $mod->intro = 'Ayúdanos a mejorar evaluando el curso y al docente. Esta encuesta es anónima y obligatoria para acceder a tu última actividad.';
    $mod->introformat = FORMAT_HTML;
    $mod->anonymous = 1;
    $mod->email_notification = 0;
    $mod->multiple_submit = 0;
    $mod->autonumbering = 1;
    $mod->publish_stats = 0;
    $mod->timeopen = 0;
    $mod->timeclose = 0;
    $mod->course = $course->id;
    $mod->section = $sectionnum;
    $mod->visible = 1;
    $mod->page_after_submit = '¡Gracias por completar la encuesta!';
    $mod->page_after_submitformat = FORMAT_HTML;
    $mod->site_after_submit = '';
    $mod->completionsubmit = 1;
    $mod->completion = 2; 
    $mod->timemodified = time();
    
    require_once($CFG->libdir . '/filelib.php');
    $mod->introeditor = ['itemid' => file_get_unused_draft_itemid(), 'text' => $mod->intro, 'format' => FORMAT_HTML];
    
    $feedback_cm = create_module($mod);
    
    if ($feedback_cm) {
        // Move the feedback module to be right before the last activity
        // In Moodle, sequence is stored in course_sections
        $section = $DB->get_record('course_sections', ['id' => $last_activity->section]);
        if ($section) {
            $sequence = explode(',', $section->sequence);
            // Remove feedback_cm from sequence
            $sequence = array_diff($sequence, [$feedback_cm->coursemodule]);
            // Find index of last_activity
            $pos = array_search($last_activity->id, $sequence);
            if ($pos !== false) {
                array_splice($sequence, $pos, 0, [$feedback_cm->coursemodule]);
            } else {
                $sequence[] = $feedback_cm->coursemodule;
            }
            $DB->set_field('course_sections', 'sequence', implode(',', $sequence), ['id' => $section->id]);
        }
        
        // Add Questions using reflection to access private method
        $reflection = new ReflectionClass($builder);
        $method = $reflection->getMethod('add_questions_to_feedback');
        $method->setAccessible(true);
        $method->invokeArgs($builder, [$feedback_cm->instance]);
        
        // Update Feedback Availability (hidden until previous activity is done)
        if ($prev_activity) {
            $feedback_cm_record = $DB->get_record('course_modules', ['id' => $feedback_cm->coursemodule]);
            $feedback_cm_record->availability = '{"op":"&","c":[{"type":"completion","cm":' . $prev_activity->id . ',"e":1}],"showc":[false]}';
            $DB->update_record('course_modules', $feedback_cm_record);
        }
        
        // Update Last Activity Availability (greyed out until feedback is done)
        $last_cm_record = $DB->get_record('course_modules', ['id' => $last_activity->id]);
        $last_cm_record->availability = '{"op":"&","c":[{"type":"completion","cm":' . $feedback_cm->coursemodule . ',"e":1}],"showc":[true]}';
        $DB->update_record('course_modules', $last_cm_record);
        
        rebuild_course_cache($course->id, true);
        echo "  - Added feedback and updated restrictions successfully.\n";
        $count++;
    }
}

echo "\nCompleted. Total courses retrofitted: $count\n";
