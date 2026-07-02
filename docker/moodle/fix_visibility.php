<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');
global $DB;
$sql = "
    SELECT cm.id, cm.availability 
    FROM {course_modules} cm 
    JOIN {modules} m ON cm.module = m.id 
    JOIN {feedback} f ON cm.instance = f.id 
    WHERE m.name = 'feedback' AND f.name = 'Encuesta de Satisfacción del Curso'
";
$cms = $DB->get_records_sql($sql);
$count = 0;
foreach($cms as $cm) {
    if ($cm->availability && strpos($cm->availability, '"showc":[false]') !== false) {
        $cm->availability = str_replace('"showc":[false]', '"showc":[true]', $cm->availability);
        $DB->update_record('course_modules', $cm);
        $count++;
    }
}
echo "Updated $count existing feedback modules to show in index.\n";
