<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── SCSS: sólo fondo del hero + escudo estático ───────────────────────────────
$css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG v8
   ═══════════════════════════════════════════════════ */

#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important; pointer-events: none !important;
    position: absolute !important; inset: 0 !important; border: none !important;
}

/* Hero: fondo unificado — azul-marino con calor rojo al centro-izquierda */
#mooveslideshow .carousel-item {
    position: relative !important;
    background:
        radial-gradient(ellipse 72% 85% at 12% 50%, rgba(6,24,68,.95)  0%, transparent 70%),
        radial-gradient(ellipse 55% 65% at 50% 50%, rgba(18,38,90,.60)  0%, transparent 85%),
        radial-gradient(ellipse 48% 55% at 85% 50%, rgba(13,55,120,.40) 0%, transparent 65%),
        radial-gradient(ellipse 38% 45% at 82% 48%, rgba(255,255,255,.05) 0%, transparent 45%),
        #080f22 !important;
}

/* Grid de puntos azul muy sutil */
#mooveslideshow .carousel-item::before {
    content: "" !important;
    position: absolute !important; inset: 0 !important; pointer-events: none !important;
    background-image: radial-gradient(circle, rgba(100,150,255,.07) 1px, transparent 1px) !important;
    background-size: 44px 44px !important; z-index: 0 !important;
}

/* Viñeta bordes */
#mooveslideshow .carousel-item::after {
    content: "" !important;
    position: absolute !important; inset: 0 !important; pointer-events: none !important;
    background: radial-gradient(ellipse 110% 110% at 50% 50%, transparent 48%, rgba(0,0,0,.55) 100%) !important;
    z-index: 0 !important;
}

/* Barra bicolor izquierda */
#mooveslideshow::before {
    content: "" !important; position: absolute !important;
    left: 0 !important; top: 0 !important; width: 4px !important; height: 100% !important;
    background: linear-gradient(to bottom, #0d4f8b 0%, #0d4f8b 46%, #c0253a 46%, #c0253a 100%) !important;
    z-index: 20 !important;
}

/* Posicionamiento del slideshow */
#mooveslideshow {
    position: relative !important;
    overflow: visible !important;
}
#mooveslideshow .carousel-inner { overflow: hidden !important; }
#mooveslideshow .carousel-caption { position: relative !important; z-index: 5 !important; }

/* Card del texto: glassmorphism azul */
#mooveslideshow .carousel-caption {
    background: linear-gradient(135deg, rgba(5,14,40,.80), rgba(8,24,65,.65)) !important;
    border: 1px solid rgba(13,79,139,.30) !important;
    border-radius: 18px !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    box-shadow: 0 8px 32px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.07) !important;
    padding: 1.9rem 2.1rem !important;
}
#mooveslideshow .carousel-caption h5,
#mooveslideshow .carousel-caption .title {
    font-size: clamp(1.8rem,3.2vw,2.7rem) !important;
    font-weight: 800 !important; color: #fff !important;
    text-shadow: 0 2px 20px rgba(0,0,0,.5) !important;
}
#mooveslideshow .carousel-caption p { color: rgba(255,255,255,.76) !important; }
#mooveslideshow .carousel-caption .btn,
#mooveslideshow .carousel-caption a.btn {
    background: linear-gradient(135deg, #c0253a, #891b2c) !important;
    border: none !important; border-radius: 10px !important;
    color: #fff !important; font-weight: 700 !important;
    padding: .72rem 1.6rem !important;
    box-shadow: 0 6px 20px rgba(192,37,58,.38) !important;
}

/* ESCUDO: estático */
#mooveslideshow::after {
    content: "" !important; position: absolute !important;
    right: 6% !important; top: 50% !important;
    transform: translateY(-50%) !important;
    width: clamp(210px, 29%, 305px) !important;
    aspect-ratio: 1 !important;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size: 82% !important; background-repeat: no-repeat !important;
    background-position: center !important; background-color: transparent !important;
    border: none !important; outline: none !important;
    z-index: 9 !important; pointer-events: none !important;
    filter: drop-shadow(0 6px 36px rgba(0,0,0,.65)) brightness(1.04) !important;
    animation: tau-escudo-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-escudo-in {
    from { opacity: 0; transform: translateY(-47%) scale(.90); }
    to   { opacity: 1; transform: translateY(-50%) scale(1); }
}

@media (max-width: 900px) {
    #mooveslideshow::after { width: clamp(170px,22vw,220px); right: 2%; }
}
@media (max-width: 768px) {
    #mooveslideshow::after { display: none !important; }
}
';

// ── JS + STYLE (footer): aros + lema — TODO aquí, garantizado ────────────────
$footer_inject = '
<style id="tau-escudo-styles">
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

