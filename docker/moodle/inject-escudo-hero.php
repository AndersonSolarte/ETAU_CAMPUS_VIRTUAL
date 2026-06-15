<?php
/**
 * Inyecta el CSS del escudo CESMAG en el hero de moove y purga el caché.
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$escudo_css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG — Hero institucional
   ═══════════════════════════════════════════════════ */

/* Ocultar imagen stock del carrusel */
#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
    border: none !important;
    outline: none !important;
}

/* Escudo institucional — lado derecho del hero */
#mooveslideshow {
    position: relative;
}
#mooveslideshow::after {
    content: "";
    position: absolute;
    right: 7%;
    top: 50%;
    transform: translateY(-50%);
    width: clamp(200px, 30%, 320px);
    aspect-ratio: 1 / 1;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png");
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.85;
    z-index: 3;
    pointer-events: none;
    animation: tau-escudo-in 0.8s ease both;
    filter: drop-shadow(0 8px 32px rgba(0,0,0,0.45));
}

@keyframes tau-escudo-in {
    from { opacity: 0; transform: translateY(-50%) scale(0.90); }
    to   { opacity: 0.85; transform: translateY(-50%) scale(1); }
}

@media (max-width: 768px) {
    #mooveslideshow::after {
        display: none;
    }
}
';

// Leer el valor actual de scss
$current = get_config('theme_moove', 'scss');

// Quitar bloque anterior si ya existe (re-aplicación idempotente)
$marker_start = '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG";
$marker_end   = '@media (max-width: 768px) {' . "\n    #mooveslideshow::after {" . "\n        display: none;" . "\n    }\n}";

$pos_start = strpos($current, $marker_start);
if ($pos_start !== false) {
    $pos_end = strpos($current, $marker_end, $pos_start);
    if ($pos_end !== false) {
        $current = substr($current, 0, $pos_start) . substr($current, $pos_end + strlen($marker_end));
    }
}

$new_scss = rtrim($current) . "\n" . $escudo_css;

set_config('scss', $new_scss, 'theme_moove');

// Purgar caches de tema
theme_reset_all_caches();

echo "OK: CSS del escudo inyectado y caché purgado.\n";
echo "Tamaño scss: " . strlen($new_scss) . " bytes\n";
