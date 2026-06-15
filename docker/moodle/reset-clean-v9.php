<?php
/**
 * RESET COMPLETO — elimina TODO lo tau acumulado y aplica versión única limpia
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ═══════════════════════════════════════════════════════════════
// 1. LIMPIAR SCSS — quitar TODO desde el primer marcador tau
// ═══════════════════════════════════════════════════════════════
$scss_base = get_config('theme_moove', 'scss');

// Cortar en el primer bloque tau que aparezca
$cuts = [
    '/* ═══════════════════════════════════════════════════',
    '// ================================================================',
    '#mooveslideshow .carousel-item > img.d-block',
];
foreach ($cuts as $cut) {
    $p = strpos($scss_base, $cut);
    if ($p !== false) { $scss_base = rtrim(substr($scss_base, 0, $p)); break; }
}

// ═══════════════════════════════════════════════════════════════
// 2. CSS ÚNICO — fondo hero + escudo estático
// ═══════════════════════════════════════════════════════════════
$css = '

/* TAU-ESCUDO-v9-ÚNICO ════════════════════════════════════════ */

/* Ocultar imagen de fondo original del carrusel */
#mooveslideshow .carousel-item > img.d-block {
    opacity: 0 !important; pointer-events: none !important;
    position: absolute !important; inset: 0 !important;
}

/* Fondo hero: azul marino institucional */
#mooveslideshow .carousel-item {
    position: relative !important;
    background:
        radial-gradient(ellipse 70% 88% at 10% 50%, rgba(5,18,55,.96) 0%, transparent 68%),
        radial-gradient(ellipse 52% 62% at 50% 50%, rgba(14,32,78,.58) 0%, transparent 84%),
        radial-gradient(ellipse 45% 52% at 84% 50%, rgba(10,42,100,.38) 0%, transparent 62%),
        #080f22 !important;
}

/* Grid de puntos sutil */
#mooveslideshow .carousel-item::before {
    content:"" !important; position:absolute !important; inset:0 !important;
    pointer-events:none !important; z-index:0 !important;
    background-image: radial-gradient(circle, rgba(100,150,255,.06) 1px, transparent 1px) !important;
    background-size: 44px 44px !important;
}

/* Barra lateral bicolor */
#mooveslideshow::before {
    content:"" !important; position:absolute !important;
    left:0 !important; top:0 !important; width:4px !important; height:100% !important;
    background: linear-gradient(to bottom, #0d4f8b 50%, #c0253a 50%) !important;
    z-index:20 !important;
}

/* Contenedor principal */
#mooveslideshow {
    position: relative !important;
    overflow: visible !important;
}
#mooveslideshow .carousel-inner { overflow: hidden !important; }
#mooveslideshow .carousel-caption { position: relative !important; z-index:5 !important; }

/* Card de texto */
#mooveslideshow .carousel-caption {
    background: linear-gradient(135deg, rgba(4,12,36,.82), rgba(7,20,58,.68)) !important;
    border: 1px solid rgba(13,79,139,.32) !important;
    border-radius: 16px !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    box-shadow: 0 8px 32px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.07) !important;
    padding: 1.8rem 2rem !important;
}
#mooveslideshow .carousel-caption h5,
#mooveslideshow .carousel-caption .title {
    font-size: clamp(1.7rem,3vw,2.6rem) !important;
    font-weight: 800 !important; color:#fff !important;
}
#mooveslideshow .carousel-caption p { color: rgba(255,255,255,.76) !important; }
#mooveslideshow .carousel-caption .btn,
#mooveslideshow .carousel-caption a.btn {
    background: linear-gradient(135deg, #c0253a, #891b2c) !important;
    border:none !important; border-radius:10px !important;
    color:#fff !important; font-weight:700 !important;
    padding:.70rem 1.5rem !important;
    box-shadow: 0 5px 18px rgba(192,37,58,.38) !important;
}

/* ESCUDO — estático, sin ninguna animación */
#mooveslideshow::after {
    content:"" !important; position:absolute !important;
    right:6% !important; top:50% !important;
    transform: translateY(-50%) !important;
    width: clamp(200px,28%,300px) !important;
    aspect-ratio: 1 !important;
    background-image: url("/theme/tau_branding/assets/official/cesmag-escudo-hero.png") !important;
    background-size: 82% !important; background-repeat: no-repeat !important;
    background-position: center !important; background-color: transparent !important;
    border:none !important; outline:none !important;
    z-index:9 !important; pointer-events:none !important;
    filter: drop-shadow(0 6px 34px rgba(0,0,0,.65)) brightness(1.04) !important;
    animation: tau-escudo-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-escudo-in {
    from { opacity:0; transform:translateY(-47%) scale(.88); }
    to   { opacity:1; transform:translateY(-50%) scale(1); }
}

