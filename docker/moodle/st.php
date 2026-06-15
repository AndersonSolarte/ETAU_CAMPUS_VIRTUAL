<?php
define('CLI_SCRIPT',true);chdir('/var/www/html');require('/var/www/html/config.php');
$scss=get_config('theme_moove','scss');
$footer=get_config(false,'additionalhtmlfooter');
echo 'SCSS:'.strlen($scss).' footer:'.strlen($footer)."\n";
echo 'FIX-HERO:'.(strpos($scss,'FIX-HERO-FINAL')!==false?'SI':'NO')."\n";
echo 'transparent:'.(strpos($scss,'background:transparent')!==false?'SI':'NO')."\n";
echo 'dark-card:'.(strpos($scss,'rgba(15,2,5,0.65)')!==false?'SI':'NO')."\n";
