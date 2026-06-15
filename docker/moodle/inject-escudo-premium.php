<?php
/**
 * Inyecta el escudo CESMAG premium con aros animados y lema institucional.
 * Reemplaza la versión simple anterior.
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── 1. CSS: estilos del escudo premium ──────────────────────────────────────
$escudo_css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG PREMIUM — Hero institucional v2
   ═══════════════════════════════════════════════════ */

#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
    border: none !important;
    outline: none !important;
}

/* Quitar ::after simple anterior */
#mooveslideshow::after { display: none !important; }

/* Contenedor del escudo premium */
.tau-escudo-stage {
    position: absolute;
    right: 6%;
    top: 50%;
    transform: translateY(-50%);
    width: clamp(220px, 28vw, 340px);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    z-index: 4;
    pointer-events: none;
    animation: tau-stage-in .9s cubic-bezier(.22,1,.36,1) both;
}

@keyframes tau-stage-in {
    from { opacity: 0; transform: translateY(calc(-50% + 24px)); }
    to   { opacity: 1; transform: translateY(-50%); }
}

/* SVG wrapper que contiene aros + imagen */
.tau-escudo-ring-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
}

/* El SVG de aros ocupa todo el wrapper */
.tau-escudo-ring-wrap svg.tau-rings {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    overflow: visible;
}

/* La imagen del escudo centrada dentro */
.tau-escudo-ring-wrap img.tau-seal {
    position: absolute;
    inset: 14%;
    width: 72%;
    height: 72%;
    object-fit: contain;
    border-radius: 50%;
    filter: drop-shadow(0 4px 24px rgba(0,0,0,.55));
    transition: transform .5s ease, filter .5s ease;
}
.tau-escudo-ring-wrap:hover img.tau-seal {
    transform: scale(1.04);
    filter: drop-shadow(0 8px 36px rgba(0,0,0,.7)) brightness(1.06);
}

/* Aro 1 — rotación lenta horaria */
.tau-ring-1 {
    animation: tau-spin-cw 18s linear infinite;
    transform-origin: 50% 50%;
    transform-box: fill-box;
}
/* Aro 2 — rotación lenta antihoraria */
.tau-ring-2 {
    animation: tau-spin-ccw 26s linear infinite;
    transform-origin: 50% 50%;
    transform-box: fill-box;
}
/* Aro 3 — pulso suave */
.tau-ring-3 {
    animation: tau-pulse-ring 4s ease-in-out infinite;
    transform-origin: 50% 50%;
    transform-box: fill-box;
}
/* Nodos brillantes en aro 1 */
.tau-node {
    animation: tau-node-glow 3s ease-in-out infinite alternate;
}
.tau-node:nth-child(2) { animation-delay: -1s; }
.tau-node:nth-child(3) { animation-delay: -2s; }
.tau-node:nth-child(4) { animation-delay: -.5s; }

@keyframes tau-spin-cw  { to { transform: rotate(360deg); } }
@keyframes tau-spin-ccw { to { transform: rotate(-360deg); } }
@keyframes tau-pulse-ring {
    0%,100% { opacity: .35; r: 48%; }
    50%      { opacity: .65; }
}
@keyframes tau-node-glow {
    from { opacity: .55; r: 3; }
    to   { opacity: 1;   r: 5; }
}

/* Resplandor central detrás del escudo */
.tau-escudo-glow {
    position: absolute;
    inset: 18%;
    width: 64%;
    height: 64%;
    background: radial-gradient(circle, rgba(198,43,58,.28) 0%, transparent 70%);
    border-radius: 50%;
    animation: tau-glow-pulse 4s ease-in-out infinite;
    pointer-events: none;
}
@keyframes tau-glow-pulse {
    0%,100% { opacity: .6; transform: scale(1); }
    50%      { opacity: 1;  transform: scale(1.08); }
}

/* Lema institucional */
.tau-lema {
    text-align: center;
    animation: tau-stage-in 1.2s cubic-bezier(.22,1,.36,1) .2s both;
}
.tau-lema__quote {
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(.72rem, 1.1vw, .88rem);
    font-weight: 400;
    font-style: italic;
    color: rgba(255,255,255,.90);
    letter-spacing: .06em;
    line-height: 1.45;
    text-shadow: 0 1px 8px rgba(0,0,0,.55);
    margin: 0 0 4px;
}
.tau-lema__quote strong {
    color: #fff;
    font-weight: 700;
    font-style: normal;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-size: 1.05em;
}
.tau-lema__author {
    font-family: var(--tau-font, "Segoe UI", Arial, sans-serif);
    font-size: clamp(.62rem, .9vw, .75rem);
    color: rgba(255,255,255,.52);
    letter-spacing: .08em;
    margin: 0;
}
.tau-lema__divider {
    width: 48px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #c62b3a, transparent);
    border: none;
    margin: 6px auto 8px;
}

@media (max-width: 900px) {
    .tau-escudo-stage { right: 3%; width: clamp(160px, 24vw, 240px); gap: 10px; }
}
@media (max-width: 768px) {
    .tau-escudo-stage { display: none; }
}
';