@media (max-width:900px) {
    #mooveslideshow::after { width:clamp(160px,22vw,210px); right:2%; }
}
@media (max-width:768px) {
    #mooveslideshow::after { display:none !important; }
}
/* TAU-ESCUDO-v9-FIN ══════════════════════════════════════════ */
';

// ═══════════════════════════════════════════════════════════════
// 3. FOOTER ÚNICO — style + script (sin basura anterior)
// ═══════════════════════════════════════════════════════════════
$footer_clean = '
<style id="tau-v9-style">
/* Aros giratorios */
.tau-rings-wrap {
    position:absolute; right:6%; top:50%;
    transform:translateY(-50%);
    width:clamp(200px,28%,300px); aspect-ratio:1;
    pointer-events:none; z-index:12; overflow:visible;
}
.tau-rings-wrap svg { width:100%; height:100%; overflow:visible; }
@keyframes tau-cw   { to { transform:rotate(360deg);  } }
@keyframes tau-ccw  { to { transform:rotate(-360deg); } }
@keyframes tau-pulse-a { 0%,100%{opacity:.18} 50%{opacity:.50} }
@keyframes tau-nd { from{r:4;opacity:.45} to{r:7;opacity:1} }
@keyframes tau-nw { from{r:3;opacity:.28} to{r:5.5;opacity:.82} }
.tau-gc  { animation:tau-cw  22s linear infinite; transform-origin:100px 100px; transform-box:fill-box; }
.tau-gcc { animation:tau-ccw 32s linear infinite; transform-origin:100px 100px; transform-box:fill-box; }
.tau-pa  { animation:tau-pulse-a 4.5s ease-in-out infinite; }
.tau-nd  { animation:tau-nd 3s ease-in-out infinite alternate; }
.tau-nd:nth-child(2){animation-delay:-.75s}
.tau-nd:nth-child(3){animation-delay:-1.5s}
.tau-nd:nth-child(4){animation-delay:-2.25s}
.tau-nw  { animation:tau-nw 3.8s ease-in-out infinite alternate; }
.tau-nw:nth-child(2){animation-delay:-.95s}
.tau-nw:nth-child(3){animation-delay:-1.9s}
.tau-nw:nth-child(4){animation-delay:-2.85s}

