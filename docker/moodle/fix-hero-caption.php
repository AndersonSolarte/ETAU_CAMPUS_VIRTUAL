<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Quitar el bloque TAU-LOGO-HERO que tiene el carousel-caption roto
$scss = get_config('theme_moove', 'scss');
$marker = '/* TAU-LOGO-HERO';
$pos = strpos($scss, $marker);
if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));

// Nuevo bloque: solo posicionar el logo TAU, NO tocar el carousel-caption
$hero_css = '

/* TAU-LOGO-HERO ════════════════════════════════════════════ */
#mooveslideshow { position: relative !important; overflow: visible !important; }
#mooveslideshow .carousel-inner { overflow: hidden !important; }
#mooveslideshow::after {
    content: "" !important; position: absolute !important;
    right: 6% !important; top: 50% !important; transform: translateY(-50%) !important;
    width: clamp(180px,25%,260px) !important; aspect-ratio: 1 !important;
    background-image: url("/theme/tau_branding/assets/official/tau-official-icon.png") !important;
    background-size: contain !important; background-repeat: no-repeat !important;
    background-position: center !important; background-color: transparent !important;
    z-index: 9 !important; pointer-events: none !important;
    filter: drop-shadow(0 8px 40px rgba(0,0,0,.55)) !important;
    animation: tau-logo-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-logo-in {
    from { opacity:0; transform:translateY(-47%) scale(.85); }
    to   { opacity:1; transform:translateY(-50%) scale(1); }
}
@media(max-width:900px){ #mooveslideshow::after{width:clamp(130px,18vw,170px);right:2%;} }
@media(max-width:768px){ #mooveslideshow::after{display:none !important;} }
/* TAU-LOGO-HERO-FIN ════════════════════════════════════════ */
';

set_config('scss', $scss . "\n" . $hero_css, 'theme_moove');
theme_reset_all_caches();
echo "Hero caption restaurado + logo TAU correcto\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
