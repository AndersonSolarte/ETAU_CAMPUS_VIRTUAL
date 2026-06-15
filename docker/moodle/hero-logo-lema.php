<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// ── 1. SCSS: quitar ::after y ::before del slideshow, poner estilos del wrapper ──
$scss = get_config('theme_moove', 'scss');
// Quitar todos los bloques TAU-FINAL anteriores
foreach (['/* TAU-FINAL', '/* TAU-LOGO-HERO'] as $m) {
    $p = strpos($scss, $m);
    if ($p !== false) $scss = rtrim(substr($scss, 0, $p));
}

$scss .= '

/* TAU-HERO-V2 ══════════════════════════════════════════════ */
/* Quitar pseudo-elementos anteriores */
#mooveslideshow::after  { content: none !important; display: none !important; }
#mooveslideshow::before { content: none !important; display: none !important; }

#mooveslideshow { position: relative !important; overflow: visible !important; }
#mooveslideshow .carousel-inner { overflow: hidden !important; }

/* Wrapper logo + lema dentro del hero */
.tau-logo-area {
    position: absolute !important;
    right: 5% !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 14px !important;
    z-index: 10 !important;
    pointer-events: none !important;
    width: clamp(200px,27%,280px) !important;
    animation: tau-area-in .9s cubic-bezier(.22,1,.36,1) both !important;
}
@keyframes tau-area-in {
    from { opacity:0; transform:translateY(-47%) scale(.88); }
    to   { opacity:1; transform:translateY(-50%) scale(1); }
}
.tau-logo-area img {
    width: 100% !important;
    height: auto !important;
    display: block !important;
    filter: drop-shadow(0 8px 36px rgba(0,0,0,.60)) !important;
}
/* Lema debajo del logo */
.tau-hero-lema {
    text-align: center !important;
    padding: 10px 14px !important;
    background: rgba(26,3,6,.72) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(198,43,58,.40) !important;
    border-top: 2px solid #c62b3a !important;
    border-radius: 12px !important;
    width: 100% !important;
}
.tau-hero-lema-q {
    font-family: Georgia, "Times New Roman", serif !important;
    font-size: clamp(.72rem,.90vw,.84rem) !important;
    font-style: italic !important;
    color: rgba(255,255,255,.92) !important;
    margin: 0 0 5px !important;
    line-height: 1.4 !important;
    text-shadow: 0 1px 6px rgba(0,0,0,.5) !important;
}
.tau-hero-lema-q strong {
    color: #fff !important; font-style: normal !important;
    font-weight: 700 !important; text-transform: uppercase !important;
}
.tau-hero-lema-autor {
    font-size: clamp(.60rem,.75vw,.68rem) !important;
    color: rgba(255,255,255,.48) !important;
    letter-spacing: .08em !important;
    text-transform: uppercase !important;
    margin: 0 !important; font-family: system-ui, sans-serif !important;
}

@media(max-width:900px){
    .tau-logo-area { right:1% !important; width:clamp(140px,18vw,180px) !important; }
    .tau-hero-lema { padding: 7px 10px !important; }
}
@media(max-width:768px){ .tau-logo-area { display:none !important; } }
/* TAU-HERO-V2-FIN ══════════════════════════════════════════ */
';

set_config('scss', $scss, 'theme_moove');

// ── 2. Limpiar additionalhtmltopofbody (ya no se necesita) ───────────────────
set_config('additionalhtmltopofbody', '', 'theme_moove');

// ── 3. Limpiar footer de versiones anteriores ─────────────────────────────────
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$kw = ['tau-v9','tau-v10','tau-v11','tau-v12','tau-v13','tau-v14','tau-v15','tau-v16','tau-v17'];
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

// ── 4. JS: inyectar wrapper logo+lema dentro del hero ────────────────────────
$footer .= '
<script id="tau-v17-script">
(function(){
    function go(){
        var ss = document.getElementById("mooveslideshow");
        if (!ss) { setTimeout(go, 300); return; }
        if (document.querySelector(".tau-logo-area")) return;

        var area = document.createElement("div");
        area.className = "tau-logo-area";
        area.innerHTML =
            "<img src=\"/theme/tau_branding/assets/official/tau-official-icon.png\" alt=\"TAU Campus Virtual\">" +
            "<div class=\"tau-hero-lema\">" +
                "<p class=\"tau-hero-lema-q\">" +
                    "&ldquo;<strong>Hombres nuevos</strong><br>para <strong>tiempos nuevos</strong>&rdquo;" +
                "</p>" +
                "<p class=\"tau-hero-lema-autor\">&mdash; Fray Guillermo de Castellana, OFMCap.</p>" +
            "</div>";

        ss.appendChild(area);
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", go);
    else go();
    setTimeout(go, 500);
})();
</script>';

set_config('additionalhtmlfooter', trim($footer), 'theme_moove');

theme_reset_all_caches();
echo "Hero logo+lema aplicado\n";
echo "scss: " . strlen(get_config('theme_moove','scss')) . " bytes\n";
echo "footer: " . strlen(get_config('theme_moove','additionalhtmlfooter')) . " bytes\n";