/* Lema */
.tau-lema-block {
    position: relative; text-align: center;
    background: linear-gradient(90deg, transparent, rgba(13,47,120,.18) 30%, rgba(13,79,139,.25) 50%, rgba(13,47,120,.18) 70%, transparent);
    border-top: 1px solid rgba(13,79,139,.35);
    border-bottom: 1px solid rgba(13,79,139,.15);
    padding: 14px 1rem; overflow: hidden;
}
.tau-lema-block::before {
    content: ""; position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(circle, rgba(13,79,139,.07) 1px, transparent 1px);
    background-size: 28px 28px;
}
.tau-lema-line {
    display: inline-flex; align-items: center; gap: 18px; position: relative;
}
.tau-lema-line::before, .tau-lema-line::after {
    content: ""; display: block; width: 54px; height: 1px;
}
.tau-lema-line::before { background: linear-gradient(90deg, transparent, #0d4f8b); }
.tau-lema-line::after  { background: linear-gradient(90deg, #0d4f8b, transparent); }
.tau-lema-quote {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(.77rem, 1.1vw, .92rem); font-style: italic;
    color: rgba(255,255,255,.88); letter-spacing: .05em; margin: 0;
    text-shadow: 0 1px 6px rgba(0,0,0,.4);
}
.tau-lema-quote strong {
    color: #fff; font-style: normal; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
}
.tau-lema-author {
    font-family: system-ui, Arial, sans-serif;
    font-size: clamp(.63rem, .88vw, .74rem);
    color: rgba(255,255,255,.40); letter-spacing: .09em; margin: 4px 0 0;
}
@media (max-width: 768px) {
    .tau-rings-wrap, .tau-lema-block { display: none !important; }
}
</style>

<script id="tau-escudo-script">
(function(){
    function tau_run() {
        var ss = document.getElementById("mooveslideshow");
        if (!ss) { setTimeout(tau_run, 200); return; }

        /* -- AROS SVG -- */
        if (!document.querySelector(".tau-rings-wrap")) {
            var w = document.createElement("div");
            w.className = "tau-rings-wrap";
            w.innerHTML = \'<svg viewBox="-32 -32 264 264" xmlns="http://www.w3.org/2000/svg">\' +
              \'<!-- Resplandor suave azul detrás del escudo -->\' +
              \'<circle class="tau-pulse" cx="100" cy="100" r="90" fill="rgba(13,55,140,.18)" stroke="none"/>\' +
              \'<!-- Aro exterior: pulso suave azul -->\' +
              \'<circle class="tau-pulse" cx="100" cy="100" r="118" fill="none" stroke="rgba(13,79,139,.38)" stroke-width="1.4" stroke-dasharray="8 14"/>\' +
              \'<!-- Aro medio: antihorario, blanco con nodos blancos -->\' +
              \'<g class="tau-g-ccw">\' +
                \'<circle cx="100" cy="100" r="108" fill="none" stroke="rgba(255,255,255,.24)" stroke-width="1.1" stroke-dasharray="5 18"/>\' +
                \'<circle class="tau-nw" cx="100" cy="-8"  r="4" fill="rgba(255,255,255,.82)"/>\' +
                \'<circle class="tau-nw" cx="208" cy="100" r="4" fill="rgba(255,255,255,.82)"/>\' +
                \'<circle class="tau-nw" cx="100" cy="208" r="4" fill="rgba(255,255,255,.82)"/>\' +
                \'<circle class="tau-nw" cx="-8"  cy="100" r="4" fill="rgba(255,255,255,.82)"/>\' +
              \'</g>\' +
              \'<!-- Aro interior: horario, blanco con nodos carmesí -->\' +
              \'<g class="tau-g-cw">\' +
                \'<circle cx="100" cy="100" r="96" fill="none" stroke="rgba(255,255,255,.32)" stroke-width="1.6" stroke-dasharray="16 8"/>\' +
                \'<circle class="tau-nd" cx="100" cy="4"   r="6.5" fill="#c0253a"/>\' +
                \'<circle class="tau-nd" cx="196" cy="100" r="6.5" fill="#c0253a"/>\' +
                \'<circle class="tau-nd" cx="100" cy="196" r="6.5" fill="#c0253a"/>\' +
                \'<circle class="tau-nd" cx="4"   cy="100" r="6.5" fill="#c0253a"/>\' +
              \'</g>\' +
            \'</svg>\';
            ss.appendChild(w);
        }

        /* -- LEMA -- */
        if (!document.querySelector(".tau-lema-block")) {
            var lema = document.createElement("div");
            lema.className = "tau-lema-block";
            lema.innerHTML =
                \'<div class="tau-lema-line">\' +
                  \'<p class="tau-lema-quote">&ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;</p>\' +
                \'</div>\' +
                \'<p class="tau-lema-author">Fray Guillermo de Castellana, OFMCap.</p>\';
            var next = ss.nextSibling;
            while (next && next.nodeType === 3) next = next.nextSibling; // saltar text nodes
            if (next) { ss.parentNode.insertBefore(lema, next); }
            else      { ss.parentNode.appendChild(lema); }
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", tau_run);
    } else {
        tau_run();
    }
})();
</script>
';

// ── Actualizar BD ─────────────────────────────────────────────────────────────
// SCSS
$scss = get_config('theme_moove', 'scss');
foreach ([
    '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG",
    '// ================================================================' . "\n// FRONTPAGE HERO",
] as $m) {
    $p = strpos($scss, $m);
    if ($p !== false) $scss = rtrim(substr($scss, 0, $p));
}
set_config('scss', $scss . "\n" . $css, 'theme_moove');

// Footer: limpiar todo lo tau anterior
$footer = get_config('theme_moove', 'additionalhtmlfooter');
foreach (['tau_hero_extras','injectEscudo','addTauElements','tau_init','tau_run',
          'tau-escudo-styles','tau-escudo-script','.tau-rings-wrap','.tau-lema'] as $needle) {
    while (strpos($footer, $needle) !== false) {
        foreach (['<style','<script'] as $tag) {
            $s = strpos($footer, $tag);
            if ($s === false) continue;
            $close = ($tag === '<style') ? '</style>' : '</script>';
            $e = strpos($footer, $close, $s);
            if ($e === false) continue;
            $footer = substr($footer, 0, $s) . substr($footer, $e + strlen($close));
            break;
        }
    }
}
set_config('additionalhtmlfooter', trim($footer) . "\n" . $footer_inject, 'theme_moove');

theme_reset_all_caches();
echo "OK v8 — CSS en footer garantizado\n";
echo "scss:   " . strlen(get_config('theme_moove','scss'))                 . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