/* Lema */
.tau-lema {
    position:relative; text-align:center; overflow:hidden;
    background:linear-gradient(180deg,#060e24 0%,#091530 70%,#070d20 100%);
    border-top:2px solid #0d4f8b;
    padding:16px 1rem 14px;
}
.tau-lema::before {
    content:""; position:absolute; inset:0; pointer-events:none;
    background-image:radial-gradient(circle,rgba(13,79,139,.09) 1px,transparent 1px);
    background-size:28px 28px;
}
.tau-lema-row {
    display:inline-flex; align-items:center; gap:16px; position:relative;
}
.tau-lema-row::before {
    content:""; display:block; width:55px; height:1.5px;
    background:linear-gradient(90deg,transparent,#0d4f8b);
}
.tau-lema-row::after {
    content:""; display:block; width:55px; height:1.5px;
    background:linear-gradient(90deg,#0d4f8b,transparent);
}
.tau-lema-q {
    font-family:Georgia,"Times New Roman",serif;
    font-size:clamp(.80rem,1.1vw,.95rem); font-style:italic;
    color:rgba(255,255,255,.90); letter-spacing:.06em; margin:0;
    text-shadow:0 1px 8px rgba(0,0,0,.5);
}
.tau-lema-q strong {
    color:#fff; font-style:normal; font-weight:700;
    letter-spacing:.13em; text-transform:uppercase;
}
.tau-lema-author {
    font-family:system-ui,Arial,sans-serif;
    font-size:clamp(.63rem,.88vw,.74rem);
    color:rgba(255,255,255,.42); letter-spacing:.10em;
    text-transform:uppercase; margin:4px 0 0;
}
@media(max-width:768px){ .tau-rings-wrap{display:none!important} }
</style>

<script id="tau-v9-script">
(function(){
    function go(){
        var ss = document.getElementById("mooveslideshow");
        if(!ss){ setTimeout(go,250); return; }

        /* Aros */
        if(!document.querySelector(".tau-rings-wrap")){
            var w=document.createElement("div");
            w.className="tau-rings-wrap";
            w.innerHTML=
              "<svg viewBox=\"-34 -34 268 268\" xmlns=\"http://www.w3.org/2000/svg\">"+
              "<circle class=\"tau-pa\" cx=\"100\" cy=\"100\" r=\"92\" fill=\"rgba(13,55,140,.16)\" stroke=\"none\"/>"+
              "<circle class=\"tau-pa\" cx=\"100\" cy=\"100\" r=\"120\" fill=\"none\" stroke=\"rgba(13,79,139,.35)\" stroke-width=\"1.4\" stroke-dasharray=\"8 14\"/>"+
              "<g class=\"tau-gcc\">"+
                "<circle cx=\"100\" cy=\"100\" r=\"110\" fill=\"none\" stroke=\"rgba(255,255,255,.22)\" stroke-width=\"1.1\" stroke-dasharray=\"5 18\"/>"+
                "<circle class=\"tau-nw\" cx=\"100\" cy=\"-10\" r=\"4\" fill=\"rgba(255,255,255,.80)\"/>"+
                "<circle class=\"tau-nw\" cx=\"210\" cy=\"100\" r=\"4\" fill=\"rgba(255,255,255,.80)\"/>"+
                "<circle class=\"tau-nw\" cx=\"100\" cy=\"210\" r=\"4\" fill=\"rgba(255,255,255,.80)\"/>"+
                "<circle class=\"tau-nw\" cx=\"-10\" cy=\"100\" r=\"4\" fill=\"rgba(255,255,255,.80)\"/>"+
              "</g>"+
              "<g class=\"tau-gc\">"+
                "<circle cx=\"100\" cy=\"100\" r=\"98\" fill=\"none\" stroke=\"rgba(255,255,255,.30)\" stroke-width=\"1.6\" stroke-dasharray=\"16 8\"/>"+
                "<circle class=\"tau-nd\" cx=\"100\" cy=\"2\"   r=\"6.5\" fill=\"#c0253a\"/>"+
                "<circle class=\"tau-nd\" cx=\"198\" cy=\"100\" r=\"6.5\" fill=\"#c0253a\"/>"+
                "<circle class=\"tau-nd\" cx=\"100\" cy=\"198\" r=\"6.5\" fill=\"#c0253a\"/>"+
                "<circle class=\"tau-nd\" cx=\"2\"   cy=\"100\" r=\"6.5\" fill=\"#c0253a\"/>"+
              "</g>"+
              "</svg>";
            ss.appendChild(w);
        }

        /* Lema */
        if(!document.querySelector(".tau-lema")){
            var lema=document.createElement("div");
            lema.className="tau-lema";
            lema.innerHTML=
              "<div class=\"tau-lema-row\">"+
                "<p class=\"tau-lema-q\">“<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>”</p>"+
              "</div>"+
              "<p class=\"tau-lema-author\">Fray Guillermo de Castellana, OFMCap.</p>";
            var ref=ss.nextSibling;
            while(ref && ref.nodeType===3) ref=ref.nextSibling;
            if(ref) ss.parentNode.insertBefore(lema,ref);
            else ss.parentNode.appendChild(lema);
        }
    }
    if(document.readyState==="loading") document.addEventListener("DOMContentLoaded",go);
    else go();
})();
</script>
';

// ═══════════════════════════════════════════════════════════════
// 4. LIMPIAR footer anterior COMPLETAMENTE (quitar TODO lo tau)
// ═══════════════════════════════════════════════════════════════
$footer_old = get_config('theme_moove', 'additionalhtmlfooter');

// Quitar todos los bloques <style ...> y <script ...> que contienen tau
$tags = [['<style', '</style>'], ['<script', '</script>']];
$keywords = ['tau','mooveslideshow','escudo','lema','rings','injectEscudo','addTauElements'];

foreach ($tags as [$open, $close]) {
    $changed = true;
    while ($changed) {
        $changed = false;
        $pos = 0;
        while (($s = strpos($footer_old, $open, $pos)) !== false) {
            $e = strpos($footer_old, $close, $s);
            if ($e === false) break;
            $block = substr($footer_old, $s, $e - $s + strlen($close));
            $is_tau = false;
            foreach ($keywords as $kw) {
                if (stripos($block, $kw) !== false) { $is_tau = true; break; }
            }
            if ($is_tau) {
                $footer_old = substr($footer_old, 0, $s) . substr($footer_old, $e + strlen($close));
                $changed = true;
                break;
            }
            $pos = $e + strlen($close);
        }
    }
}

$footer_final = trim($footer_old) . "\n" . $footer_clean;

// ═══════════════════════════════════════════════════════════════
// 5. GUARDAR Y PURGAR CACHÉ
// ═══════════════════════════════════════════════════════════════
set_config('scss', $scss_base . "\n" . $css, 'theme_moove');
set_config('additionalhtmlfooter', $footer_final, 'theme_moove');
theme_reset_all_caches();

echo "RESET v9 OK\n";
echo "scss:   " . strlen(get_config('theme_moove','scss'))                 . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
