<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── CSS ───────────────────────────────────────────────────────────────────────
$css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG v7 — Rediseño institucional azul/rojo
   ═══════════════════════════════════════════════════ */

#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
    border: none !important;
}

/* HERO: fondo institucional azul profundo — combina con el escudo CESMAG */
#mooveslideshow .carousel-item {
    position: relative !important;
    background:
        radial-gradient(ellipse 70% 90% at 14% 52%,  rgba(8,38,96,.92)  0%, rgba(4,20,55,.55) 50%, transparent 72%),
        radial-gradient(ellipse 55% 60% at 84% 16%,  rgba(13,79,139,.28) 0%, transparent 68%),
        radial-gradient(ellipse 40% 50% at 90% 88%,  rgba(6,28,72,.65)  0%, transparent 52%),
        radial-gradient(ellipse 60% 65% at 50% 50%,  rgba(198,43,58,.10) 0%, transparent 80%),
        #060f1e !important;
    background-size: cover !important;
}

/* Grilla de puntos — azul institucional */
#mooveslideshow .carousel-item::before {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    pointer-events: none !important;
    background-image: radial-gradient(circle, rgba(13,79,139,.12) 1px, transparent 1px) !important;
    background-size: 48px 48px !important;
    z-index: 0 !important;
}

/* Arco decorativo diagonal — parte superior derecha */
#mooveslideshow .carousel-item::after {
    content: "" !important;
    position: absolute !important;
    inset: 0 !important;
    pointer-events: none !important;
    background:
        radial-gradient(circle at 82% 50%, transparent 220px, rgba(13,79,139,.12) 221px, rgba(13,79,139,.12) 224px, transparent 225px),
        radial-gradient(circle at 82% 50%, transparent 320px, rgba(13,79,139,.08) 321px, rgba(13,79,139,.08) 323px, transparent 324px),
        radial-gradient(circle at 82% 50%, transparent 440px, rgba(13,79,139,.05) 441px, rgba(13,79,139,.05) 443px, transparent 444px),
        radial-gradient(ellipse 100% 100% at 50% 50%, transparent 52%, rgba(0,0,0,.40) 100%) !important;
    z-index: 0 !important;
}

/* Barra acento izquierda — bicolor institucional */
#mooveslideshow::before {
    content: "" !important;
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 4px !important;
    height: 100% !important;
    background: linear-gradient(to bottom,
        #0d4f8b 0%,
        #0d4f8b 45%,
        #c0253a 45%,
        #c0253a 100%) !important;
    z-index: 10 !important;
}

/* POSICIONAMIENTO BASE del slideshow */
#mooveslideshow {
    position: relative !important;
    overflow: visible !important;   /* permite que los aros SVG sobresalgan */
}
#mooveslideshow .carousel-inner {
    overflow: hidden !important;    /* los slides siguen recortados */
}

/* Captions con z-index por encima del fondo */
#mooveslideshow .carousel-caption,
#mooveslideshow .carousel-inner .caption {
    position: relative !important;
    z-index: 5 !important;
}

/* Caption: fondo glassmorphism azul oscuro */
#mooveslideshow .carousel-caption {
    background: linear-gradient(135deg,
        rgba(6,18,46,.75) 0%,
        rgba(8,28,72,.60) 100%) !important;
    border: 1px solid rgba(13,79,139,.35) !important;
    border-radius: 18px !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    box-shadow:
        0 8px 32px rgba(0,0,0,.40),
        inset 0 1px 0 rgba(255,255,255,.08) !important;
    padding: 2rem 2.2rem !important;
}

/* Tipografía del caption */
#mooveslideshow .carousel-caption h5,
#mooveslideshow .carousel-caption .title {
    font-size: clamp(1.8rem, 3.2vw, 2.7rem) !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    letter-spacing: -.02em !important;
    text-shadow: 0 2px 20px rgba(0,0,0,.50) !important;
}
#mooveslideshow .carousel-caption p,
#mooveslideshow .carousel-caption .description {
    color: rgba(255,255,255,.78) !important;
    font-size: clamp(.92rem, 1.3vw, 1.05rem) !important;
    line-height: 1.65 !important;
}
#mooveslideshow .carousel-caption .btn,
#mooveslideshow .carousel-caption a.btn {
    background: linear-gradient(135deg, #c0253a, #8b1524) !important;
    border: none !important;
    border-radius: 10px !important;
    color: #fff !important;
    font-weight: 700 !important;
    padding: .72rem 1.6rem !important;
    box-shadow: 0 6px 20px rgba(192,37,58,.38) !important;
    transition: all 200ms ease !important;
}
#mooveslideshow .carousel-caption .btn:hover {
    background: linear-gradient(135deg, #d42d45, #c0253a) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 28px rgba(192,37,58,.50) !important;
}

