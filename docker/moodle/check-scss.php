<?php
define('CLI_SCRIPT',true);chdir('/var/www/html');require('/var/www/html/config.php');
$s=get_config('theme_moove','scss');
echo strlen($s)." bytes\n";
echo "min-height 440: ".(strpos($s,'440px')!==false?'SI':'NO')."\n";
echo "tau-ins-text: ".(strpos($s,'tau-ins-text')!==false?'SI':'NO')."\n";
echo "carousel-item img hidden: ".(strpos($s,'visibility: hidden')!==false?'SI':'NO')."\n";
echo "dark gradient: ".(strpos($s,'0e0812')!==false?'SI':'NO')."\n";
// mostrar el bloque del carousel-item
$pos=strpos($s,'min-height: 440px');
if($pos) echo "\n".substr($s,$pos-200,400)."\n";
