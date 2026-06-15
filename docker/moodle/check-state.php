<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');
$footer = get_config('theme_moove', 'additionalhtmlfooter');
echo "Footer bytes: " . strlen($footer) . "\n";
echo "tau-v17: " . (strpos($footer,'tau-v17')!==false?'SI':'NO') . "\n";
echo "tau-logo-area: " . (strpos($footer,'tau-logo-area')!==false?'SI':'NO') . "\n";
echo "--- FOOTER COMPLETO ---\n";
echo $footer . "\n";
