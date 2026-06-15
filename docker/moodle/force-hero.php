<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Forzar CSS encima de todo lo existente
$scss = get_config('theme_moove', 'scss');

// Quitar parches tau anteriores para no acumular
foreach (['/* TAU-HERO-V2','/* TAU-FINAL','/* TAU-LOGO-HERO','/* TAU-HERO-SLIDE','/* FORCE-HERO'] as $m) {
    $p = strpos($scss, $m);
    if ($p !== false) $scss = rtrim(substr($scss, 0, $p));
}

$scss .= '

/* FORCE-HERO ════════════════════════════════════════════════ */

/* 1. Quitar el cuadro vino del hero */
.tau-banner-card {
    background: transparent !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    border: none !important;
    border-left: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

/* 2. Logo TAU + lema en el lado derecho */
.tau-logo-deco {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 14px !important;
}
.tau-deco-logo {
    width: clamp(160px,18%,210px) !important;
    height: auto !important;
    display: block !important;
    filter: drop-shadow(0 8px 36px rgba(0,0,0,.65)) !important;
    opacity: 1 !important;
    visibility: visible !important;
}
.tau-deco-lema {
    text-align: center !important;
    padding: 11px 16px !important;
    background: rgba(15,2,5,.82) !important;
    border: 1px solid rgba(198,43,58,.40) !important;
    border-top: 2px solid #c62b3a !important;
    border-radius: 12px !important;
    font-family: Georgia, serif !important;
    font-size: clamp(.74rem,.88vw,.85rem) !important;
    font-style: italic !important;
    color: rgba(255,255,255,.93) !important;
    line-height: 1.5 !important;
    width: clamp(160px,18%,220px) !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}
.tau-deco-lema span {
    display: block !important;
    font-family: system-ui, sans-serif !important;
    font-style: normal !important;
    font-size: .62rem !important;
    color: rgba(255,255,255,.42) !important;
    letter-spacing: .08em !important;
    text-transform: uppercase !important;
    margin-top: 6px !important;
}

/* FORCE-HERO-FIN ════════════════════════════════════════════ */
';

set_config('scss', $scss, 'theme_moove');

// Limpiar TODA la caché de Moodle, no solo la del tema
purge_all_caches();

echo "CSS forzado y caché purgada completamente\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";

// Verificar que el CSS de la tarjeta transparente quedó al final
$pos = strrpos(get_config('theme_moove','scss'), '.tau-banner-card');
echo "Último .tau-banner-card en posición: $pos\n";
