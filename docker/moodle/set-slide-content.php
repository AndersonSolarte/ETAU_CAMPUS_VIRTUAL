<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── Limpiar todos los scripts tau del footer ──────────────────────────────────
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12','tau-v13','tau-v14','tau-v15','tau-v16','tau-v17','tau-diag'];
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
set_config('additionalhtmlfooter', trim($footer), 'theme_moove');
set_config('additionalhtmltopofbody', '', 'theme_moove');

// ── Quitar CSS hero anterior del SCSS ────────────────────────────────────────
$scss = get_config('theme_moove', 'scss');
foreach (['/* TAU-HERO-V2', '/* TAU-FINAL', '/* TAU-LOGO-HERO'] as $m) {
    $p = strpos($scss, $m);
    if ($p !== false) $scss = rtrim(substr($scss, 0, $p));
}

// ── CSS mínimo para el hero ───────────────────────────────────────────────────
$scss .= '

/* TAU-HERO-SLIDE ════════════════════════════════════════════ */
/* El slide tiene dos columnas: texto izq (caption) + imagen der (img-slide) */
#mooveslideshow { position:relative !important; }
#mooveslideshow .carousel-inner { overflow:hidden !important; }

/* Contenedor derecho con logo + lema — dentro del caption del slide */
.tau-slide-right {
    position:absolute !important;
    right:5% !important; top:50% !important;
    transform:translateY(-50%) !important;
    display:flex !important;
    flex-direction:column !important;
    align-items:center !important;
    gap:12px !important;
    width:clamp(190px,25%,260px) !important;
    z-index:10 !important;
    pointer-events:none !important;
}
.tau-slide-right img {
    width:85% !important; height:auto !important;
    display:block !important; margin:0 auto !important;
    filter:drop-shadow(0 8px 36px rgba(0,0,0,.65)) !important;
    opacity:1 !important; visibility:visible !important;
}
.tau-slide-lema {
    width:100% !important; text-align:center !important;
    padding:10px 14px !important;
    background:rgba(20,3,6,.85) !important;
    border:1px solid rgba(198,43,58,.40) !important;
    border-top:2px solid #c62b3a !important;
    border-radius:12px !important;
    box-shadow:0 4px 20px rgba(0,0,0,.50) !important;
}
.tau-slide-lema p { margin:0 !important; }
.tau-slide-lema-q {
    font-family:Georgia,serif !important;
    font-size:clamp(.72rem,.85vw,.82rem) !important;
    font-style:italic !important;
    color:rgba(255,255,255,.93) !important;
    line-height:1.45 !important;
    margin-bottom:5px !important;
}
.tau-slide-lema-q b {
    font-style:normal !important; font-weight:700 !important;
    text-transform:uppercase !important; color:#fff !important;
}
.tau-slide-lema-autor {
    font-size:clamp(.58rem,.70vw,.65rem) !important;
    color:rgba(255,255,255,.45) !important;
    letter-spacing:.08em !important;
    text-transform:uppercase !important;
    font-family:system-ui,sans-serif !important;
}
@media(max-width:900px){.tau-slide-right{right:1% !important;width:clamp(140px,18vw,180px) !important;}}
@media(max-width:768px){.tau-slide-right{display:none !important;}}
/* TAU-HERO-SLIDE-FIN ════════════════════════════════════════ */
';
set_config('scss', $scss, 'theme_moove');

// ── Inyectar el HTML directamente en el texto del slider (slidertext1) ────────
// El tema Moove renderiza slidertext1 dentro del carousel-item como HTML libre
$logo_lema_html = '
<div class="tau-slide-right">
  <img src="/theme/tau_branding/assets/official/tau-official-icon.png" alt="TAU">
  <div class="tau-slide-lema">
    <p class="tau-slide-lema-q">&ldquo;<b>Hombres nuevos</b><br>para <b>tiempos nuevos</b>&rdquo;</p>
    <p class="tau-slide-lema-autor">&mdash; Fray Guillermo de Castellana, OFMCap.</p>
  </div>
</div>';

set_config('slidertext1', $logo_lema_html, 'theme_moove');

theme_reset_all_caches();
echo "Slide content actualizado\n";
echo "slidertext1: ".strlen(get_config('theme_moove','slidertext1'))." bytes\n";
echo "scss: ".strlen(get_config('theme_moove','scss'))." bytes\n";
echo "footer limpio: ".strlen(get_config('theme_moove','additionalhtmlfooter'))." bytes\n";
