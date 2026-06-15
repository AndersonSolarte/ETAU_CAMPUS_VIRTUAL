<?php
/**
 * Escudo v4 — z-index corregido. El PNG se descarga pero el carousel lo tapaba.
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$escudo_css = '

/* ═══════════════════════════════════════════════════
   ESCUDO CESMAG v4 — z-index fix
   ═══════════════════════════════════════════════════ */

#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important;
    pointer-events: none !important;
    position: absolute !important;
    inset: 0 !important;
    border: none !important;
}

/* El slideshow debe ser el contexto de apilamiento raíz */
#mooveslideshow {
    position: relative !important;
}

/* El carousel interno NO debe crear stacking context que tape el escudo */
#mooveslideshow .carousel,
#mooveslideshow .carousel-inner,
#mooveslideshow .carousel-item {
    position: relative !important;
    z-index: 1 !important;
}

/* Aro exterior antihorario — z-index muy alto */
#mooveslideshow::before {
    content: "" !important;
    position: absolute !important;
    right: calc(7% - 30px) !important;
    top: 50% !important;
    width: clamp(256px, 34vw, 370px) !important;
    aspect-ratio: 1 / 1 !important;
    border-radius: 50% !important;
    border: 1px dashed rgba(255,255,255,.22) !important;
    transform: translateY(-50%) !important;
    animation: tau-ccw 28s linear infinite, tau-fade-in .9s ease both !important;
    z-index: 9998 !important;
    pointer-events: none !important;
    box-shadow: 0 0 0 1px rgba(198,43,58,.10) !important;
}

/* Escudo + aro interior horario */
#mooveslideshow::after {
    content: "" !important;
    position: absolute !important;
    right: 7% !important;
    top: 50% !important;
    width: clamp(208px, 28vw, 316px) !important;
    aspect-ratio: 1 / 1 !important;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size: 78% auto !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    border-radius: 50% !important;
    border: 1.5px dashed rgba(255,255,255,.30) !important;
    transform: translateY(-50%) !important;
    animation: tau-cw 20s linear infinite, tau-fade-in .8s ease both !important;
    z-index: 9999 !important;
    pointer-events: none !important;
    box-shadow:
        0 0 0 4px rgba(198,43,58,.15),
        0 0 60px 10px rgba(198,43,58,.20) !important;
}

@keyframes tau-cw   { to { transform: translateY(-50%) rotate(360deg); } }
@keyframes tau-ccw  { to { transform: translateY(-50%) rotate(-360deg); } }
@keyframes tau-fade-in { from { opacity: 0; } to { opacity: 1; } }

@media (max-width: 900px) {
    #mooveslideshow::after  { width: clamp(160px,22vw,210px); right: 3%; }
    #mooveslideshow::before { width: clamp(195px,28vw,260px); right: calc(3% - 22px); }
}
@media (max-width: 768px) {
    #mooveslideshow::after,
    #mooveslideshow::before { display: none !important; }
}
';

