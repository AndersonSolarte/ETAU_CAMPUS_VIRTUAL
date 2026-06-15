<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$theme = get_config('core', 'theme');
echo "Active theme:  " . $theme . PHP_EOL;

$moove_scss = get_config('theme_moove', 'scss');
echo "Moove SCSS set: " . (strlen($moove_scss) > 10 ? 'YES (' . strlen($moove_scss) . ' chars)' : 'NO') . PHP_EOL;

$head = get_config('core', 'additionalhtmlhead');
echo "HTMLhead set:  " . (strlen($head) > 10 ? 'YES (' . strlen($head) . ' chars)' : 'NO/EMPTY') . PHP_EOL;

$topofbody = get_config('core', 'additionalhtmltopofbody');
echo "TopOfBody set: " . (strlen($topofbody) > 10 ? 'YES (' . strlen($topofbody) . ' chars)' : 'NO/EMPTY') . PHP_EOL;

echo PHP_EOL;
echo "Moodle version: " . $CFG->version . PHP_EOL;
echo "wwwroot: " . $CFG->wwwroot . PHP_EOL;
