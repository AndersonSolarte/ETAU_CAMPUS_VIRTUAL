<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');

// Quitar bloque TAU-HERO-V2 anterior
$p = strpos($scss, '/* TAU-HERO-V2');
if ($p !== false) $scss = rtrim(substr($scss, 0, $p));

$scss .= '

/* TAU-HERO-V2 ══════════════════════════════════════════════ */
#mooveslideshow::after  { content:none !important; display:none !important; }
#mooveslideshow::before { content:none !important; display:none !important; }

/* Hero más alto para que quepa logo + lema */
#mooveslideshow,
#mooveslideshow .carousel,
#mooveslideshow .carousel-inner,
#mooveslideshow .carousel-item {
    min-height: 460px !important;
    overflow: visible !important;
}
/* Solo la imagen de fondo se recorta */
#mooveslideshow .carousel-item > .carousel-caption,
#mooveslideshow .carousel-item > img,
#mooveslideshow .carousel-item > a > img {
    overflow: hidden !important;
}

/* Wrapper: logo arriba + lema abajo */
.tau-logo-area {
    position: absolute !important;
    right: 5% !important;
    top: 50% !important;
    transform: translateY(-55%) !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 12px !important;
    z-index: 20 !important;
    pointer-events: none !important;
    width: clamp(190px,24%,260px) !important;
    animation: tau-area-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-area-in {
    from { opacity:0; transform:translateY(-58%) scale(.88); }
    to   { opacity:1; transform:translateY(-55%) scale(1); }
}
.tau-logo-area img.tau-logo-img {
    width: 80% !important;
    height: auto !important;
    display: block !important;
    margin: 0 auto !important;
    filter: drop-shadow(0 8px 36px rgba(0,0,0,.60)) !important;
}
/* Lema debajo del logo */
.tau-hero-lema {
    width: 100% !important;
    text-align: center !important;
    padding: 10px 14px 11px !important;
    background: rgba(20,3,6,.80) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(198,43,58,.35) !important;
    border-top: 2px solid #c62b3a !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,.45) !important;
}
.tau-hero-lema-q {
    font-family: Georgia, "Times New Roman", serif !important;
    font-size: clamp(.73rem,.88vw,.85rem) !important;
    font-style: italic !important;
    color: rgba(255,255,255,.93) !important;
    margin: 0 0 5px !important;
    line-height: 1.45 !important;
    text-shadow: 0 1px 5px rgba(0,0,0,.5) !important;
}
.tau-hero-lema-q strong {
    color: #fff !important; font-style: normal !important;
    font-weight: 700 !important; text-transform: uppercase !important;
}
.tau-hero-lema-autor {
    font-size: clamp(.58rem,.72vw,.66rem) !important;
    color: rgba(255,255,255,.45) !important;
    letter-spacing: .08em !important;
    text-transform: uppercase !important;
    margin: 0 !important;
    font-family: system-ui, sans-serif !important;
}
@media(max-width:900px){
    .tau-logo-area { right:1% !important; width:clamp(150px,19vw,190px) !important; }
}
@media(max-width:768px){ .tau-logo-area { display:none !important; } }
/* TAU-HERO-V2-FIN ══════════════════════════════════════════ */
';

set_config('scss', $scss, 'theme_moove');
theme_reset_all_caches();
echo "Hero height fix aplicado\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
