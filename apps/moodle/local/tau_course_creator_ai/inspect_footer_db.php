<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

global $DB, $CFG;

$footer = get_config('theme_moove', 'additionalhtmlfooter');
echo "theme_moove additionalhtmlfooter length: " . strlen($footer) . "\n";
echo "Substring search for local_tau_course_creator_ai:\n";
if (strpos($footer, 'local_tau_course_creator_ai') !== false) {
    echo "FOUND in theme_moove/additionalhtmlfooter!\n";
} else {
    echo "NOT FOUND in theme_moove/additionalhtmlfooter!\n";
}

$core_footer = get_config('core', 'additionalhtmlfooter');
echo "core additionalhtmlfooter length: " . strlen($core_footer) . "\n";
if (strpos($core_footer, 'local_tau_course_creator_ai') !== false) {
    echo "FOUND in core/additionalhtmlfooter!\n";
} else {
    echo "NOT FOUND in core/additionalhtmlfooter!\n";
}