// ── 2. HTML+JS: inyectar el escudo en el DOM ────────────────────────────────
$escudo_html = '
<script>
(function() {
    function injectEscudo() {
        var ss = document.getElementById("mooveslideshow");
        if (!ss || document.querySelector(".tau-escudo-stage")) return;

        var stage = document.createElement("div");
        stage.className = "tau-escudo-stage";
        stage.innerHTML = `
<div class="tau-escudo-ring-wrap">
  <!-- Resplandor de fondo -->
  <div class="tau-escudo-glow"></div>

  <!-- SVG con aros decorativos -->
  <svg class="tau-rings" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <filter id="tau-blur-sm">
        <feGaussianBlur stdDeviation="1.2"/>
      </filter>
    </defs>

    <!-- Aro 3: pulso exterior suave -->
    <circle class="tau-ring-3" cx="100" cy="100" r="96"
      fill="none" stroke="rgba(198,43,58,0.18)" stroke-width="1.2"
      stroke-dasharray="4 8"/>

    <!-- Aro 1: rota horario, guiones institucionales -->
    <g class="tau-ring-1">
      <circle cx="100" cy="100" r="88"
        fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="1"
        stroke-dasharray="12 6"/>
      <!-- Nodos en aro 1 -->
      <circle class="tau-node" cx="100" cy="12" r="3.5" fill="#c62b3a"/>
      <circle class="tau-node" cx="188" cy="100" r="3.5" fill="#c62b3a"/>
      <circle class="tau-node" cx="100" cy="188" r="3.5" fill="#c62b3a"/>
      <circle class="tau-node" cx="12"  cy="100" r="3.5" fill="#c62b3a"/>
    </g>

    <!-- Aro 2: rota antihorario, más fino -->
    <g class="tau-ring-2">
      <circle cx="100" cy="100" r="78"
        fill="none" stroke="rgba(255,255,255,0.12)" stroke-width=".7"
        stroke-dasharray="3 14"/>
      <!-- Nodos diagonales -->
      <circle class="tau-node" cx="155" cy="45" r="2.5" fill="rgba(255,255,255,0.70)"/>
      <circle class="tau-node" cx="155" cy="155" r="2.5" fill="rgba(255,255,255,0.70)"/>
      <circle class="tau-node" cx="45"  cy="155" r="2.5" fill="rgba(255,255,255,0.70)"/>
      <circle class="tau-node" cx="45"  cy="45"  r="2.5" fill="rgba(255,255,255,0.70)"/>
    </g>

    <!-- Barra de acento izquierda (reflejo del navbar) -->
    <line x1="2" y1="30" x2="2" y2="170"
      stroke="#c62b3a" stroke-width="3" stroke-linecap="round"/>
  </svg>

  <!-- Escudo oficial CESMAG -->
  <img class="tau-seal"
       src="/theme/tau_branding/assets/official/cesmag-escudo-hero.png"
       alt="Escudo Universidad CESMAG"/>
</div>

<!-- Lema institucional -->
<div class="tau-lema">
  <hr class="tau-lema__divider"/>
  <p class="tau-lema__quote">
    &ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;
  </p>
  <p class="tau-lema__author">Fray Guillermo de Castellana, OFMCap.</p>
</div>
`;

        ss.style.position = "relative";
        ss.appendChild(stage);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", injectEscudo);
    } else {
        injectEscudo();
    }
})();
</script>
';

// ── 3. Actualizar BD ─────────────────────────────────────────────────────────

// — scss: quitar versión anterior e insertar nueva
$current_scss = get_config('theme_moove', 'scss');
$marker = '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG";
$pos = strpos($current_scss, $marker);
if ($pos !== false) {
    $current_scss = rtrim(substr($current_scss, 0, $pos));
}
set_config('scss', $current_scss . "\n" . $escudo_css, 'theme_moove');

// — additionalhtml_footer: quitar inyección anterior e insertar nueva
$current_footer = get_config('theme_moove', 'additionalhtmlfooter');
$js_marker = '<script>' . "\n(function() {" . "\n    function injectEscudo()";
$pos2 = strpos($current_footer, '<script>');
// Buscar específicamente nuestro script
if ($pos2 !== false && strpos($current_footer, 'injectEscudo') !== false) {
    $start = strpos($current_footer, '<script>');
    $end   = strpos($current_footer, '</script>', $start);
    if ($end !== false) {
        $current_footer = substr($current_footer, 0, $start) . substr($current_footer, $end + 9);
    }
}
set_config('additionalhtmlfooter', trim($current_footer) . "\n" . $escudo_html, 'theme_moove');

// — Purgar caché
theme_reset_all_caches();

echo "OK: Escudo premium inyectado correctamente.\n";
echo "scss bytes: "   . strlen(get_config('theme_moove', 'scss'))                . "\n";
echo "footer bytes: " . strlen(get_config('theme_moove', 'additionalhtmlfooter')) . "\n";
