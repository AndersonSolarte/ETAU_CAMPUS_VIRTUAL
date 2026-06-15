<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$footer = get_config('theme_moove', 'additionalhtmlfooter');
echo "Footer JS length: " . strlen($footer) . " bytes\n";

// Buscar cualquier referencia al hero/banner/overlay
foreach (['tau-banner','tau-logo','tau-deco','mooveslideshow','carousel-item','tau-official'] as $needle) {
    $count = substr_count($footer, $needle);
    if ($count > 0) {
        echo "Encontrado '$needle' x$count en footer\n";
    }
}

// Mostrar primeros 3000 caracteres del footer para ver qué JS hay
echo "\n=== FOOTER JS (primeros 2000 chars) ===\n";
echo substr($footer, 0, 2000) . "\n";

// Buscar el bloque que inserta tau-banner-card
$pos = strpos($footer, 'tau-banner-card');
if ($pos !== false) {
    echo "\n=== CONTEXTO tau-banner-card en footer (pos $pos) ===\n";
    echo substr($footer, max(0,$pos-200), 600) . "\n";
}
