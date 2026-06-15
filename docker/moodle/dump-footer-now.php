<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$f = get_config(false,'additionalhtmlfooter');
echo "=== additionalhtmlfooter (" . strlen($f) . " bytes) ===\n";
echo $f . "\n";
