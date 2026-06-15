<?php
/**
 * redesign-hero.php
 * Mejora el diseño del hero: lado derecho (logo TAU + lema) más profesional
 * y corrige los colores del texto del banner
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$css = '
.tau-banner-overlay{position:absolute!important;top:0!important;left:0!important;width:100%!important;height:100%!important;display:flex!important;align-items:center!important;justify-content:space-between!important;padding:0 7% 0 8%!important;z-index:10!important;box-sizing:border-box!important;}

/* ── Lado izquierdo: texto sin cuadro ── */
.tau-banner-card{background:transparent!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;border:none!important;box-shadow:none!important;border-radius:0!important;padding:0!important;max-width:460px!important;}
.tau-banner-pretitle{font-size:.72rem!important;font-weight:700!important;letter-spacing:.18em!important;color:#e87a84!important;text-transform:uppercase!important;margin-bottom:10px!important;display:block!important;}
.tau-banner-title{font-size:2.3rem!important;font-weight:800!important;color:#fff!important;line-height:1.12!important;margin:0 0 8px!important;letter-spacing:-.04em!important;}
.tau-accent-text{color:#ffc5cb!important;background:linear-gradient(90deg,#fff 0%,#ffc5cb 100%)!important;-webkit-background-clip:text!important;-webkit-text-fill-color:transparent!important;background-clip:text!important;}
.tau-banner-subtitle{font-size:.78rem!important;font-weight:700!important;color:#fff!important;background:linear-gradient(135deg,#c62b3a 0%,#a32230 100%)!important;padding:3px 11px!important;border-radius:20px!important;display:inline-block!important;margin-bottom:14px!important;letter-spacing:.08em!important;}
.tau-banner-desc{font-size:.88rem!important;color:rgba(255,255,255,.85)!important;line-height:1.6!important;margin-bottom:22px!important;}
.btn-tau-banner-explore{background:linear-gradient(135deg,#c62b3a 0%,#8e1f2d 100%)!important;border:none!important;color:#fff!important;border-radius:12px!important;padding:10px 26px!important;font-weight:700!important;font-size:.88rem!important;box-shadow:0 8px 22px rgba(198,43,58,.35)!important;text-decoration:none!important;display:inline-flex!important;align-items:center!important;}

/* ── Lado derecho: logo TAU + lema ── */
.tau-logo-deco{display:flex!important;flex-direction:column!important;align-items:center!important;gap:0!important;flex-shrink:0!important;}

.tau-deco-logo{width:clamp(120px,13vw,170px)!important;height:auto!important;display:block!important;filter:drop-shadow(0 6px 24px rgba(0,0,0,.6))!important;margin-bottom:16px!important;}

.tau-deco-lema{text-align:center!important;padding:14px 18px!important;background:rgba(10,2,5,.78)!important;border:1px solid rgba(198,43,58,.3)!important;border-top:2px solid #c62b3a!important;border-radius:0 0 14px 14px!important;width:clamp(150px,15vw,200px)!important;box-sizing:border-box!important;}
.tau-deco-lema-text{display:block!important;font-family:Georgia,"Times New Roman",serif!important;font-style:italic!important;font-size:clamp(.78rem,.9vw,.9rem)!important;color:rgba(255,255,255,.95)!important;line-height:1.55!important;margin-bottom:8px!important;}
.tau-deco-lema-attr{display:block!important;font-family:system-ui,sans-serif!important;font-style:normal!important;font-size:.6rem!important;color:rgba(255,255,255,.45)!important;letter-spacing:.1em!important;text-transform:uppercase!important;border-top:1px solid rgba(255,255,255,.1)!important;padding-top:7px!important;margin-top:0!important;}
';

$js = '
<script>(function(){
function buildHero(){
  var item=document.querySelector("#mooveslideshow .carousel-item");
  if(!item||item.querySelector(".tau-banner-overlay"))return;
  var overlay=document.createElement("div");
  overlay.className="tau-banner-overlay";
  overlay.innerHTML=
    \'<div class="tau-banner-card">\'+
      \'<span class="tau-banner-pretitle">Universidad CESMAG<\/span>\'+
      \'<h1 class="tau-banner-title">TAU <span class="tau-accent-text">Campus Virtual<\/span><\/h1>\'+
      \'<span class="tau-banner-subtitle">UNICESMAG<\/span>\'+
      \'<p class="tau-banner-desc">Tu plataforma de educación y aprendizaje en línea de vanguardia, diseñada para conectar tu talento con el futuro profesional.<\/p>\'+
      \'<a href="#apoyo-academico" class="btn-tau-banner-explore">Explorar Cursos<\/a>\'+
    \'<\/div>\'+
    \'<div class="tau-logo-deco">\'+
      \'<img src="\/theme\/tau_branding\/assets\/official\/tau-official-icon.png" class="tau-deco-logo" alt="TAU">\'+
      \'<div class="tau-deco-lema">\'+
        \'<span class="tau-deco-lema-text">&ldquo;Hombres nuevos<br>para tiempos nuevos&rdquo;<\/span>\'+
        \'<span class="tau-deco-lema-attr">&mdash;&thinsp;Fray Guillermo de Castellana,&nbsp;OFMCap.<\/span>\'+
      \'<\/div>\'+
    \'<\/div>\';
  item.appendChild(overlay);
  var btn=overlay.querySelector(".btn-tau-banner-explore");
  if(btn)btn.addEventListener("click",function(e){e.preventDefault();var d=document.getElementById("apoyo-academico");if(d)d.scrollIntoView({behavior:"smooth",block:"start"});});
}
if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",function(){setTimeout(buildHero,60);});}
else{setTimeout(buildHero,60);}
})();<\/script>';

$footer = '<style>' . $css . '</style>' . $js;
set_config('additionalhtmlfooter', $footer);
echo "[ok] additionalhtmlfooter: " . strlen($footer) . " bytes\n";

// Purgar caches
purge_all_caches();
echo "[ok] Caches purgadas\n";
echo "Verif tau-deco-lema-text: " . (strpos($footer,'tau-deco-lema-text')!==false?'SI':'NO') . "\n";
echo "Recarga con Ctrl+Shift+R\n";
