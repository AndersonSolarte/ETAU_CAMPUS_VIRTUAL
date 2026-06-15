<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$ch = curl_init('http://localhost/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
curl_close($ch);

echo "HTML size: " . strlen($html) . "\n";
echo "TAU HERO CSS in page: " . (strpos($html, 'TAU HERO CSS') !== false ? 'SI' : 'NO') . "\n";
echo "buildHero in page: " . (strpos($html, 'buildHero') !== false ? 'SI' : 'NO') . "\n";
echo "tau-ins-text in page: " . (strpos($html, 'tau-ins-text') !== false ? 'SI' : 'NO') . "\n";
echo "additionalhtmlfooter content: " . substr(get_config(false, 'additionalhtmlfooter'), 0, 100) . "\n";

// Buscar dónde aparece 'buildHero' en el HTML
$pos = strpos($html, 'buildHero');
if ($pos !== false) {
    echo "\n=== Context buildHero in HTML ===\n";
    echo substr($html, max(0, $pos - 200), 400);
}
