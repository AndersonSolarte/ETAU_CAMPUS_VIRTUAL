<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── 1. CSS en SCSS (garantizado que carga) ────────────────────────────────────
$scss = get_config('theme_moove', 'scss');

// Quitar bloque anterior TAU-LOGO-HERO
$marker = '/* TAU-LOGO-HERO';
$pos = strpos($scss, $marker);
if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));

// Quitar bloque TAU-FINAL si existe
$marker2 = '/* TAU-FINAL';
$pos2 = strpos($scss, $marker2);
if ($pos2 !== false) $scss = rtrim(substr($scss, 0, $pos2));

$hero_and_lema_css = '

/* TAU-FINAL ════════════════════════════════════════════════ */

/* Logo TAU a la derecha del hero */
#mooveslideshow { position: relative !important; overflow: visible !important; }
#mooveslideshow .carousel-inner { overflow: hidden !important; }
#mooveslideshow::after {
    content: "" !important;
    position: absolute !important;
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

/* Etiqueta "E-TAU Campus Virtual" encima del logo — CSS puro */
#mooveslideshow::before {
    content: "E-TAU Campus Virtual" !important;
    position: absolute !important;
    right: 6% !important;
    top: 18% !important;
    width: clamp(180px,25%,260px) !important;
    text-align: center !important;
    font-family: "Manrope", system-ui, sans-serif !important;
    font-size: clamp(.62rem,.80vw,.78rem) !important;
    font-weight: 800 !important;
    letter-spacing: .18em !important;
    text-transform: uppercase !important;
    color: rgba(255,255,255,.92) !important;
    background: rgba(198,43,58,.60) !important;
    border: 1px solid rgba(255,255,255,.22) !important;
    border-radius: 20px !important;
    padding: 4px 0 !important;
    z-index: 10 !important;
    pointer-events: none !important;
}

@media(max-width:900px){
    #mooveslideshow::after { width:clamp(130px,18vw,170px) !important; right:2% !important; }
    #mooveslideshow::before { right:2% !important; width:clamp(130px,18vw,170px) !important; }
}
@media(max-width:768px){
    #mooveslideshow::after, #mooveslideshow::before { display:none !important; }
}

/* LEMA — bloque estático debajo del hero */
.tau-lema-bloque {
    position: relative !important;
    overflow: hidden !important;
    background: linear-gradient(180deg, #1a0306 0%, #3d0b12 50%, #1a0306 100%) !important;
    border-top: 3px solid #c62b3a !important;
    border-bottom: 1px solid rgba(198,43,58,.20) !important;
    padding: 24px 1.5rem 20px !important;
    margin: 0 !important;
    display: block !important;
    text-align: center !important;
    order: -1 !important;
}
.tau-lema-bloque::before {
    content: "" !important;
    position: absolute !important; inset: 0 !important; pointer-events: none !important;
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
    box-shadow: 0 4px 18px rgba(0,0,0,.50) !important;
    flex-shrink: 0 !important;
}
.tau-lema-txt { text-align: center !important; }
.tau-lema-linea {
    display: inline-flex !important; align-items: center !important;
    gap: 12px !important; margin-bottom: 5px !important;
}
.tau-lema-linea::before {
    content: "" !important; display: block !important;
    width: 38px !important; height: 1px !important;
    background: linear-gradient(90deg, transparent, rgba(198,43,58,.80)) !important;
}
.tau-lema-linea::after {
    content: "" !important; display: block !important;
    width: 38px !important; height: 1px !important;
    background: linear-gradient(90deg, rgba(198,43,58,.80), transparent) !important;
}
.tau-lema-frase {
    font-family: Georgia, "Times New Roman", serif !important;
    font-size: clamp(.86rem,1.2vw,1.02rem) !important;
    font-style: italic !important;
    color: rgba(255,255,255,.93) !important;
    letter-spacing: .03em !important; margin: 0 !important;
    text-shadow: 0 1px 8px rgba(0,0,0,.5) !important;
}
.tau-lema-frase strong {
    color: #fff !important; font-style: normal !important;
    font-weight: 700 !important; letter-spacing: .08em !important;
    text-transform: uppercase !important;
}
.tau-lema-autor {
    font-size: clamp(.63rem,.86vw,.73rem) !important;
    color: rgba(255,255,255,.42) !important;
    letter-spacing: .10em !important; text-transform: uppercase !important;
    margin: 5px 0 0 !important; font-family: system-ui, sans-serif !important;
}
/* TAU-FINAL-FIN ════════════════════════════════════════════ */
';

set_config('scss', $scss . "\n" . $hero_and_lema_css, 'theme_moove');

// ── 2. Limpiar footer de versiones anteriores ─────────────────────────────────
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12','tau-v13','tau-v14','tau-v15'];
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

// ── 3. Footer: solo JS para insertar el bloque lema en la posición correcta ──
$footer_js = <<<'JS'
<script id="tau-v15-script">
(function(){
    var PORTRAIT = "/theme/tau_branding/assets/official/fray-guillermo.jpg";

    function buildLema(){
        var d = document.createElement("div");
        d.className = "tau-lema-bloque";
        d.innerHTML =
            '<div class="tau-lema-inner">' +
                '<div class="tau-fray-icon">&#10013;</div>' +
                '<div class="tau-lema-txt">' +
                    '<div class="tau-lema-linea">' +
                        '<p class="tau-lema-frase">' +
                            '&ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;' +
                        '</p>' +
                    '</div>' +
                    '<p class="tau-lema-autor">&mdash;&nbsp;Fray Guillermo de Castellana, OFMCap.</p>' +
                '</div>' +
            '</div>';

        // Reemplazar ícono por foto si existe
        var img = new Image();
        img.onload = function(){
            var icon = d.querySelector(".tau-fray-icon");
            if (!icon) return;
            var pic = document.createElement("img");
            pic.src = PORTRAIT; pic.className = "tau-fray-img";
            pic.alt = "Fray Guillermo de Castellana, OFMCap.";
            icon.parentNode.replaceChild(pic, icon);
        };
        img.src = PORTRAIT;
        return d;
    }

    function go(){
        if (document.querySelector(".tau-lema-bloque")) return;

        // Buscar el header de página y poner el lema justo después
        var anchors = [
            document.querySelector("#page-header"),
            document.querySelector("header[role='banner']"),
            document.querySelector(".moove-page-header-wrapper"),
            document.querySelector("#mooveslideshow") ?
                document.querySelector("#mooveslideshow").closest("section, header, [id*='header'], [class*='header']") : null,
            document.querySelector("#mooveslideshow")
        ];

        var lema = buildLema();
        for (var i = 0; i < anchors.length; i++) {
            var a = anchors[i];
            if (!a) continue;
            // Subir hasta que tenga un hermano siguiente
            var node = a;
            for (var j = 0; j < 5; j++) {
                if (node.parentNode && node.nextElementSibling) {
                    node.parentNode.insertBefore(lema, node.nextElementSibling);
                    return;
                }
                if (node.parentNode) node = node.parentNode;
            }
        }

        // Fallback: insertar antes del primer div de contenido principal
        var main = document.querySelector("#page-content, #region-main, main, #page");
        if (main) {
            main.parentNode.insertBefore(lema, main);
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", go);
    } else {
        go();
    }
    setTimeout(go, 500);
    setTimeout(go, 1200);
})();
</script>
JS;

set_config('additionalhtmlfooter', trim($footer) . "\n" . $footer_js, 'theme_moove');

theme_reset_all_caches();
echo "Fix final aplicado\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
