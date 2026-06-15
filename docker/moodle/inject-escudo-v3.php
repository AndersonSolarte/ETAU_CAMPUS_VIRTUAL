<?php
/**
 * Escudo CESMAG v3 — CSS puro (::before + ::after) + lema como sibling.
 * Confiable: no depende de JS para el escudo ni de overflow del carrusel.
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── 1. CSS ────────────────────────────────────────────────────────────────────
$escudo_css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG v3 — aros CSS puro
   ═══════════════════════════════════════════════════ */

#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
    border: none !important;
}

/* Aro exterior antihorario */
#mooveslideshow::before {
    content: "";
    position: absolute;
    right: calc(7% - 28px);
    top: 50%;
    width: clamp(248px, 34%, 368px);
    aspect-ratio: 1 / 1;
    border-radius: 50%;
    border: 1px dashed rgba(255,255,255,.20);
    transform: translateY(-50%) rotate(0deg);
    animation: tau-ccw 28s linear infinite, tau-escudo-in .9s ease both;
    z-index: 3;
    pointer-events: none;
    box-shadow: 0 0 0 1px rgba(198,43,58,.12);
}

/* Escudo + aro interior horario */
#mooveslideshow::after {
    content: "";
    position: absolute;
    right: 7%;
    top: 50%;
    width: clamp(200px, 28%, 308px);
    aspect-ratio: 1 / 1;
    background:
        url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png")
        center / 80% auto no-repeat;
    border-radius: 50%;
    border: 1.5px dashed rgba(255,255,255,.30);
    transform: translateY(-50%) rotate(0deg);
    animation: tau-cw 20s linear infinite, tau-escudo-in .8s ease both;
    z-index: 4;
    pointer-events: none;
    /* Resplandor carmesí + nodos via box-shadow */
    box-shadow:
        0 0 0 3px rgba(198,43,58,.18),
        0 0 48px 8px rgba(198,43,58,.22),
        0 0 0 12px rgba(255,255,255,.03);
}

@keyframes tau-cw  {
    from { transform: translateY(-50%) rotate(0deg); }
    to   { transform: translateY(-50%) rotate(360deg); }
}
@keyframes tau-ccw {
    from { transform: translateY(-50%) rotate(0deg); }
    to   { transform: translateY(-50%) rotate(-360deg); }
}
@keyframes tau-escudo-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* Nodos carmesí: 4 puntos en el aro interior via radial-gradient capas */
/* Se logra con un pseudo en el aro ::after pero no podemos anidar pseudos;
   los nodos se hacen via el JS del lema que también agrega el SVG de nodos */

/* Lema institucional — se inyecta como sibling via JS */
.tau-lema-block {
    position: relative;
    background: linear-gradient(90deg,
        rgba(198,43,58,.06) 0%,
        rgba(198,43,58,.12) 40%,
        rgba(198,43,58,.06) 100%);
    border-top: 1px solid rgba(198,43,58,.25);
    border-bottom: 1px solid rgba(198,43,58,.12);
    padding: 14px 0;
    text-align: center;
    overflow: hidden;
}
.tau-lema-block::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
}
.tau-lema-block__line {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    position: relative;
}
.tau-lema-block__line::before,
.tau-lema-block__line::after {
    content: "";
    display: block;
    width: 48px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(198,43,58,.7));
}
.tau-lema-block__line::after {
    background: linear-gradient(90deg, rgba(198,43,58,.7), transparent);
}
.tau-lema-block__quote {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(.75rem, 1.1vw, .9rem);
    font-style: italic;
    color: rgba(255,255,255,.82);
    letter-spacing: .06em;
    margin: 0;
    text-shadow: 0 1px 6px rgba(0,0,0,.4);
}
.tau-lema-block__quote strong {
    color: #fff;
    font-style: normal;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
}
.tau-lema-block__author {
    font-family: system-ui, Arial, sans-serif;
    font-size: clamp(.65rem, .9vw, .75rem);
    color: rgba(255,255,255,.45);
    letter-spacing: .09em;
    margin: 4px 0 0;
}

