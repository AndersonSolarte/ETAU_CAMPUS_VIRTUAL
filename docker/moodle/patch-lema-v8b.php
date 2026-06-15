<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Reemplazar solo el <style id="tau-escudo-styles"> en el footer
$footer = get_config('theme_moove', 'additionalhtmlfooter');

$old_style_start = '<style id="tau-escudo-styles">';
$old_style_end   = '</style>';
$s = strpos($footer, $old_style_start);
$e = strpos($footer, $old_style_end, $s);

$new_style = '<style id="tau-escudo-styles">
/* Contenedor aros */
.tau-rings-wrap {
    position: absolute;
    right: 6%;
    top: 50%;
    transform: translateY(-50%);
    width: clamp(210px, 29%, 305px);
    aspect-ratio: 1;
    pointer-events: none;
    z-index: 12;
    overflow: visible;
}
.tau-rings-wrap svg { width: 100%; height: 100%; overflow: visible; }

/* Animaciones */
@keyframes tau-spin-cw  { to { transform: rotate(360deg);  } }
@keyframes tau-spin-ccw { to { transform: rotate(-360deg); } }
@keyframes tau-glow-pulse { 0%,100% { opacity: .20; } 50% { opacity: .55; } }
@keyframes tau-node-red   { from { r: 4;   opacity: .50; } to { r: 7;   opacity: 1.0; } }
@keyframes tau-node-white { from { r: 3;   opacity: .30; } to { r: 5.5; opacity: .85; } }

.tau-g-cw  { animation: tau-spin-cw  22s linear infinite; transform-origin: 100px 100px; transform-box: fill-box; }
.tau-g-ccw { animation: tau-spin-ccw 32s linear infinite; transform-origin: 100px 100px; transform-box: fill-box; }
.tau-pulse { animation: tau-glow-pulse 4.5s ease-in-out infinite; }
.tau-nd    { animation: tau-node-red   3s   ease-in-out infinite alternate; }
.tau-nd:nth-child(2) { animation-delay: -.75s; }
.tau-nd:nth-child(3) { animation-delay: -1.5s; }
.tau-nd:nth-child(4) { animation-delay: -2.25s; }
.tau-nw    { animation: tau-node-white 3.8s ease-in-out infinite alternate; }
.tau-nw:nth-child(2) { animation-delay: -.95s; }
.tau-nw:nth-child(3) { animation-delay: -1.9s; }
.tau-nw:nth-child(4) { animation-delay: -2.85s; }

/* ── LEMA ── */
.tau-lema-block {
    position: relative;
    text-align: center;
    /* Fondo oscuro marino que conecta visualmente con el hero */
    background: linear-gradient(180deg, #07112b 0%, #0a1835 60%, #081020 100%);
    border-top: 2px solid #0d4f8b;
    border-bottom: 1px solid rgba(13,79,139,.30);
    padding: 18px 1rem 16px;
    overflow: hidden;
}
.tau-lema-block::before {
    content: ""; position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(circle, rgba(13,79,139,.10) 1px, transparent 1px);
    background-size: 28px 28px;
}
/* Línea decorativa horizontal con puntos de color */
.tau-lema-block::after {
    content: ""; position: absolute;
    bottom: 0; left: 50%; transform: translateX(-50%);
    width: 60px; height: 2px;
    background: linear-gradient(90deg, #0d4f8b, #c0253a, #0d4f8b);
}
.tau-lema-line {
    display: inline-flex; align-items: center; gap: 18px; position: relative;
}
/* Líneas laterales — AZULES */
.tau-lema-line::before, .tau-lema-line::after {
    content: ""; display: block; width: 60px; height: 1.5px;
}
.tau-lema-line::before { background: linear-gradient(90deg, transparent, #0d4f8b); }
.tau-lema-line::after  { background: linear-gradient(90deg, #0d4f8b, transparent); }

/* Texto — BLANCO */
.tau-lema-quote {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(.80rem, 1.15vw, .96rem);
    font-style: italic;
    color: rgba(255,255,255,.92);   /* ← BLANCO */
    letter-spacing: .07em;
    margin: 0;
    text-shadow: 0 1px 8px rgba(0,0,0,.5);
}
.tau-lema-quote strong {
    color: #ffffff;                  /* ← BLANCO puro en las palabras clave */
    font-style: normal;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
}
.tau-lema-author {
    font-family: system-ui, Arial, sans-serif;
    font-size: clamp(.65rem, .90vw, .76rem);
    color: rgba(255,255,255,.45);    /* gris claro sobre oscuro */
    letter-spacing: .10em;
    margin: 5px 0 0;
    text-transform: uppercase;
}
@media (max-width: 768px) {
    .tau-rings-wrap { display: none !important; }
    .tau-lema-block { padding: 12px .75rem; }
}
</style>';

if ($s !== false && $e !== false) {
    $footer = substr($footer, 0, $s) . $new_style . substr($footer, $e + strlen($old_style_end));
    set_config('additionalhtmlfooter', $footer, 'theme_moove');
    theme_reset_all_caches();
    echo "OK patch lema v8b — texto blanco, bordes azules, fondo marino\n";
    echo "footer: " . strlen($footer) . " bytes\n";
} else {
    echo "ERROR: no se encontró el bloque <style id=\"tau-escudo-styles\">\n";
}
