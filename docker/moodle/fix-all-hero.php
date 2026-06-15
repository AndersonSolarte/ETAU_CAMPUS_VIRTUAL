<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// 1. Ocultar la imagen del slider (escudo CESMAG) y hacer card transparente en SCSS
$scss = get_config('theme_moove', 'scss');

// Quitar TODOS los bloques FORCE/TAU anteriores para no acumular
foreach (['/* FORCE-HERO','/* TAU-HERO','/* TAU-FINAL','/* TAU-LOGO','/* FORCE-CARD','/* FIX-HERO'] as $m) {
    $pos = strpos($scss, $m);
    if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));
}

// Appender CSS FINAL que gana sobre todo
$scss .= '

/* FIX-HERO-FINAL ══════════════════════════════════════════════════ */

/* Ocultar imagen de fondo del slider (escudo CESMAG) */
#mooveslideshow .carousel-item > img,
#mooveslideshow .carousel-item > a > img {
    display: none !important;
    visibility: hidden !important;
}

/* Card izquierda completamente transparente */
.tau-banner-card {
    background: transparent !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    border: none !important;
    border-left: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    max-width: 460px !important;
}

/* Overlay: ambos lados bien distribuidos */
.tau-banner-overlay {
    position: absolute !important;
    top: 0 !important; left: 0 !important;
    width: 100% !important; height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0 6% 0 8% !important;
    z-index: 10 !important;
    box-sizing: border-box !important;
}

/* Inscripción sin caja */
.tau-logo-deco {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 0 !important;
    flex-shrink: 0 !important;
    background: transparent !important;
}
.tau-deco-logo {
    width: clamp(150px, 15vw, 195px) !important;
    height: auto !important;
    display: block !important;
    filter: drop-shadow(0 8px 28px rgba(0,0,0,.6)) !important;
    margin-bottom: 14px !important;
}
.tau-ins-wrap {
    text-align: center !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    max-width: 200px !important;
}
.tau-ins-line {
    display: block !important;
    width: 32px !important;
    height: 2px !important;
    background: #c62b3a !important;
    margin: 0 auto 10px !important;
    border-radius: 1px !important;
}
.tau-ins-text {
    font-family: Georgia, "Times New Roman", serif !important;
    font-style: italic !important;
    font-size: .98rem !important;
    color: rgba(255,255,255,.95) !important;
    line-height: 1.55 !important;
    margin: 0 0 7px !important;
    display: block !important;
}
.tau-ins-attr {
    font-family: system-ui, sans-serif !important;
    font-style: normal !important;
    font-size: .58rem !important;
    color: rgba(255,255,255,.38) !important;
    letter-spacing: .1em !important;
    text-transform: uppercase !important;
    display: block !important;
}

/* FIX-HERO-FINAL-FIN ═══════════════════════════════════════════════ */
';

set_config('scss', $scss, 'theme_moove');
echo "[ok] SCSS actualizado: " . strlen($scss) . " bytes\n";

// 2. additionalhtmlfooter: solo CSS que respalda, SIN JS (tau_frontpage.js lo hace)
set_config('additionalhtmlfooter', '');
echo "[ok] additionalhtmlfooter vaciado (tau_frontpage.js maneja el JS)\n";

// 3. Borrar cache compilada
global $CFG;
$dirs = [
    $CFG->dataroot . '/localcache/theme',
    $CFG->localcachedir . '/theme',
];
$total = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    $n = 0;
    foreach ($it as $f) { if($f->isFile()){unlink($f->getRealPath());$n++;} elseif($f->isDir()){@rmdir($f->getRealPath());} }
    echo "Borrados $n en $dir\n"; $total+=$n;
}
echo "[ok] Cache disco: $total archivos\n";

theme_reset_all_caches();
purge_all_caches();
echo "[ok] Caches Moodle purgadas\nRecarga con Ctrl+Shift+R\n";
