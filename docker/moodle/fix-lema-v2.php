<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Limpiar footers anteriores
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12','tau-v13','tau-v14'];
foreach (['<style','<script'] as $tag) {
    $close = ($tag === '<style') ? '</style>' : '</script>';
    $changed = true;
    while ($changed) {
        $changed = false; $pos = 0;
        while (($s = strpos($footer,$tag,$pos)) !== false) {
            $e = strpos($footer,$close,$s);
            if ($e===false) break;
            $block = substr($footer,$s,$e-$s+strlen($close));
            $hit = false;
            foreach ($kw as $k) { if (stripos($block,$k)!==false){$hit=true;break;} }
            if ($hit){$footer=substr($footer,0,$s).substr($footer,$e+strlen($close));$changed=true;break;}
            $pos=$e+strlen($close);
        }
    }
}

$inject = <<<'INJECT'
<style id="tau-v14-style">
/* Aros TAU */
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

/* Etiqueta TAU Campus Virtual encima del logo */
.tau-hero-label{
    position:absolute;
    right:6%;
    top:calc(50% - clamp(100px,14%,150px) - 18px);
    z-index:13;
    pointer-events:none;
    text-align:center;
    width:clamp(180px,25%,270px);
}
.tau-hero-label span{
    display:inline-block;
    font-family:'Manrope',system-ui,sans-serif;
    font-size:clamp(.65rem,.85vw,.80rem);
    font-weight:800;
    letter-spacing:.18em;
    text-transform:uppercase;
    color:rgba(255,255,255,.90);
    background:rgba(198,43,58,.55);
    backdrop-filter:blur(6px);
    -webkit-backdrop-filter:blur(6px);
    border:1px solid rgba(255,255,255,.20);
    border-radius:20px;
    padding:4px 14px;
    text-shadow:0 1px 4px rgba(0,0,0,.4);
}
@media(max-width:900px){.tau-hero-label{right:2%;}}
@media(max-width:768px){.tau-hero-label,.tau-rings-wrap{display:none!important}}

