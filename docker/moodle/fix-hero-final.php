<?php
/**
 * fix-hero-final.php
 * Inserta el JS del hero overlay en additionalhtmlfooter
 * y asegura que el CSS no tenga cuadro oscuro en .tau-banner-card
 */
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── 1. JavaScript del hero (transparent card + TAU logo + lema) ──────────────
$heroJs = '<script>(function(){
function buildHero(){
  var item=document.querySelector("#mooveslideshow .carousel-item");
  if(!item||item.querySelector(".tau-banner-overlay"))return;
  var overlay=document.createElement("div");
  overlay.className="tau-banner-overlay";
  overlay.innerHTML=
    \'<div class="tau-banner-card">\'+
      \'<div class="tau-banner-pretitle tau-banner-animate">Universidad CESMAG<\/div>\'+
      \'<h1 class="tau-banner-title tau-banner-animate">TAU <span class="tau-accent-text">Campus Virtual<\/span><\/h1>\'+
      \'<div class="tau-banner-subtitle tau-banner-animate">UNICESMAG<\/div>\'+
      \'<p class="tau-banner-desc tau-banner-animate">Tu plataforma de educación y aprendizaje en línea de vanguardia, diseñada para conectar tu talento con el futuro profesional.<\/p>\'+
      \'<div class="tau-banner-btn-wrap tau-banner-animate">\'+
        \'<a href="#apoyo-academico" class="btn btn-tau-banner-explore">Explorar Cursos<\/a>\'+
      \'<\/div>\'+
    \'<\/div>\'+
    \'<div class="tau-banner-deco tau-logo-deco">\'+
      \'<img src="\/theme\/tau_branding\/assets\/official\/tau-official-icon.png" class="tau-deco-logo" alt="TAU">\'+
      \'<div class="tau-deco-lema">\'+
        \'“Hombres nuevos<br>para tiempos nuevos”\'+
        \'<span>— Fray Guillermo de Castellana, OFMCap.<\/span>\'+
      \'<\/div>\'+
    \'<\/div>\';
  item.appendChild(overlay);
  var btn=overlay.querySelector(".btn-tau-banner-explore");
  if(btn){btn.addEventListener("click",function(e){e.preventDefault();var d=document.getElementById("apoyo-academico");if(d)d.scrollIntoView({behavior:"smooth",block:"start"});});}
}
if(document.readyState==="loading"){
  document.addEventListener("DOMContentLoaded",function(){setTimeout(buildHero,80);});
}else{
  setTimeout(buildHero,80);
}
})();<\/script>';

// ── 2. CSS extra para garantizar card transparente + logo/lema ───────────────
$heroCss = '<style>
.tau-banner-overlay{position:absolute!important;top:0!important;left:0!important;width:100%!important;height:100%!important;display:flex!important;align-items:center!important;justify-content:space-between!important;padding:0 8%!important;z-index:10!important;pointer-events:auto!important}
.tau-banner-card{background:transparent!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;border:none!important;box-shadow:none!important;border-radius:0!important;padding:30px 36px!important;max-width:480px!important}
.tau-logo-deco{display:flex!important;flex-direction:column!important;align-items:center!important;justify-content:center!important;gap:14px!important}
.tau-deco-logo{width:clamp(140px,16%,200px)!important;height:auto!important;display:block!important;filter:drop-shadow(0 8px 36px rgba(0,0,0,.65))!important;opacity:1!important;visibility:visible!important}
.tau-deco-lema{text-align:center!important;padding:11px 16px!important;background:rgba(15,2,5,.82)!important;border:1px solid rgba(198,43,58,.40)!important;border-top:2px solid #c62b3a!important;border-radius:12px!important;font-family:Georgia,serif!important;font-size:clamp(.74rem,.88vw,.85rem)!important;font-style:italic!important;color:rgba(255,255,255,.93)!important;line-height:1.5!important;width:clamp(140px,16%,210px)!important;display:block!important;visibility:visible!important;opacity:1!important}
.tau-deco-lema span{display:block!important;font-family:system-ui,sans-serif!important;font-style:normal!important;font-size:.62rem!important;color:rgba(255,255,255,.55)!important;letter-spacing:.08em!important;text-transform:uppercase!important;margin-top:6px!important}
</style>';

$footer = $heroCss . $heroJs;

// Guardar en core additionalhtmlfooter
set_config('additionalhtmlfooter', $footer);
echo "[ok] additionalhtmlfooter: " . strlen($footer) . " bytes\n";

// ── 3. Asegurar CSS transparente en SCSS ──────────────────────────────────────
$scss = get_config('theme_moove', 'scss');

// Eliminar bloques FORCE-HERO previos para no duplicar
foreach (['/* FORCE-HERO','/* TAU-HERO','/* TAU-FINAL','/* TAU-LOGO'] as $marker) {
    $pos = strpos($scss, $marker);
    if ($pos !== false) $scss = rtrim(substr($scss, 0, $pos));
}

set_config('scss', $scss, 'theme_moove');
echo "[ok] SCSS limpiado: " . strlen($scss) . " bytes\n";

// ── 4. Borrar cache compilada en disco ────────────────────────────────────────
global $CFG;
$dirs = [
    $CFG->dataroot . '/localcache/theme',
    $CFG->localcachedir . '/theme',
    '/var/www/html/localcache/theme',
];
$total = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) { echo "No existe: $dir\n"; continue; }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $n = 0;
    foreach ($it as $f) {
        if ($f->isFile()) { unlink($f->getRealPath()); $n++; }
        elseif ($f->isDir()) { @rmdir($f->getRealPath()); }
    }
    echo "Borrados $n archivos en $dir\n";
    $total += $n;
}
echo "[ok] Total cache borrada: $total archivos\n";

// ── 5. Purgar todo ────────────────────────────────────────────────────────────
theme_reset_all_caches();
purge_all_caches();
echo "[ok] Caches de Moodle purgadas\n";

// ── Verificación final ────────────────────────────────────────────────────────
$check = get_config(false, 'additionalhtmlfooter');
echo "\nVerificacion footer: " . strlen($check) . " bytes\n";
echo "tau-banner-card en footer: " . (strpos($check,'tau-banner-card')!==false?'SI':'NO') . "\n";
echo "tau-deco-lema en footer: " . (strpos($check,'tau-deco-lema')!==false?'SI':'NO') . "\n";
echo "\nListo. Recarga con Ctrl+Shift+R\n";
