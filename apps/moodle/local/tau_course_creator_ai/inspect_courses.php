<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

global $DB;

$total = $DB->count_records('course');
echo "Total courses: $total\n";

$sql_public = "SELECT c.id, c.fullname
                 FROM {course} c
                 JOIN {customfield_data} d ON d.instanceid = c.id
                 JOIN {customfield_field} f ON f.id = d.fieldid
                WHERE f.shortname = 'publish_apoyo_academico' AND d.intvalue = 1";
$public_courses = $DB->get_records_sql($sql_public);
echo "Public courses count: " . count($public_courses) . "\n";
foreach ($public_courses as $c) {
    echo "ID: {$c->id} | Name: {$c->fullname}\n";
}
