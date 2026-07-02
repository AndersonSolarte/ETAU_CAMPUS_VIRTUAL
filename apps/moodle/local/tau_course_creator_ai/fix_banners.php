<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB;

$sections = $DB->get_records('course_sections', []);
$count = 0;
foreach ($sections as $sec) {
    if (strpos($sec->summary, 'font-size:.76rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">General</div>') !== false) {
        $newsummary = str_replace('<div style="display:inline-flex;align-items:center;gap:8px;padding:7px 14px;border-radius:999px;background:rgba(255,255,255,.12);font-size:.76rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">General</div>', '', $sec->summary);
        
        $sec->summary = $newsummary;
        $DB->update_record('course_sections', $sec);
        $count++;
    }
}
echo "Updated $count sections\n";
