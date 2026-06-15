<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');

// Reemplazar directamente los valores del cuadro en la fuente
$scss = str_replace(
    'background: rgba(15, 2, 5, 0.5) !important;',
    'background: transparent !important;',
    $scss
);
$scss = str_replace(
    'backdrop-filter: blur(20px) !important;',
    'backdrop-filter: none !important;',
    $scss
);
$scss = str_replace(
    '-webkit-backdrop-filter: blur(20px) !important;',
    '-webkit-backdrop-filter: none !important;',
    $scss
);
$scss = str_replace(
    "border: 1px solid rgba(255, 255, 255, 0.12) !important;\n    border-left: 5px solid #c62b3a !important;",
    "border: none !important;\n    border-left: none !important;",
    $scss
);
$scss = str_replace(
    'border-radius: 24px !important;',
    'border-radius: 0 !important;',
    $scss
);
$scss = str_replace(
    'box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45) !important;',
    'box-shadow: none !important;',
    $scss
);

set_config('scss', $scss, 'theme_moove');
purge_all_caches();
echo "Cuadro eliminado por str_replace directo\n";

// Verificar
$check = get_config('theme_moove', 'scss');
echo "rgba(15, 2, 5, 0.5) aún existe: " . (strpos($check,'rgba(15, 2, 5, 0.5)')!==false?'SI - problema':'NO - correcto') . "\n";
echo "border-left: 5px solid #c62b3a aún existe: " . (strpos($check,'border-left: 5px solid #c62b3a')!==false?'SI - problema':'NO - correcto') . "\n";
