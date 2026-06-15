<?php
/**
 * Cambia la paleta completa del sitio de rojo/vino a azul institucional CESMAG.
 * Azul institucional: #0d4f8b
 * Se aplica directamente en la BD de theme_moove (campo scss y additionalhtmlfooter)
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$replacements = [
    // Rojo principal → azul institucional
    '#c62b3a' => '#0d4f8b',
    '#C62B3A' => '#0D4F8B',
    // Rojos oscuros hover/variantes → azules oscuros
    '#a32230' => '#0a3d6b',
    '#8e1e2c' => '#083259',
    '#8e1f2d' => '#083259',
    '#9e1e2d' => '#0a3d6b',
    '#9e2332' => '#0a3d6b',
    '#791723' => '#063050',
    '#5f121d' => '#052040',
    '#4b0f18' => '#031828',
    '#b42838' => '#0c4880',
    '#b11f35' => '#0a3d6b',
    '#7f0d1c' => '#063050',
    '#67141f' => '#052040',
    '#8f1f2d' => '#083259',
    '#b32939' => '#0c4880',
    '#aa2635' => '#0a3d6b',
    '#b73345' => '#0c4880',
    '#992737' => '#083259',
    '#781c2a' => '#063050',
    '#54131d' => '#031828',
    '#8b1a29' => '#063050',
    // Rojos claros / rosa → azules claros
    '#de5567' => '#2d7dd2',
    '#e87a84' => '#4d9de0',
    '#e8717c' => '#4d9de0',
    '#d14d5f' => '#2d7dd2',
    '#de3c4f' => '#1a6ab5',
    '#1a6ab5' => '#1a6ab5', // ya está bien
    // rgba con valores del rojo
    'rgba(198,43,58,'    => 'rgba(13,79,139,',
    'rgba(198, 43, 58,'  => 'rgba(13, 79, 139,',
    'rgba(198,43,58 ,'   => 'rgba(13,79,139,',
    'rgba(88,16,28,'     => 'rgba(8,40,90,',
    'rgba(87,17,28,'     => 'rgba(8,40,90,',
    'rgba(232,113,124,'  => 'rgba(77,157,224,',
    'rgba(232, 113, 124,' => 'rgba(77, 157, 224,',
    // Color de marca base
    '#1f4b99' => '#0d4f8b',
    // Gradientes con múltiples stops — reemplazamos el stop de rojo
    '5f121d 0%, #791723 24%, #9e1e2d 52%, #c62b3a 78%, #de5567 100%'
        => '052040 0%, #063050 24%, #083259 52%, #0d4f8b 78%, #2d7dd2 100%',
    '5f121d 0%, #791723 22%, #9e1e2d 54%, #c62b3a 80%, #de5567 100%'
        => '052040 0%, #063050 22%, #083259 54%, #0d4f8b 80%, #2d7dd2 100%',
    // Gradient del footer (cambia a azul oscuro)
    'linear-gradient(135deg, #2d050a 0%, #4a0e17 50%, #5f121d 100%)'
        => 'linear-gradient(135deg, #030d1e 0%, #052040 50%, #083259 100%)',
    'linear-gradient(135deg, #1b0205 0%, #2a080d 50%, #380a11 100%)'
        => 'linear-gradient(135deg, #020a18 0%, #041830 50%, #062348 100%)',
    // fef2f3 (hover fondo rosa) → hover fondo azul claro
    '#fef2f3' => '#f0f5ff',
    '#fff1f3' => '#eff5ff',
    '#fff8f9' => '#f0f7ff',
    '#fff5f6' => '#f0f5ff',
    '#fcfbfb' => '#f8faff',
    '#fff8f9 0%, #fff 100%' => '#f0f7ff 0%, #fff 100%',
];

// Aplicar en campo scss
$scss = get_config('theme_moove', 'scss');
$original_len = strlen($scss);
$scss = str_replace(array_keys($replacements), array_values($replacements), $scss);
set_config('scss', $scss, 'theme_moove');
echo "[scss] " . $original_len . " → " . strlen($scss) . " bytes\n";

// Aplicar en footer (el JS/CSS del escudo y lema)
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$footer = str_replace(array_keys($replacements), array_values($replacements), $footer);
// El lema y aros son azules de por sí, no tocar

// Aplicar en additionalhtmlhead si existe
$head = get_config('theme_moove', 'additionalhtmlhead');
if ($head) {
    $head = str_replace(array_keys($replacements), array_values($replacements), $head);
    set_config('additionalhtmlhead', $head, 'theme_moove');
}

// brandcolor
set_config('brandcolor', '#0d4f8b', 'theme_moove');
set_config('buttonbrandcolor', '#0d4f8b', 'theme_moove');
set_config('linkcolor', '#0d4f8b', 'theme_moove');

theme_reset_all_caches();
echo "Paleta azul aplicada\n";
echo "brandcolor: " . get_config('theme_moove', 'brandcolor') . "\n";