// JS: nodos SVG + lema (no afecta visibilidad del escudo)
$escudo_js = '
<script>
(function(){
    function tau_hero_extras() {
        var ss = document.getElementById("mooveslideshow");
        if (!ss) return;

        /* Nodos giratorios SVG — posicionados igual que ::after */
        if (!document.querySelector(".tau-nodes-svg")) {
            var ov = document.createElement("div");
            ov.className = "tau-nodes-svg";
            ov.style.cssText = [
                "position:absolute",
                "right:7%",
                "top:50%",
                "transform:translateY(-50%)",
                "width:clamp(208px,28vw,316px)",
                "aspect-ratio:1/1",
                "pointer-events:none",
                "z-index:10000"
            ].join(";");
            ov.innerHTML = `<svg viewBox="0 0 200 200" style="width:100%;height:100%;overflow:visible">
  <g style="animation:tau-cw 20s linear infinite;transform-origin:100px 100px">
    <circle cx="100" cy="5"   r="5.5" fill="#c62b3a"
      style="animation:tau-nodo 2.8s ease-in-out infinite alternate"/>
    <circle cx="195" cy="100" r="5.5" fill="#c62b3a"
      style="animation:tau-nodo 2.8s ease-in-out infinite alternate;animation-delay:-.7s"/>
    <circle cx="100" cy="195" r="5.5" fill="#c62b3a"
      style="animation:tau-nodo 2.8s ease-in-out infinite alternate;animation-delay:-1.4s"/>
    <circle cx="5"   cy="100" r="5.5" fill="#c62b3a"
      style="animation:tau-nodo 2.8s ease-in-out infinite alternate;animation-delay:-2.1s"/>
  </g>
  <g style="animation:tau-ccw 28s linear infinite;transform-origin:100px 100px;transform:scale(1.26)">
    <circle cx="100" cy="5"   r="3.5" fill="rgba(255,255,255,.72)"
      style="animation:tau-nodo 3.4s ease-in-out infinite alternate"/>
    <circle cx="195" cy="100" r="3.5" fill="rgba(255,255,255,.72)"
      style="animation:tau-nodo 3.4s ease-in-out infinite alternate;animation-delay:-.85s"/>
    <circle cx="100" cy="195" r="3.5" fill="rgba(255,255,255,.72)"
      style="animation:tau-nodo 3.4s ease-in-out infinite alternate;animation-delay:-1.7s"/>
    <circle cx="5"   cy="100" r="3.5" fill="rgba(255,255,255,.72)"
      style="animation:tau-nodo 3.4s ease-in-out infinite alternate;animation-delay:-2.55s"/>
  </g>
</svg>`;
            ss.style.position = "relative";
            ss.appendChild(ov);
        }

        /* Lema debajo del slideshow */
        if (!document.querySelector(".tau-lema-block")) {
            var lema = document.createElement("div");
            lema.className = "tau-lema-block";
            lema.innerHTML = `
<div class="tau-lema-block__line">
    <p class="tau-lema-block__quote">
        &ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;
    </p>
</div>
<p class="tau-lema-block__author">Fray Guillermo de Castellana, OFMCap.</p>`;
            if (ss.nextSibling) {
                ss.parentNode.insertBefore(lema, ss.nextSibling);
            } else {
                ss.parentNode.appendChild(lema);
            }
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", tau_hero_extras);
    } else {
        tau_hero_extras();
    }
})();
</script>

<style>
@keyframes tau-cw  { to{transform:rotate(360deg)} }
@keyframes tau-ccw { to{transform:rotate(-360deg)} }
@keyframes tau-nodo { from{opacity:.45;r:4} to{opacity:1;r:7} }

.tau-lema-block {
    position:relative;
    background:linear-gradient(90deg,rgba(198,43,58,.05),rgba(198,43,58,.12),rgba(198,43,58,.05));
    border-top:1px solid rgba(198,43,58,.28);
    border-bottom:1px solid rgba(198,43,58,.12);
    padding:13px 0;
    text-align:center;
    overflow:hidden;
}
.tau-lema-block::before {
    content:"";position:absolute;inset:0;pointer-events:none;
    background-image:radial-gradient(circle,rgba(255,255,255,.03) 1px,transparent 1px);
    background-size:32px 32px;
}
.tau-lema-block__line {
    display:inline-flex;align-items:center;gap:16px;position:relative;
}
.tau-lema-block__line::before {
    content:"";display:block;width:52px;height:1px;
    background:linear-gradient(90deg,transparent,rgba(198,43,58,.8));
}
.tau-lema-block__line::after {
    content:"";display:block;width:52px;height:1px;
    background:linear-gradient(90deg,rgba(198,43,58,.8),transparent);
}
.tau-lema-block__quote {
    font-family:Georgia,"Times New Roman",serif;
    font-size:clamp(.76rem,1.1vw,.9rem);font-style:italic;
    color:rgba(255,255,255,.85);letter-spacing:.05em;margin:0;
    text-shadow:0 1px 6px rgba(0,0,0,.4);
}
.tau-lema-block__quote strong {
    color:#fff;font-style:normal;font-weight:700;
    letter-spacing:.1em;text-transform:uppercase;
}
.tau-lema-block__author {
    font-family:system-ui,Arial,sans-serif;
    font-size:clamp(.64rem,.88vw,.74rem);
    color:rgba(255,255,255,.44);letter-spacing:.09em;margin:4px 0 0;
}
</style>
';

// Actualizar BD
$scss = get_config('theme_moove', 'scss');
$marker = '/* ═══════════════════════════════════════════════════' . "\n   ESCUDO CESMAG";
$pos = strpos($scss, $marker);
if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));
set_config('scss', $scss . "\n" . $escudo_css, 'theme_moove');

$footer = get_config('theme_moove', 'additionalhtmlfooter');
while (strpos($footer, 'tau_hero_extras') !== false || strpos($footer, 'injectEscudo') !== false || strpos($footer, 'addTauElements') !== false) {
    $s = strpos($footer, '<script>');
    if ($s === false) break;
    $e = strpos($footer, '</script>', $s);
    if ($e === false) break;
    $footer = substr($footer, 0, $s) . substr($footer, $e + 9);
}
set_config('additionalhtmlfooter', trim($footer) . "\n" . $escudo_js, 'theme_moove');

theme_reset_all_caches();
echo "OK v4\n";
echo "scss: "   . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
