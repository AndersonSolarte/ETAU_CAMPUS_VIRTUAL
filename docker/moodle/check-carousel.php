<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$ch = curl_init('http://localhost/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
curl_close($ch);

// Buscar mooveslideshow
$pos = strpos($html, 'mooveslideshow');
if ($pos === false) { echo "mooveslideshow NOT FOUND in HTML\n"; exit; }

// Extraer 3000 chars alrededor
$start = max(0, $pos - 100);
echo substr($html, $start, 3000);
