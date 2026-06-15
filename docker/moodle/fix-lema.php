<?php
/**
 * Fix definitivo del lema — estrategia DOM robusta
 * Inserta DESPUÉS del contenedor padre del slideshow (no adentro)
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$footer = get_config('theme_moove', 'additionalhtmlfooter');

// Quitar versiones anteriores
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12','tau-v13'];
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

$inject = <<<'INJECT'
<style id="tau-v13-style">
/* Aros giratorios alrededor del logo TAU */
.tau-rings-wrap{position:absolute;right:6%;top:50%;transform:translateY(-50%);width:clamp(180px,25%,270px);aspect-ratio:1;pointer-events:none;z-index:12;overflow:visible;}
.tau-rings-wrap svg{width:100%;height:100%;overflow:visible;}
@keyframes tau-cw  {to{transform:rotate(360deg)}}
@keyframes tau-ccw {to{transform:rotate(-360deg)}}
@keyframes tau-pa  {0%,100%{opacity:.12}50%{opacity:.35}}
@keyframes tau-nd  {from{r:3.5;opacity:.35}to{r:6;opacity:1}}
@keyframes tau-nw  {from{r:2.5;opacity:.20}to{r:4.5;opacity:.75}}
.tau-gc {animation:tau-cw  22s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-gcc{animation:tau-ccw 30s linear infinite;transform-origin:100px 100px;transform-box:fill-box;}
.tau-pa{animation:tau-pa 5s ease-in-out infinite;}
.tau-nd{animation:tau-nd 3s ease-in-out infinite alternate;}
.tau-nd:nth-child(2){animation-delay:-.75s}.tau-nd:nth-child(3){animation-delay:-1.5s}.tau-nd:nth-child(4){animation-delay:-2.25s}
.tau-nw{animation:tau-nw 3.8s ease-in-out infinite alternate;}
.tau-nw:nth-child(2){animation-delay:-.95s}.tau-nw:nth-child(3){animation-delay:-1.9s}.tau-nw:nth-child(4){animation-delay:-2.85s}

/* LEMA */
.tau-lema{
    position:relative;overflow:hidden;
    background:linear-gradient(180deg,#2d050a 0%,#4a0e17 45%,#2d050a 100%);
    border-top:3px solid #c62b3a;
    border-bottom:1px solid rgba(198,43,58,.25);
    padding:26px 1.5rem 22px;
    margin:0;
}
.tau-lema::before{
    content:"";position:absolute;inset:0;pointer-events:none;
    background-image:radial-gradient(circle,rgba(198,43,58,.07) 1px,transparent 1px);
    background-size:26px 26px;
}
.tau-lema-inner{
    position:relative;z-index:1;
    max-width:820px;margin:0 auto;
    display:flex;align-items:center;gap:24px;
    justify-content:center;flex-wrap:wrap;
}
.tau-fray-placeholder{
    width:84px;height:84px;border-radius:50%;
    background:rgba(198,43,58,.15);
    border:3px solid rgba(198,43,58,.45);
    display:flex;align-items:center;justify-content:center;
    font-size:2rem;flex-shrink:0;color:rgba(255,255,255,.60);
    font-family:serif;
}
.tau-fray-portrait{
    width:84px;height:84px;border-radius:50%;
    object-fit:cover;object-position:top;
    border:3px solid rgba(198,43,58,.60);
    box-shadow:0 4px 18px rgba(0,0,0,.45);
    flex-shrink:0;display:block;
}
.tau-lema-text{text-align:center;}
.tau-lema-row{display:inline-flex;align-items:center;gap:14px;margin-bottom:5px;}
.tau-lema-row::before{content:"";display:block;width:44px;height:1.5px;background:linear-gradient(90deg,transparent,rgba(198,43,58,.70));}
.tau-lema-row::after {content:"";display:block;width:44px;height:1.5px;background:linear-gradient(90deg,rgba(198,43,58,.70),transparent);}
.tau-lema-q{font-family:Georgia,"Times New Roman",serif;font-size:clamp(.84rem,1.2vw,1rem);font-style:italic;color:rgba(255,255,255,.93);letter-spacing:.04em;margin:0;text-shadow:0 1px 8px rgba(0,0,0,.5);}
.tau-lema-q strong{color:#fff;font-style:normal;font-weight:700;letter-spacing:.10em;text-transform:uppercase;}
.tau-lema-author{font-size:clamp(.64rem,.88vw,.74rem);color:rgba(255,255,255,.45);letter-spacing:.10em;text-transform:uppercase;margin:4px 0 0;font-style:normal;font-family:system-ui,sans-serif;}
@media(max-width:600px){.tau-fray-placeholder,.tau-fray-portrait{width:60px;height:60px;font-size:1.5rem;}.tau-rings-wrap{display:none!important}}
</style>
<script id="tau-v13-script">
(function(){
    var PORTRAIT_URL="/theme/tau_branding/assets/official/fray-guillermo.jpg";

    function insertLema(){
        if(document.querySelector(".tau-lema")) return;

        var lema=document.createElement("div");
        lema.className="tau-lema";
        lema.innerHTML=
          '<div class="tau-lema-inner">'+
            '<div class="tau-fray-placeholder">&#10013;</div>'+
            '<div class="tau-lema-text">'+
              '<div class="tau-lema-row">'+
                '<p class="tau-lema-q">&ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;</p>'+
              '</div>'+
              '<p class="tau-lema-author">&mdash; Fray Guillermo de Castellana, OFMCap.</p>'+
            '</div>'+
          '</div>';

        /* Cargar retrato si existe */
        var img=new Image();
        img.onload=function(){
            var p=lema.querySelector(".tau-fray-placeholder");
            if(p){var el=document.createElement("img");el.src=PORTRAIT_URL;el.className="tau-fray-portrait";el.alt="Fray Guillermo de Castellana, OFMCap.";p.parentNode.replaceChild(el,p);}
        };
        img.src=PORTRAIT_URL;

        /* Buscar punto de inserción: subir hasta encontrar
           un padre con varios hijos (el wrapper de sección) */
        var ss=document.getElementById("mooveslideshow");
        if(!ss) return false;

        /* Subir en el DOM hasta que el padre tenga hermanos después */
        var anchor=ss;
        for(var i=0;i<6;i++){
            if(!anchor.parentNode) break;
            /* Si el padre tiene un siguiente hermano elemento, insertar lema ahí */
            var next=anchor.nextElementSibling;
            if(next){
                anchor.parentNode.insertBefore(lema,next);
                return true;
            }
            anchor=anchor.parentNode;
        }
        /* Último recurso: append al padre más cercano con id/clase de sección */
        var targets=['#page-content','#page-wrapper','#page','main','body'];
        for(var t=0;t<targets.length;t++){
            var el=document.querySelector(targets[t]);
            if(el){el.insertBefore(lema,el.firstChild);return true;}
        }
        return false;
    }

    function addRings(){
        var ss=document.getElementById("mooveslideshow");
        if(!ss||document.querySelector(".tau-rings-wrap")) return;
        var w=document.createElement("div");
        w.className="tau-rings-wrap";
        w.innerHTML=
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

    function go(){
        addRings();
        var ok=insertLema();
        if(!ok) setTimeout(go,400);
    }

    if(document.readyState==="loading") document.addEventListener("DOMContentLoaded",go);
    else go();
    setTimeout(go,900);
})();
</script>
INJECT;

set_config('additionalhtmlfooter', trim($footer) . "\n" . $inject, 'theme_moove');
theme_reset_all_caches();
echo "Fix lema aplicado\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
