<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── 1. SCSS: quitar ::before del logo TAU (el texto derecho) ─────────────────
$scss = get_config('theme_moove', 'scss');
$marker = '/* TAU-FINAL';
$pos = strpos($scss, $marker);
if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));

$scss .= '

/* TAU-FINAL ════════════════════════════════════════════════ */
/* Logo TAU derecha — SIN etiqueta de texto */
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
/* Sin ::before = sin etiqueta de texto sobre el logo */
#mooveslideshow::before { display: none !important; content: none !important; }
@media(max-width:900px){ #mooveslideshow::after{ width:clamp(130px,18vw,170px)!important; right:2%!important; } }
@media(max-width:768px){ #mooveslideshow::after{ display:none !important; } }

/* LEMA */
.tau-lema-bloque {
    position: relative !important; overflow: hidden !important;
    background: linear-gradient(180deg, #1a0306 0%, #3d0b12 50%, #1a0306 100%) !important;
    border-top: 3px solid #c62b3a !important;
    border-bottom: 1px solid rgba(198,43,58,.20) !important;
    padding: 24px 1.5rem 20px !important;
    margin: 0 !important; display: block !important;
    visibility: visible !important; opacity: 1 !important;
}
.tau-lema-bloque::before {
    content: "" !important; position: absolute !important; inset: 0 !important;
    pointer-events: none !important;
    background-image: radial-gradient(circle, rgba(198,43,58,.07) 1px, transparent 1px) !important;
    background-size: 26px 26px !important;
}
.tau-lema-inner {
    position: relative !important; z-index: 1 !important;
    max-width: 760px !important; margin: 0 auto !important;
    display: flex !important; align-items: center !important;
    gap: 22px !important; justify-content: center !important; flex-wrap: wrap !important;
}
.tau-fray-icon {
    width: 76px !important; height: 76px !important; border-radius: 50% !important;
    background: rgba(198,43,58,.18) !important;
    border: 2px solid rgba(198,43,58,.50) !important;
    display: flex !important; align-items: center !important; justify-content: center !important;
    font-size: 1.8rem !important; flex-shrink: 0 !important;
    color: rgba(255,255,255,.55) !important; font-family: Georgia, serif !important;
}
.tau-fray-img {
    width: 76px !important; height: 76px !important; border-radius: 50% !important;
    object-fit: cover !important; object-position: top !important;
    border: 2px solid rgba(198,43,58,.60) !important;
    box-shadow: 0 4px 18px rgba(0,0,0,.50) !important; flex-shrink: 0 !important;
}
.tau-lema-txt { text-align: center !important; }
.tau-lema-linea {
    display: inline-flex !important; align-items: center !important;
    gap: 12px !important; margin-bottom: 5px !important;
}
.tau-lema-linea::before {
    content: "" !important; display: block !important; width: 38px !important; height: 1px !important;
    background: linear-gradient(90deg, transparent, rgba(198,43,58,.80)) !important;
}
.tau-lema-linea::after {
    content: "" !important; display: block !important; width: 38px !important; height: 1px !important;
    background: linear-gradient(90deg, rgba(198,43,58,.80), transparent) !important;
}
.tau-lema-frase {
    font-family: Georgia, "Times New Roman", serif !important;
    font-size: clamp(.88rem,1.2vw,1.04rem) !important; font-style: italic !important;
    color: rgba(255,255,255,.93) !important; letter-spacing: .03em !important;
    margin: 0 !important; text-shadow: 0 1px 8px rgba(0,0,0,.5) !important;
}
.tau-lema-frase strong {
    color: #fff !important; font-style: normal !important;
    font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: .08em !important;
}
.tau-lema-autor {
    font-size: clamp(.63rem,.86vw,.73rem) !important;
    color: rgba(255,255,255,.42) !important; letter-spacing: .10em !important;
    text-transform: uppercase !important; margin: 5px 0 0 !important;
    font-family: system-ui, sans-serif !important;
}
/* TAU-FINAL-FIN ════════════════════════════════════════════ */
';

set_config('scss', $scss, 'theme_moove');

// ── 2. additionalhtmltopofbody: HTML estático del lema (oculto al inicio) ─────
$lema_html = '<div id="tau-lema-src" class="tau-lema-bloque" style="display:none!important">
  <div class="tau-lema-inner">
    <div class="tau-fray-icon">&#10013;</div>
    <div class="tau-lema-txt">
      <div class="tau-lema-linea">
        <p class="tau-lema-frase">&ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;</p>
      </div>
      <p class="tau-lema-autor">&mdash;&nbsp;Fray Guillermo de Castellana, OFMCap.</p>
    </div>
  </div>
</div>';

set_config('additionalhtmltopofbody', $lema_html, 'theme_moove');

// ── 3. Footer: JS que solo MUEVE el div ya existente ─────────────────────────
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12','tau-v13','tau-v14','tau-v15','tau-v16'];
foreach (['<style','<script'] as $tag) {
    $close = ($tag==='<style') ? '</style>' : '</script>';
    $changed = true;
    while ($changed) {
        $changed = false; $pos = 0;
        while (($s = strpos($footer,$tag,$pos)) !== false) {
            $e = strpos($footer,$close,$s);
            if ($e===false) break;
            $block = substr($footer,$s,$e-$s+strlen($close));
            $hit = false;
            foreach ($kw as $k){if(stripos($block,$k)!==false){$hit=true;break;}}
            if ($hit){$footer=substr($footer,0,$s).substr($footer,$e+strlen($close));$changed=true;break;}
            $pos=$e+strlen($close);
        }
    }
}

$footer .= '
<script id="tau-v16-script">
(function(){
    function go(){
        var lema = document.getElementById("tau-lema-src");
        if (!lema) return;
        // Ya fue movido
        if (lema.getAttribute("data-placed")) return;
        lema.setAttribute("data-placed","1");

        // Foto si existe
        var img = new Image();
        img.onload = function(){
            var icon = lema.querySelector(".tau-fray-icon");
            if (!icon) return;
            var pic = document.createElement("img");
            pic.src = "/theme/tau_branding/assets/official/fray-guillermo.jpg";
            pic.className = "tau-fray-img";
            pic.alt = "Fray Guillermo de Castellana, OFMCap.";
            icon.parentNode.replaceChild(pic, icon);
        };
        img.src = "/theme/tau_branding/assets/official/fray-guillermo.jpg";

        // Mostrar
        lema.style.cssText = "";

        // Mover: después del slideshow o del page-header
        var ss = document.getElementById("mooveslideshow");
        if (ss) {
            var parent = ss.parentNode;
            // subir hasta encontrar hermano siguiente
            var node = ss;
            for (var i = 0; i < 8; i++) {
                var next = node.nextElementSibling;
                if (next && !next.classList.contains("tau-lema-bloque")) {
                    node.parentNode.insertBefore(lema, next);
                    return;
                }
                if (!node.parentNode || node.parentNode === document.body) break;
                node = node.parentNode;
            }
        }
        // Fallback: antes de #page-content
        var pc = document.querySelector("#page-content, #region-main-box, #region-main");
        if (pc) { pc.parentNode.insertBefore(lema, pc); return; }
        // Último fallback: segundo hijo del body
        var b = document.body;
        b.insertBefore(lema, b.children[1] || b.firstChild);
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", go);
    else go();
    setTimeout(go, 600);
})();
</script>';

set_config('additionalhtmlfooter', trim($footer), 'theme_moove');

theme_reset_all_caches();
echo "Lema definitivo aplicado\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "topofbody: " . strlen(get_config('theme_moove','additionalhtmltopofbody')) . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
