<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scsspre = get_config('theme_moove', 'scsspre');
$scss    = get_config('theme_moove', 'scss');

echo "=== SCSSPRE (" . strlen($scsspre) . " bytes) ===\n";
echo "tau-banner-card en scsspre: " . (strpos($scsspre,'tau-banner-card')!==false?'SI':'NO') . "\n";
echo "rgba(15, 2, 5 en scsspre: "  . (strpos($scsspre,'rgba(15, 2, 5')!==false?'SI':'NO') . "\n";
echo "border-left.*c62b3a en scsspre: " . (preg_match('/border-left.*c62b3a/',$scsspre)?'SI':'NO') . "\n";

echo "\n=== SCSS (" . strlen($scss) . " bytes) ===\n";
echo "tau-banner-card en scss: " . (strpos($scss,'tau-banner-card')!==false?'SI':'NO') . "\n";
echo "rgba(15, 2, 5 en scss: "  . (strpos($scss,'rgba(15, 2, 5')!==false?'SI':'NO') . "\n";
echo "border-radius: 24px en scss: " . (strpos($scss,'border-radius: 24px')!==false?'SI':'NO') . "\n";

// Mostrar todas las ocurrencias de tau-banner-card en scss
preg_match_all('/.{0,30}tau-banner-card.{0,80}/s', $scss, $m);
echo "\nOcurrencias tau-banner-card en scss:\n";
foreach($m[0] as $hit) echo "  " . trim(str_replace("\n"," ",$hit)) . "\n";

// Mostrar todas las ocurrencias en scsspre
preg_match_all('/.{0,30}tau-banner-card.{0,80}/s', $scsspre, $m2);
echo "\nOcurrencias tau-banner-card en scsspre:\n";
foreach($m2[0] as $hit) echo "  " . trim(str_replace("\n"," ",$hit)) . "\n";
