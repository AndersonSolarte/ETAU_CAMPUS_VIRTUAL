<?php
/**
 * Reemplaza el escudo CESMAG en el hero por el logo TAU oficial
 * y coloca la sección del lema debajo con espacio para retrato.
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── CSS: reemplazar escudo por logo TAU ──────────────────────────────────────
$hero_css = '

/* TAU-LOGO-HERO ════════════════════════════════════════════ */
#mooveslideshow { position: relative !important; overflow: visible !important; }
#mooveslideshow .carousel-inner { overflow: hidden !important; }
#mooveslideshow .carousel-item {
    min-height: clamp(300px,44vh,500px) !important;
    display: flex !important; align-items: center !important;
}
#mooveslideshow .carousel-caption {
    position: relative !important; bottom: auto !important;
    left: auto !important; right: auto !important;
    max-width: 54% !important; z-index: 5 !important;
}
/* Logo TAU a la derecha del hero */
#mooveslideshow::after {
    content: "" !important; position: absolute !important;
    right: 6% !important; top: 50% !important; transform: translateY(-50%) !important;
    width: clamp(180px,25%,270px) !important; aspect-ratio: 1 !important;
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
@media(max-width:900px){ #mooveslideshow::after{width:clamp(140px,20vw,180px);right:2%;} }
@media(max-width:768px){ #mooveslideshow::after{display:none !important;} }
/* TAU-LOGO-HERO-FIN ════════════════════════════════════════ */
';

// ── FOOTER: aros + LEMA ─────────────────────────────────────────────────────
$footer_inject = <<<'FOOTER'
<style id="tau-v12-style">
/* Aros alrededor del logo TAU */
.tau-rings-wrap{position:absolute;right:6%;top:50%;transform:translateY(-50%);width:clamp(180px,25%,270px);aspect-ratio:1;pointer-events:none;z-index:12;overflow:visible;}
.tau-rings-wrap svg{width:100%;height:100%;overflow:visible;}
@keyframes tau-cw  {to{transform:rotate(360deg)}}
@keyframes tau-ccw {to{transform:rotate(-360deg)}}
@keyframes tau-pa  {0%,100%{opacity:.12}50%{opacity:.35}}
@keyframes tau-nd  {from{r:3.5;opacity:.35}to{r:6;opacity:1}}
@keyframes tau-nw  {from{r:2.5;opacity:.20}to{r:4.5;opacity:.75}}
.tau-gc {animation:tau-cw  22s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-gcc{animation:tau-ccw 30s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-pa {animation:tau-pa 5s ease-in-out infinite;}
.tau-nd {animation:tau-nd 3s ease-in-out infinite alternate;}
.tau-nd:nth-child(2){animation-delay:-.75s}.tau-nd:nth-child(3){animation-delay:-1.5s}.tau-nd:nth-child(4){animation-delay:-2.25s}
.tau-nw {animation:tau-nw 3.8s ease-in-out infinite alternate;}
.tau-nw:nth-child(2){animation-delay:-.95s}.tau-nw:nth-child(3){animation-delay:-1.9s}.tau-nw:nth-child(4){animation-delay:-2.85s}

/* SECCIÓN LEMA + RETRATO */
.tau-lema {
    position: relative; overflow: hidden;
    background: linear-gradient(180deg, #2d050a 0%, #4a0e17 40%, #2d050a 100%);
    border-top: 3px solid #c62b3a;
    border-bottom: 1px solid rgba(198,43,58,.25);
    padding: 28px 1rem 24px;
}
.tau-lema::before {
    content:""; position:absolute; inset:0; pointer-events:none;
    background-image: radial-gradient(circle, rgba(198,43,58,.08) 1px, transparent 1px);
    background-size: 26px 26px;
}
/* Layout: retrato a la izquierda, texto a la derecha (cuando hay imagen) */
.tau-lema-inner {
    position: relative; z-index: 1;
    max-width: 860px; margin: 0 auto;
    display: flex; align-items: center; gap: 28px;
    justify-content: center; flex-wrap: wrap;
}
/* Retrato de Fray Guillermo */
.tau-fray-portrait {
    width: 90px; height: 90px; border-radius: 50%;
    object-fit: cover; object-position: top center;
    border: 3px solid rgba(198,43,58,.60);
    box-shadow: 0 4px 20px rgba(0,0,0,.45);
    flex-shrink: 0; display: block;
}
/* Si no hay imagen: placeholder circular */
.tau-fray-placeholder {
    width: 90px; height: 90px; border-radius: 50%;
    background: rgba(198,43,58,.15);
    border: 3px solid rgba(198,43,58,.40);
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; flex-shrink: 0;
}
.tau-lema-text { text-align: center; }
.tau-lema-row {
    display: inline-flex; align-items: center; gap: 14px;
    margin-bottom: 6px;
}
.tau-lema-row::before {
    content:""; display:block; width:44px; height:1.5px;
    background: linear-gradient(90deg, transparent, rgba(198,43,58,.70));
}
.tau-lema-row::after {
    content:""; display:block; width:44px; height:1.5px;
    background: linear-gradient(90deg, rgba(198,43,58,.70), transparent);
}
.tau-lema-q {
    font-family: Georgia,"Times New Roman",serif;
    font-size: clamp(.82rem,1.15vw,.98rem); font-style: italic;
    color: rgba(255,255,255,.92); letter-spacing: .04em; margin: 0;
    text-shadow: 0 1px 8px rgba(0,0,0,.5);
}
.tau-lema-q strong {
    color: #fff; font-style: normal; font-weight: 700;
    letter-spacing: .10em; text-transform: uppercase;
}
.tau-lema-author {
    font-family: system-ui, Arial, sans-serif;
    font-size: clamp(.65rem,.88vw,.74rem);
    color: rgba(255,255,255,.45); letter-spacing: .10em;
    text-transform: uppercase; margin: 4px 0 0;
    font-style: normal;
}
@media(max-width:600px){
    .tau-fray-portrait,.tau-fray-placeholder{width:64px;height:64px;font-size:1.6rem;}
    .tau-rings-wrap{display:none!important}
}
</style>
<script id="tau-v12-script">
(function(){
    var PORTRAIT_URL = "/theme/tau_branding/assets/official/fray-guillermo.jpg";

    function go(){
        var ss = document.getElementById("mooveslideshow");
        if(!ss){ setTimeout(go, 300); return; }

        /* --- AROS --- */
        if(!document.querySelector(".tau-rings-wrap")){
            var w = document.createElement("div");
            w.className = "tau-rings-wrap";
            w.innerHTML =
              '<svg viewBox="-40 -40 280 280" xmlns="http://www.w3.org/2000/svg">'+
              '<circle class="tau-pa" cx="100" cy="100" r="95" fill="rgba(80,10,20,.20)" stroke="none"/>'+
              '<circle class="tau-pa" cx="100" cy="100" r="122" fill="none" stroke="rgba(198,43,58,.28)" stroke-width="1.2" stroke-dasharray="8 14"/>'+
              '<g class="tau-gcc">'+
                '<circle cx="100" cy="100" r="112" fill="none" stroke="rgba(255,255,255,.16)" stroke-width="1" stroke-dasharray="5 18"/>'+
                '<circle class="tau-nw" cx="100" cy="-12" r="4" fill="rgba(255,255,255,.75)"/>'+
                '<circle class="tau-nw" cx="212" cy="100" r="4" fill="rgba(255,255,255,.75)"/>'+
                '<circle class="tau-nw" cx="100" cy="212" r="4" fill="rgba(255,255,255,.75)"/>'+
                '<circle class="tau-nw" cx="-12" cy="100" r="4" fill="rgba(255,255,255,.75)"/>'+
              '</g>'+
              '<g class="tau-gc">'+
                '<circle cx="100" cy="100" r="100" fill="none" stroke="rgba(198,43,58,.50)" stroke-width="1.5" stroke-dasharray="16 8"/>'+
                '<circle class="tau-nd" cx="100" cy="0"   r="5.5" fill="#c62b3a"/>'+
                '<circle class="tau-nd" cx="200" cy="100" r="5.5" fill="#c62b3a"/>'+
                '<circle class="tau-nd" cx="100" cy="200" r="5.5" fill="#c62b3a"/>'+
                '<circle class="tau-nd" cx="0"   cy="100" r="5.5" fill="#c62b3a"/>'+
              '</g>'+
              '</svg>';
            ss.appendChild(w);
        }

        /* --- LEMA --- */
        if(!document.querySelector(".tau-lema")){
            var lema = document.createElement("div");
            lema.className = "tau-lema";

            /* Intentar cargar retrato; si falla, mostrar ícono */
            var portraitHtml = '';
            var img = new Image();
            img.onload = function(){
                var p = lema.querySelector(".tau-fray-placeholder");
                if(p){
                    var el = document.createElement("img");
                    el.src = PORTRAIT_URL;
                    el.className = "tau-fray-portrait";
                    el.alt = "Fray Guillermo de Castellana";
                    p.parentNode.replaceChild(el, p);
                }
            };
            img.src = PORTRAIT_URL;

            lema.innerHTML =
              '<div class="tau-lema-inner">'+
                '<div class="tau-fray-placeholder">✝</div>'+
                '<div class="tau-lema-text">'+
                  '<div class="tau-lema-row">'+
                    '<p class="tau-lema-q">"<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>"</p>'+
                  '</div>'+
                  '<p class="tau-lema-author">— Fray Guillermo de Castellana, OFMCap.</p>'+
                '</div>'+
              '</div>';

            var ref = ss.nextSibling;
            while(ref && ref.nodeType === 3) ref = ref.nextSibling;
            if(ref) ss.parentNode.insertBefore(lema, ref);
            else ss.parentNode.appendChild(lema);
        }
    }

    if(document.readyState === "loading") document.addEventListener("DOMContentLoaded", go);
    else go();
    setTimeout(go, 800);
})();
</script>
FOOTER;

// ── Aplicar en BD ────────────────────────────────────────────────────────────
// SCSS: quitar bloque anterior de escudo/logo y poner el nuevo
$scss = get_config('theme_moove', 'scss');
foreach (['/* TAU-ESCUDO-HERO', '/* TAU-LOGO-HERO'] as $marker) {
    $pos = strpos($scss, $marker);
    if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));
}
set_config('scss', $scss . "\n" . $hero_css, 'theme_moove');

// Footer: limpiar versiones anteriores
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12'];
foreach (['<style','<script'] as $tag) {
    $close = ($tag === '<style') ? '</style>' : '</script>';
    $changed = true;
    while ($changed) {
        $changed = false; $pos = 0;
        while (($s = strpos($footer, $tag, $pos)) !== false) {
            $e = strpos($footer, $close, $s);
            if ($e === false) break;
            $block = substr($footer, $s, $e - $s + strlen($close));
            $hit = false;
            foreach ($kw as $k) { if (stripos($block, $k) !== false) { $hit = true; break; } }
            if ($hit) { $footer = substr($footer, 0, $s) . substr($footer, $e + strlen($close)); $changed = true; break; }
            $pos = $e + strlen($close);
        }
    }
}
set_config('additionalhtmlfooter', trim($footer) . "\n" . $footer_inject, 'theme_moove');

theme_reset_all_caches();
echo "Logo TAU en hero + lema actualizado\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
echo "\nNOTA: Para la foto de Fray Guillermo, coloca la imagen en:\n";
echo "apps/moodle/theme/tau_branding/assets/official/fray-guillermo.jpg\n";