/* ESCUDO: estático, sin borde rectangulary sin rotación */
#mooveslideshow::after {
    content: "" !important;
    position: absolute !important;
    right: 6% !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: clamp(210px, 29%, 310px) !important;
    aspect-ratio: 1 / 1 !important;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size: 84% auto !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    background-color: transparent !important;
    border: none !important;
    outline: none !important;
    z-index: 9 !important;
    pointer-events: none !important;
    filter: drop-shadow(0 8px 40px rgba(0,0,0,.60)) brightness(1.05) !important;
    animation: tau-escudo-appear .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-escudo-appear {
    from { opacity: 0; transform: translateY(-48%) scale(.92); }
    to   { opacity: 1; transform: translateY(-50%) scale(1);   }
}

/* Lema institucional */
.tau-lema-block {
    position: relative;
    background: linear-gradient(90deg,
        rgba(6,18,46,.0),
        rgba(13,47,120,.15) 30%,
        rgba(13,79,139,.22) 50%,
        rgba(13,47,120,.15) 70%,
        rgba(6,18,46,.0));
    border-top: 1px solid rgba(13,79,139,.38);
    border-bottom: 1px solid rgba(13,79,139,.18);
    padding: 14px 0;
    text-align: center;
    overflow: hidden;
}
.tau-lema-block::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image: radial-gradient(circle, rgba(13,79,139,.06) 1px, transparent 1px);
    background-size: 28px 28px;
}
.tau-lema-block__line {
    display: inline-flex;
    align-items: center;
    gap: 16px;
    position: relative;
}
.tau-lema-block__line::before {
    content: "";
    display: block;
    width: 56px;
    height: 1px;
    background: linear-gradient(90deg, transparent, #0d4f8b);
}
.tau-lema-block__line::after {
    content: "";
    display: block;
    width: 56px;
    height: 1px;
    background: linear-gradient(90deg, #0d4f8b, transparent);
}
.tau-lema-block__quote {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(.77rem, 1.1vw, .92rem);
    font-style: italic;
    color: rgba(255,255,255,.88);
    letter-spacing: .05em;
    margin: 0;
    text-shadow: 0 1px 6px rgba(0,0,0,.4);
}
.tau-lema-block__quote strong {
    color: #fff;
    font-style: normal;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-size: 1.04em;
}
.tau-lema-block__author {
    font-family: system-ui, Arial, sans-serif;
    font-size: clamp(.63rem, .88vw, .74rem);
    color: rgba(255,255,255,.42);
    letter-spacing: .09em;
    margin: 4px 0 0;
}

/* Aros SVG */
.tau-rings-wrap {
    position: absolute;
    right: 6%;
    top: 50%;
    transform: translateY(-50%);
    width: clamp(210px, 29%, 310px);
    aspect-ratio: 1 / 1;
    pointer-events: none;
    z-index: 8;
    overflow: visible;
}
.tau-rings-wrap svg { width: 100%; height: 100%; overflow: visible; }

/* Animaciones de los aros */
.tau-r1 { animation: tau-cw  22s linear infinite; transform-origin: 100px 100px; }
.tau-r2 { animation: tau-ccw 32s linear infinite; transform-origin: 100px 100px; }
.tau-r3 { animation: tau-pulse 4.5s ease-in-out infinite; }
.tau-nd { animation: tau-nd 3s ease-in-out infinite alternate; }
.tau-nd:nth-child(2){animation-delay:-.75s}
.tau-nd:nth-child(3){animation-delay:-1.5s}
.tau-nd:nth-child(4){animation-delay:-2.25s}
.tau-nw { animation: tau-nw 3.8s ease-in-out infinite alternate; }
.tau-nw:nth-child(2){animation-delay:-.95s}
.tau-nw:nth-child(3){animation-delay:-1.9s}
.tau-nw:nth-child(4){animation-delay:-2.85s}

@keyframes tau-cw   { to { transform: rotate(360deg);  } }
@keyframes tau-ccw  { to { transform: rotate(-360deg); } }
@keyframes tau-pulse{ 0%,100%{opacity:.20}50%{opacity:.52} }
@keyframes tau-nd   { from{opacity:.50}to{opacity:1.0} }
@keyframes tau-nw   { from{opacity:.30}to{opacity:.82} }

@media (max-width: 900px) {
    #mooveslideshow::after { width: clamp(170px,23vw,230px); right: 2%; }
    .tau-rings-wrap         { width: clamp(170px,23vw,230px); right: 2%; }
}
@media (max-width: 768px) {
    #mooveslideshow::after  { display: none !important; }
    .tau-rings-wrap, .tau-lema-block { display: none !important; }
}
';

// ── JS ────────────────────────────────────────────────────────────────────────
$js = '
<script>
(function(){
  function tau_init(){
    var ss = document.getElementById("mooveslideshow");
    if (!ss) return;

    // Aros SVG alrededor del escudo
    if (!ss.querySelector(".tau-rings-wrap")) {
      var wrap = document.createElement("div");
      wrap.className = "tau-rings-wrap";
      // viewBox más grande para que los aros exteriores no se corten
      wrap.innerHTML = `<svg viewBox="-30 -30 260 260" xmlns="http://www.w3.org/2000/svg">

  <!-- Aro 3: pulso exterior — azul institucional -->
  <circle class="tau-r3" cx="100" cy="100" r="122"
    fill="none" stroke="rgba(13,79,139,.35)" stroke-width="1.4"
    stroke-dasharray="8 12"/>

  <!-- Aro 2: antihorario — blanco tenue con nodos blancos -->
  <g class="tau-r2">
    <circle cx="100" cy="100" r="112"
      fill="none" stroke="rgba(255,255,255,.22)" stroke-width="1"
      stroke-dasharray="4 18"/>
    <circle class="tau-nw" cx="100" cy="-12" r="4"   fill="rgba(255,255,255,.80)"/>
    <circle class="tau-nw" cx="212" cy="100" r="4"   fill="rgba(255,255,255,.80)"/>
    <circle class="tau-nw" cx="100" cy="212" r="4"   fill="rgba(255,255,255,.80)"/>
    <circle class="tau-nw" cx="-12" cy="100" r="4"   fill="rgba(255,255,255,.80)"/>
  </g>

  <!-- Aro 1: horario — blanco con nodos carmesí -->
  <g class="tau-r1">
    <circle cx="100" cy="100" r="100"
      fill="none" stroke="rgba(255,255,255,.30)" stroke-width="1.6"
      stroke-dasharray="16 8"/>
    <circle class="tau-nd" cx="100" cy="0"   r="6" fill="#c0253a"/>
    <circle class="tau-nd" cx="200" cy="100" r="6" fill="#c0253a"/>
    <circle class="tau-nd" cx="100" cy="200" r="6" fill="#c0253a"/>
    <circle class="tau-nd" cx="0"   cy="100" r="6" fill="#c0253a"/>
  </g>

  <!-- Resplandor detrás del escudo — azul institucional -->
  <circle cx="100" cy="100" r="82"
    fill="none" stroke="rgba(13,79,139,.22)" stroke-width="28"/>

</svg>`;
      ss.appendChild(wrap);
    }

    // Lema debajo del slideshow
    if (!document.querySelector(".tau-lema-block")) {
      var lema = document.createElement("div");
      lema.className = "tau-lema-block";
      lema.innerHTML = `<div class="tau-lema-block__line">
  <p class="tau-lema-block__quote">
    &ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;
  </p>
</div>
<p class="tau-lema-block__author">Fray Guillermo de Castellana, OFMCap.</p>`;
      ss.parentNode.insertBefore(lema, ss.nextSibling);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", tau_init);
  } else {
    tau_init();
  }
})();
</script>
';

// ── BD ────────────────────────────────────────────────────────────────────────
$scss = get_config('theme_moove', 'scss');
$marker = '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG";
$pos = strpos($scss, $marker);
if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));

// Quitar también el bloque FRONTPAGE HERO anterior que usaba fondo carmesí
$hero_marker = '// ================================================================' . "\n// FRONTPAGE HERO — CRIMSON PROFESSIONAL BACKGROUND";
$pos2 = strpos($scss, $hero_marker);
if ($pos2 !== false) $scss = rtrim(substr($scss, 0, $pos2));

set_config('scss', $scss . "\n" . $css, 'theme_moove');

// Footer: limpiar todo lo tau anterior
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$needles = ['tau_hero_extras','injectEscudo','addTauElements','tau_init','.tau-rings-wrap','.tau-lema-block'];
$changed = true;
while ($changed) {
    $changed = false;
    foreach ($needles as $needle) {
        if (strpos($footer, $needle) === false) continue;
        foreach (['<script>','<style>'] as $open) {
            $close = str_replace('<','</',$open);
            $s = strpos($footer, $open);
            if ($s === false) continue;
            $e = strpos($footer, $close, $s);
            if ($e === false) continue;
            $footer = substr($footer, 0, $s) . substr($footer, $e + strlen($close));
            $changed = true;
            break;
        }
        break;
    }
}
set_config('additionalhtmlfooter', trim($footer) . "\n" . $js, 'theme_moove');

theme_reset_all_caches();
echo "OK v7 — hero azul institucional, escudo estatico, aros giratorios\n";
echo "scss:   " . strlen(get_config('theme_moove','scss'))                 . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
