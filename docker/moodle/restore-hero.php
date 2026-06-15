<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$css = '
/* ═══ TAU HERO ═══ */
#mooveslideshow .carousel-item img { opacity:0!important; visibility:hidden!important; pointer-events:none!important; }
#mooveslideshow .carousel-caption { display:none!important; }

.tau-banner-overlay {
    position:absolute!important; top:0!important; left:0!important;
    width:100%!important; height:100%!important;
    display:flex!important; align-items:center!important;
    justify-content:space-between!important;
    padding:0 6% 0 8%!important; z-index:20!important;
    box-sizing:border-box!important;
}
.tau-banner-card {
    background:transparent!important; border:none!important;
    border-left:none!important; box-shadow:none!important;
    border-radius:0!important; padding:0!important;
    max-width:460px!important; backdrop-filter:none!important;
}
.tau-banner-pretitle {
    font-size:.72rem!important; font-weight:700!important;
    letter-spacing:.18em!important; color:#e87a84!important;
    text-transform:uppercase!important; margin-bottom:10px!important;
    display:block!important;
}
.tau-banner-title {
    font-size:2.3rem!important; font-weight:800!important;
    color:#fff!important; line-height:1.1!important;
    margin:0 0 8px!important; letter-spacing:-.04em!important;
}
.tau-accent-text {
    color:#ffc5cb!important;
    background:linear-gradient(90deg,#fff 0%,#ffc5cb 100%)!important;
    -webkit-background-clip:text!important;
    -webkit-text-fill-color:transparent!important;
    background-clip:text!important;
}
.tau-banner-subtitle {
    font-size:.75rem!important; font-weight:700!important;
    color:#fff!important;
    background:linear-gradient(135deg,#c62b3a 0%,#a32230 100%)!important;
    padding:3px 12px!important; border-radius:20px!important;
    display:inline-block!important; margin-bottom:14px!important;
    letter-spacing:.08em!important;
}
.tau-banner-desc {
    font-size:.87rem!important; color:rgba(255,255,255,.82)!important;
    line-height:1.62!important; margin-bottom:22px!important;
}
.btn-tau-banner-explore {
    background:linear-gradient(135deg,#c62b3a 0%,#8e1f2d 100%)!important;
    border:none!important; color:#fff!important;
    border-radius:12px!important; padding:11px 28px!important;
    font-weight:700!important; font-size:.88rem!important;
    box-shadow:0 8px 22px rgba(198,43,58,.35)!important;
    text-decoration:none!important; display:inline-flex!important;
    align-items:center!important; cursor:pointer!important;
}
.tau-logo-deco {
    display:flex!important; flex-direction:column!important;
    align-items:center!important; gap:0!important;
    flex-shrink:0!important; background:transparent!important;
}
.tau-deco-logo {
    width:clamp(140px,15vw,185px)!important; height:auto!important;
    display:block!important;
    filter:drop-shadow(0 8px 28px rgba(0,0,0,.6))!important;
    margin-bottom:14px!important;
}
.tau-ins-wrap {
    text-align:center!important; background:transparent!important;
    border:none!important; padding:0!important; max-width:200px!important;
}
.tau-ins-line {
    display:block!important; width:32px!important; height:2px!important;
    background:#c62b3a!important; margin:0 auto 10px!important;
    border-radius:1px!important;
}
.tau-ins-text {
    font-family:Georgia,"Times New Roman",serif!important;
    font-style:italic!important; font-size:.98rem!important;
    color:rgba(255,255,255,.95)!important; line-height:1.55!important;
    margin:0 0 7px!important; display:block!important;
}
.tau-ins-attr {
    font-family:system-ui,sans-serif!important; font-style:normal!important;
    font-size:.58rem!important; color:rgba(255,255,255,.38)!important;
    letter-spacing:.1em!important; text-transform:uppercase!important;
    display:block!important;
}
@media(max-width:767px){
    .tau-banner-overlay{ flex-direction:column!important; padding:24px!important; gap:20px!important; }
    .tau-logo-deco{ display:none!important; }
    .tau-banner-title{ font-size:1.6rem!important; }
}
';

$html =
'<div class="tau-banner-overlay" id="tau-hero-overlay">' .
  '<div class="tau-banner-card">' .
    '<span class="tau-banner-pretitle">Universidad CESMAG</span>' .
    '<h1 class="tau-banner-title">TAU <span class="tau-accent-text">Campus Virtual</span></h1>' .
    '<span class="tau-banner-subtitle">UNICESMAG</span>' .
    '<p class="tau-banner-desc">Tu plataforma de educación y aprendizaje en línea de vanguardia, diseñada para conectar tu talento con el futuro profesional.</p>' .
    '<a href="#apoyo-academico" class="btn-tau-banner-explore">Explorar Cursos</a>' .
  '</div>' .
  '<div class="tau-logo-deco">' .
    '<img src="/theme/tau_branding/assets/official/tau-official-icon.png" class="tau-deco-logo" alt="TAU">' .
    '<div class="tau-ins-wrap">' .
      '<span class="tau-ins-line"></span>' .
      '<p class="tau-ins-text">&ldquo;Hombres nuevos<br>para tiempos nuevos&rdquo;</p>' .
      '<cite class="tau-ins-attr">&mdash;&nbsp;Fray Guillermo de Castellana,&nbsp;OFMCap.</cite>' .
    '</div>' .
  '</div>' .
'</div>';

$js = '<script>(function(){
function buildHero(){
  if(document.getElementById("tau-hero-overlay")) return;
  var ci=document.querySelector("#mooveslideshow .carousel-item");
  if(!ci) return;
  var imgs=ci.querySelectorAll("img");
  for(var i=0;i<imgs.length;i++){
    imgs[i].style.cssText="opacity:0!important;visibility:hidden!important;pointer-events:none!important;";
  }
  ci.style.position="relative";
  ci.insertAdjacentHTML("beforeend","' . addslashes($html) . '");
}
if(document.readyState==="loading"){
  document.addEventListener("DOMContentLoaded",function(){setTimeout(buildHero,50);});
}else{ setTimeout(buildHero,50); }
})();</script>';

$footer = '<style>' . $css . '</style>' . $js;
set_config('additionalhtmlfooter', $footer);
echo "[ok] footer: ".strlen($footer)." bytes\n";

$dirs=['/var/www/html/localcache/theme','/var/www/moodledata/localcache/theme'];
foreach($dirs as $d){
    if(!is_dir($d)) continue;
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);
    foreach($it as $f){ if($f->isFile()) @unlink($f->getRealPath()); elseif($f->isDir()) @rmdir($f->getRealPath()); }
}
theme_reset_all_caches();
echo "[ok] cache cleared\n";
