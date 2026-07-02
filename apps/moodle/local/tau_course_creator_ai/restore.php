<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$users = [20, 21];

foreach ($users as $userid) {
    // Find fake enrolments
    $fake_enrols = $DB->get_records_sql("
        SELECT ue.id, ue.enrolid, ue.userid, e.courseid, c.fullname
        FROM {user_enrolments} ue
        JOIN {enrol} e ON ue.enrolid = e.id
        JOIN {course} c ON e.courseid = c.id
        WHERE ue.userid = ? AND ue.timecreated >= ?
    ", [$userid, strtotime('2026-06-19 21:35:00')]);
    
    foreach ($fake_enrols as $ue) {
        $instance = $DB->get_record('enrol', ['id' => $ue->enrolid]);
        if ($instance) {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->unenrol_user($instance, $ue->userid);
            echo "Unenrolled user $userid from course {$ue->courseid} ({$ue->fullname})\n";
        }
    }
}
echo "Done restoring original enrolments.\n";