/* LEMA */
.tau-lema{
    position:relative;overflow:hidden;
    background:linear-gradient(180deg,#1a0306 0%,#3d0b12 45%,#1a0306 100%);
    border-top:3px solid #c62b3a;
    border-bottom:1px solid rgba(198,43,58,.20);
    padding:26px 1.5rem 22px;
    margin:0!important;
    display:block!important;
    visibility:visible!important;
    opacity:1!important;
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
    width:80px;height:80px;border-radius:50%;
    background:rgba(198,43,58,.18);
    border:2px solid rgba(198,43,58,.50);
    display:flex;align-items:center;justify-content:center;
    font-size:1.9rem;flex-shrink:0;
    color:rgba(255,255,255,.55);font-family:Georgia,serif;
}
.tau-fray-portrait{
    width:80px;height:80px;border-radius:50%;
    object-fit:cover;object-position:top;
    border:2px solid rgba(198,43,58,.60);
    box-shadow:0 4px 18px rgba(0,0,0,.50);
    flex-shrink:0;display:block;
}
.tau-lema-text{text-align:center;}
.tau-lema-sep{display:inline-flex;align-items:center;gap:12px;margin-bottom:5px;}
.tau-lema-sep::before{content:"";display:block;width:40px;height:1px;background:linear-gradient(90deg,transparent,rgba(198,43,58,.80));}
.tau-lema-sep::after {content:"";display:block;width:40px;height:1px;background:linear-gradient(90deg,rgba(198,43,58,.80),transparent);}
.tau-lema-q{
    font-family:Georgia,"Times New Roman",serif;
    font-size:clamp(.86rem,1.2vw,1.02rem);font-style:italic;
    color:rgba(255,255,255,.93);letter-spacing:.03em;margin:0;
    text-shadow:0 1px 8px rgba(0,0,0,.5);
}
.tau-lema-q strong{color:#fff;font-style:normal;font-weight:700;letter-spacing:.08em;text-transform:uppercase;}
.tau-lema-author{
    font-size:clamp(.64rem,.88vw,.74rem);
    color:rgba(255,255,255,.42);letter-spacing:.10em;
    text-transform:uppercase;margin:5px 0 0;
    font-style:normal;font-family:system-ui,sans-serif;
}
</style>
<script id="tau-v14-script">
(function(){
    var PORTRAIT="/theme/tau_branding/assets/official/fray-guillermo.jpg";

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
          '</g></svg>';
        ss.appendChild(w);
    }

    function addLabel(){
        var ss=document.getElementById("mooveslideshow");
        if(!ss||document.querySelector(".tau-hero-label")) return;
        var lb=document.createElement("div");
        lb.className="tau-hero-label";
        lb.innerHTML='<span>TAU Campus Virtual</span>';
        ss.appendChild(lb);
    }

    function buildLema(){
        var el=document.createElement("div");
        el.className="tau-lema";
        el.innerHTML=
          '<div class="tau-lema-inner">'+
            '<div class="tau-fray-placeholder">&#10013;</div>'+
            '<div class="tau-lema-text">'+
              '<div class="tau-lema-sep">'+
                '<p class="tau-lema-q">&ldquo;<strong>Hombres nuevos</strong> para <strong>tiempos nuevos</strong>&rdquo;</p>'+
              '</div>'+
              '<p class="tau-lema-author">&mdash;&nbsp;Fray Guillermo de Castellana, OFMCap.</p>'+
            '</div>'+
          '</div>';
        /* intentar cargar retrato */
        var img=new Image();
        img.onload=function(){
            var ph=el.querySelector(".tau-fray-placeholder");
            if(!ph) return;
            var pic=document.createElement("img");
            pic.src=PORTRAIT; pic.className="tau-fray-portrait";
            pic.alt="Fray Guillermo de Castellana, OFMCap.";
            ph.parentNode.replaceChild(pic,ph);
        };
        img.src=PORTRAIT;
        return el;
    }

    function insertLema(){
        if(document.querySelector(".tau-lema")) return true;
        var lema=buildLema();

        /* Estrategia 1: después del header de página */
        var hdr=document.querySelector('#page-header,header[role="banner"],.moove-page-header-wrapper,.header-slideshow');
        if(hdr){ hdr.insertAdjacentElement('afterend',lema); return true; }

        /* Estrategia 2: primer hijo de #page-content */
        var pc=document.querySelector('#page-content,.main-content,[role="main"]');
        if(pc){ pc.insertAdjacentElement('afterbegin',lema); return true; }

        /* Estrategia 3: después de #mooveslideshow subiendo padres */
        var ss=document.getElementById("mooveslideshow");
        if(ss){
            var node=ss;
            for(var i=0;i<8;i++){
                if(!node.parentNode) break;
                if(node.nextElementSibling){
                    node.parentNode.insertBefore(lema,node.nextElementSibling);
                    return true;
                }
                node=node.parentNode;
            }
            /* append al body como fallback */
            document.body.insertBefore(lema, document.body.children[2]||document.body.firstChild);
            return true;
        }
        return false;
    }

    function go(){
        var ss=document.getElementById("mooveslideshow");
        if(!ss){ setTimeout(go,300); return; }
        addRings();
        addLabel();
        var ok=insertLema();
        if(!ok) setTimeout(insertLema,600);
    }

    if(document.readyState==="loading") document.addEventListener("DOMContentLoaded",go);
    else go();
    setTimeout(go,1000);
})();
</script>
INJECT;

set_config('additionalhtmlfooter', trim($footer)."\n".$inject, 'theme_moove');
theme_reset_all_caches();
echo "Fix lema v2 aplicado\n";
echo "footer: ".strlen(get_config('theme_moove','additionalhtmlfooter'))." bytes\n";
