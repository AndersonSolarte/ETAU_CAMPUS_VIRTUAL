<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// CSS minimalista — idéntico al que funcionó la primera vez, sin rotación del escudo
$css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG v5
   ═══════════════════════════════════════════════════ */

#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
    border: none !important;
}

/* Escudo estático — exactamente como funcionó la primera vez */
#mooveslideshow::after {
    content: "" !important;
    position: absolute !important;
    right: 6% !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: clamp(200px, 30%, 320px) !important;
    aspect-ratio: 1 / 1 !important;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    z-index: 9 !important;
    pointer-events: none !important;
    opacity: .92 !important;
    filter: drop-shadow(0 6px 32px rgba(0,0,0,.55)) !important;
}

/* Aros decorativos via JS — ver footer */
.tau-rings-wrap {
    position: absolute;
    right: 6%;
    top: 50%;
    transform: translateY(-50%);
    width: clamp(200px, 30%, 320px);
    aspect-ratio: 1 / 1;
    pointer-events: none;
    z-index: 8;
}
.tau-rings-wrap svg {
    width: 100%;
    height: 100%;
    overflow: visible;
}

/* Lema */
.tau-lema-block {
    position: relative;
    background: linear-gradient(90deg, rgba(198,43,58,.05), rgba(198,43,58,.13), rgba(198,43,58,.05));
    border-top: 1px solid rgba(198,43,58,.30);
    border-bottom: 1px solid rgba(198,43,58,.12);
    padding: 13px 0;
    text-align: center;
}
.tau-lema-block__line {
    display: inline-flex;
    align-items: center;
    gap: 16px;
}
.tau-lema-block__line::before {
    content: "";
    display: block;
    width: 52px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(198,43,58,.8));
}
.tau-lema-block__line::after {
    content: "";
    display: block;
    width: 52px;
    height: 1px;
    background: linear-gradient(90deg, rgba(198,43,58,.8), transparent);
}
.tau-lema-block__quote {
    font-family: Georgia, serif;
    font-size: clamp(.76rem, 1.1vw, .9rem);
    font-style: italic;
    color: rgba(255,255,255,.86);
    letter-spacing: .05em;
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
    font-size: clamp(.64rem, .9vw, .75rem);
    color: rgba(255,255,255,.44);
    letter-spacing: .09em;
    margin: 4px 0 0;
}

@media (max-width: 768px) {
    #mooveslideshow::after { display: none !important; }
    .tau-rings-wrap { display: none; }
}
';

// JS: aros SVG giratiorios + lema. El escudo ya está en CSS ::after (estático)
$js = '
<script>
(function(){
  function tau_init(){
    var ss = document.getElementById("mooveslideshow");
    if (!ss) return;

    /* Aros giratorios alrededor del escudo (::after estático) */
    if (!ss.querySelector(".tau-rings-wrap")) {
      var wrap = document.createElement("div");
      wrap.className = "tau-rings-wrap";
      wrap.innerHTML = `<svg viewBox="-20 -20 240 240" xmlns="http://www.w3.org/2000/svg">
  <style>
    .r1{animation:tcw  20s linear infinite;transform-origin:100px 100px}
    .r2{animation:tccw 28s linear infinite;transform-origin:100px 100px}
    .r3{animation:tpulse 4s ease-in-out infinite}
    .nd{animation:tnd 2.8s ease-in-out infinite alternate}
    .nd:nth-child(2){animation-delay:-.7s}
    .nd:nth-child(3){animation-delay:-1.4s}
    .nd:nth-child(4){animation-delay:-2.1s}
    .nw{animation:tnw 3.4s ease-in-out infinite alternate}
    .nw:nth-child(2){animation-delay:-.85s}
    .nw:nth-child(3){animation-delay:-1.7s}
    .nw:nth-child(4){animation-delay:-2.55s}
    @keyframes tcw  {to{transform:rotate(360deg)}}
    @keyframes tccw {to{transform:rotate(-360deg)}}
    @keyframes tpulse{0%,100%{opacity:.28}50%{opacity:.60}}
    @keyframes tnd  {from{r:4;opacity:.5}to{r:7;opacity:1}}
    @keyframes tnw  {from{r:3;opacity:.4}to{r:5.5;opacity:.85}}
  </style>

  <!-- Aro pulso exterior (estático, solo parpadea) -->
  <circle class="r3" cx="100" cy="100" r="108"
    fill="none" stroke="rgba(198,43,58,.18)" stroke-width="1.2"
    stroke-dasharray="6 10"/>

  <!-- Aro exterior antihorario con nodos blancos -->
  <g class="r2">
    <circle cx="100" cy="100" r="96"
      fill="none" stroke="rgba(255,255,255,.18)" stroke-width="1"
      stroke-dasharray="3 16"/>
    <circle class="nw" cx="100" cy="4"   r="3.5" fill="rgba(255,255,255,.75)"/>
    <circle class="nw" cx="196" cy="100" r="3.5" fill="rgba(255,255,255,.75)"/>
    <circle class="nw" cx="100" cy="196" r="3.5" fill="rgba(255,255,255,.75)"/>
    <circle class="nw" cx="4"   cy="100" r="3.5" fill="rgba(255,255,255,.75)"/>
  </g>

  <!-- Aro interior horario con nodos carmesí -->
  <g class="r1">
    <circle cx="100" cy="100" r="112"
      fill="none" stroke="rgba(255,255,255,.25)" stroke-width="1.5"
      stroke-dasharray="14 7"/>
    <circle class="nd" cx="100" cy="-12" r="5" fill="#c62b3a"/>
    <circle class="nd" cx="212" cy="100" r="5" fill="#c62b3a"/>
    <circle class="nd" cx="100" cy="212" r="5" fill="#c62b3a"/>
    <circle class="nd" cx="-12" cy="100" r="5" fill="#c62b3a"/>
  </g>

  <!-- Resplandor central -->
  <circle cx="100" cy="100" r="86"
    fill="none"
    stroke="rgba(198,43,58,.10)"
    stroke-width="20"/>
</svg>`;
      ss.style.position = "relative";
      ss.appendChild(wrap);
    }

    /* Lema debajo del slideshow */
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

// Actualizar BD
$scss = get_config('theme_moove', 'scss');
$marker = '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG";
$pos = strpos($scss, $marker);
if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));
set_config('scss', $scss . "\n" . $css, 'theme_moove');

$footer = get_config('theme_moove', 'additionalhtmlfooter');
// Quitar scripts tau anteriores
foreach (['tau_hero_extras','injectEscudo','addTauElements','tau_init'] as $fn) {
    while (strpos($footer, $fn) !== false) {
        $s = strpos($footer, '<script>');
        if ($s === false) break;
        $e = strpos($footer, '</script>', $s);
        if ($e === false) break;
        $footer = substr($footer, 0, $s) . substr($footer, $e + 9);
    }
}
set_config('additionalhtmlfooter', trim($footer) . "\n" . $js, 'theme_moove');

theme_reset_all_caches();
echo "OK v5 — escudo estatico, aros giratorios separados\n";
