<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$sections = $DB->get_records('course_sections', []);
foreach ($sections as $sec) {
    if (strpos($sec->summary, 'Paz y Bien') !== false) {
        echo $sec->summary . "\n";
        break;
    }
}
