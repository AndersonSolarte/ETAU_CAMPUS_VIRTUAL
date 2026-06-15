<?php
define('CLI_SCRIPT',true);chdir('/var/www/html');require('/var/www/html/config.php');
$s=get_config('theme_moove','scss');
preg_match_all('/\.tau-banner-card \{[^}]{0,300}/s',$s,$m);
foreach($m[0] as $h) echo trim(str_replace("\n"," ",$h))."\n---\n";
echo "\nNeedle test: " . (strpos($s,'background: transparent')!==false?'SI':'NO')."\n";
echo "dark bg: " . (strpos($s,'rgba(15, 2, 5, 0.5)')!==false?'SI':'NO')."\n";
