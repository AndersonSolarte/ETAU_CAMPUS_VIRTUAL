<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');
global $DB;
$cms = $DB->get_records_sql("
    SELECT cm.id, cm.visible, m.name as modname, f.name as itemname, cm.availability 
    FROM {course_modules} cm 
    JOIN {modules} m ON cm.module = m.id 
    LEFT JOIN {feedback} f ON (m.name = 'feedback' AND cm.instance = f.id) 
    WHERE cm.course = 47 
    ORDER BY cm.id DESC 
    LIMIT 5
");
foreach($cms as $cm) {
    echo "ID:{$cm->id} Mod:{$cm->modname} Name:{$cm->itemname} Visible:{$cm->visible} Avail:{$cm->availability}\n";
}
