<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');

$file = '/var/www/html/course/format/classes/output/local/overview/overviewpage.php';
$content = file_get_contents($file);

if (strpos($content, '!$cm->uservisible') !== false) {
    $content = str_replace('!$cm->uservisible', '!$cm->is_visible_on_course_page()', $content);
    file_put_contents($file, $content);
    echo "Patched overviewpage.php successfully.\n";
} else {
    echo "Already patched or not found.\n";
}
