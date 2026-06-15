<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/enrol/manual/lib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');

if ($argc < 2) {
    echo "Usage: php enroll_users.php <courseid>\n";
    exit(1);
}

$courseid = (int)$argv[1];
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$teacherRole = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);
$studentRole = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);

$teacherId = 3; // Carlos Rodríguez Medina (prof.rodriguez)
$studentId = 6; // Sofía Méndez Torres (sofia.mendez)

$enrol = enrol_get_plugin('manual');
$instance = $DB->get_record('enrol', ['enrol' => 'manual', 'courseid' => $courseid]);

if (!$instance) {
    // Add manual enrolment instance if not present
    $enrolid = $enrol->add_instance($course);
    $instance = $DB->get_record('enrol', ['id' => $enrolid], '*', MUST_EXIST);
}

// Enroll teacher
$context = context_course::instance($courseid);
if (!is_enrolled($context, $teacherId)) {
    $enrol->enrol_user($instance, $teacherId, $teacherRole->id);
    echo "Enrolled prof.rodriguez as teacher in course $courseid\n";
} else {
    echo "prof.rodriguez is already enrolled in course $courseid\n";
}

// Enroll student
if (!is_enrolled($context, $studentId)) {
    $enrol->enrol_user($instance, $studentId, $studentRole->id);
    echo "Enrolled sofia.mendez as student in course $courseid\n";
} else {
    echo "sofia.mendez is already enrolled in course $courseid\n";
}

echo "Done enrolment.\n";
