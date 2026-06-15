<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');
file_put_contents('/var/www/html/local/tau_course_creator_ai/db_scss.css', $scss);
echo "Dumped database SCSS: " . strlen($scss) . " bytes\n";
