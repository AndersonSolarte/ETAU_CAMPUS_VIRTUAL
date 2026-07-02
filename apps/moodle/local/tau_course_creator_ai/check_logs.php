<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$users = $DB->get_records_select('user', "firstname LIKE '%Gestión%' OR lastname LIKE '%Planeación%'");

foreach ($users as $user) {
    echo "User: {$user->firstname} {$user->lastname} (ID: {$user->id})\n";
    // Check user_enrolments
    $enrols = $DB->get_records_sql("
        SELECT ue.id, e.courseid, c.fullname, ue.timecreated
        FROM {user_enrolments} ue
        JOIN {enrol} e ON ue.enrolid = e.id
        JOIN {course} c ON e.courseid = c.id
        WHERE ue.userid = ?
        ORDER BY ue.timecreated ASC
    ", [$user->id]);
    
    foreach ($enrols as $ue) {
        $date = date('Y-m-d H:i:s', $ue->timecreated);
        echo "  - Course {$ue->courseid}: {$ue->fullname} (Enrolled: {$date})\n";
    }
}
