<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Core config (sin plugin)
$footer = get_config(false, 'additionalhtmlfooter');
echo "Core additionalhtmlfooter length: " . strlen($footer) . " bytes\n";

$head = get_config(false, 'additionalhtmlhead');
echo "Core additionalhtmlhead length: " . strlen($head) . " bytes\n";

foreach (['tau-banner','tau-banner-card','mooveslideshow','tau-official'] as $needle) {
    if (strpos($footer,$needle)!==false) echo "FOOTER contiene '$needle'\n";
    if (strpos($head,$needle)!==false) echo "HEAD contiene '$needle'\n";
}

// Ver el footer completo (posición del banner)
$pos = strpos($footer,'tau-banner');
if ($pos!==false) {
    echo "\n=== tau-banner en core footer ===\n";
    echo substr($footer, max(0,$pos-100), 1000);
}

// Mostrar los primeros 500 chars para orientarnos
echo "\n=== Core footer (primeros 500 chars) ===\n";
echo substr($footer, 0, 500);