/* Aros SVG superpuestos (inyectados via JS como elemento extra sobre el slideshow) */
.tau-ring-overlay {
    position: absolute;
    right: 7%;
    top: 50%;
    transform: translateY(-50%);
    width: clamp(200px, 28%, 308px);
    aspect-ratio: 1 / 1;
    pointer-events: none;
    z-index: 5;
}
.tau-ring-overlay svg {
    width: 100%;
    height: 100%;
    overflow: visible;
}
.tau-node-r { animation: tau-node-pulse 2.8s ease-in-out infinite alternate; }
.tau-node-r:nth-child(2) { animation-delay: -.7s; }
.tau-node-r:nth-child(3) { animation-delay: -1.4s; }
.tau-node-r:nth-child(4) { animation-delay: -2.1s; }
.tau-node-w { animation: tau-node-pulse 3.4s ease-in-out infinite alternate; }
.tau-node-w:nth-child(2) { animation-delay: -.85s; }
.tau-node-w:nth-child(3) { animation-delay: -1.7s; }
.tau-node-w:nth-child(4) { animation-delay: -2.55s; }
@keyframes tau-node-pulse {
    from { opacity: .45; r: 4; }
    to   { opacity: 1;   r: 7; }
}

@media (max-width: 900px) {
    #mooveslideshow::after  { width: clamp(160px, 24%, 220px); right: 3%; }
    #mooveslideshow::before { width: clamp(196px, 30%, 268px); right: calc(3% - 20px); }
    .tau-ring-overlay       { width: clamp(160px, 24%, 220px); right: 3%; }
}
@media (max-width: 768px) {
    #mooveslideshow::after,
    #mooveslideshow::before,
    .tau-ring-overlay { display: none; }
}
';

// ── 2. JS: nodos SVG + lema (sólo elementos adicionales, no el escudo) ────────
$escudo_js = '
<script>
(function(){
    function addTauElements() {
        var ss = document.getElementById("mooveslideshow");
        if (!ss) return;

        /* Nodos giratorios — SVG overlay encima del ::after */
        if (!document.querySelector(".tau-ring-overlay")) {
            ss.style.position = "relative";
            var ov = document.createElement("div");
            ov.className = "tau-ring-overlay";
            ov.innerHTML = `<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
  <g style="animation:tau-cw 20s linear infinite;transform-origin:100px 100px">
    <circle class="tau-node-r" cx="100" cy="5"   r="5" fill="#c62b3a"/>
    <circle class="tau-node-r" cx="195" cy="100" r="5" fill="#c62b3a"/>
    <circle class="tau-node-r" cx="100" cy="195" r="5" fill="#c62b3a"/>
    <circle class="tau-node-r" cx="5"   cy="100" r="5" fill="#c62b3a"/>
  </g>
  <g style="animation:tau-ccw 28s linear infinite;transform-origin:100px 100px">
    <circle class="tau-node-w" cx="100" cy="-18" r="3.5" fill="rgba(255,255,255,.70)"/>
    <circle class="tau-node-w" cx="218" cy="100" r="3.5" fill="rgba(255,255,255,.70)"/>
    <circle class="tau-node-w" cx="100" cy="218" r="3.5" fill="rgba(255,255,255,.70)"/>
    <circle class="tau-node-w" cx="-18" cy="100" r="3.5" fill="rgba(255,255,255,.70)"/>
  </g>
</svg>`;
            ss.appendChild(ov);
        }

        /* Lema institucional como sibling después del slideshow */
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
        document.addEventListener("DOMContentLoaded", addTauElements);
    } else {
        addTauElements();
    }
})();
</script>
';

// ── 3. Actualizar BD ──────────────────────────────────────────────────────────

// scss: quitar todo bloque anterior de escudo
$scss = get_config('theme_moove', 'scss');
$marker = '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG";
$pos = strpos($scss, $marker);
if ($pos !== false) {
    $scss = rtrim(substr($scss, 0, $pos));
}
set_config('scss', $scss . "\n" . $escudo_css, 'theme_moove');

// footer: quitar sólo nuestros scripts anteriores
$footer = get_config('theme_moove', 'additionalhtmlfooter');
// Quitar cualquier script con injectEscudo o addTauElements
while (strpos($footer, 'injectEscudo') !== false || strpos($footer, 'addTauElements') !== false) {
    $s = strpos($footer, '<script>');
    if ($s === false) break;
    $e = strpos($footer, '</script>', $s);
    if ($e === false) break;
    $footer = substr($footer, 0, $s) . substr($footer, $e + 9);
}
set_config('additionalhtmlfooter', trim($footer) . "\n" . $escudo_js, 'theme_moove');

theme_reset_all_caches();
echo "OK v3 — CSS puro + JS ligero inyectados.\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
