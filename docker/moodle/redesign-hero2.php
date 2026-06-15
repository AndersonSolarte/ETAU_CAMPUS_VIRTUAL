<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$css = '
/* ── Hero overlay ── */
.tau-banner-overlay{position:absolute!important;top:0!important;left:0!important;width:100%!important;height:100%!important;display:flex!important;align-items:center!important;justify-content:space-between!important;padding:0 6% 0 8%!important;z-index:10!important;box-sizing:border-box!important;}

/* ── Texto izquierdo sin cuadro ── */
.tau-banner-card{background:transparent!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;border:none!important;box-shadow:none!important;border-radius:0!important;padding:0!important;max-width:440px!important;}
.tau-banner-pretitle{font-size:.7rem!important;font-weight:700!important;letter-spacing:.18em!important;color:#e87a84!important;text-transform:uppercase!important;margin-bottom:10px!important;display:block!important;}
.tau-banner-title{font-size:2.3rem!important;font-weight:800!important;color:#fff!important;line-height:1.1!important;margin:0 0 8px!important;letter-spacing:-.04em!important;}
.tau-accent-text{color:#ffc5cb!important;background:linear-gradient(90deg,#fff 0%,#ffc5cb 100%)!important;-webkit-background-clip:text!important;-webkit-text-fill-color:transparent!important;background-clip:text!important;}
.tau-banner-subtitle{font-size:.75rem!important;font-weight:700!important;color:#fff!important;background:linear-gradient(135deg,#c62b3a 0%,#a32230 100%)!important;padding:3px 12px!important;border-radius:20px!important;display:inline-block!important;margin-bottom:14px!important;letter-spacing:.08em!important;}
.tau-banner-desc{font-size:.87rem!important;color:rgba(255,255,255,.82)!important;line-height:1.62!important;margin-bottom:22px!important;}
.btn-tau-banner-explore{background:linear-gradient(135deg,#c62b3a 0%,#8e1f2d 100%)!important;border:none!important;color:#fff!important;border-radius:12px!important;padding:11px 28px!important;font-weight:700!important;font-size:.88rem!important;box-shadow:0 8px 22px rgba(198,43,58,.35)!important;text-decoration:none!important;display:inline-flex!important;align-items:center!important;}

/* ── Lado derecho: logo libre + inscripción sin cuadro ── */
.tau-logo-deco{display:flex!important;flex-direction:column!important;align-items:center!important;gap:0!important;flex-shrink:0!important;background:transparent!important;}
.tau-deco-logo{width:clamp(150px,16vw,200px)!important;height:auto!important;display:block!important;filter:drop-shadow(0 8px 32px rgba(0,0,0,.55))!important;margin-bottom:14px!important;}
/* Inscripción tipo mural — sin caja */
.tau-ins-wrap{text-align:center!important;background:transparent!important;border:none!important;padding:0!important;}
.tau-ins-line{display:block!important;width:36px!important;height:2px!important;background:#c62b3a!important;margin:0 auto 12px!important;border-radius:1px!important;}
.tau-ins-text{font-family:Georgia,"Times New Roman",serif!important;font-style:italic!important;font-size:clamp(.9rem,1.05vw,1.05rem)!important;color:rgba(255,255,255,.95)!important;line-height:1.55!important;margin:0 0 8px!important;display:block!important;}
.tau-ins-attr{font-family:system-ui,sans-serif!important;font-style:normal!important;font-size:.6rem!important;color:rgba(255,255,255,.38)!important;letter-spacing:.12em!important;text-transform:uppercase!important;display:block!important;}
';

set_config('additionalhtmlfooter', '<style>' . $css . '</style>');
echo "[ok] CSS actualizado: " . strlen($css) . " chars\n";

purge_all_caches();
echo "[ok] Caches purgadas\n";
