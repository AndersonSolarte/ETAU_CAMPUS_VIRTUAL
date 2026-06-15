<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Inyectar script de diagnóstico que muestra en pantalla qué existe
$diag = '
<script id="tau-diag">
(function(){
    function show(msg){
        var d=document.getElementById("tau-diag-box");
        if(!d){
            d=document.createElement("div");
            d.id="tau-diag-box";
            d.style.cssText="position:fixed;top:120px;left:50%;transform:translateX(-50%);background:#1a0306;color:#fff;border:2px solid #c62b3a;border-radius:10px;padding:16px 24px;z-index:99999;font-family:monospace;font-size:13px;max-width:90vw;text-align:left;";
            document.body.appendChild(d);
        }
        d.innerHTML+=msg+"<br>";
    }
    window.addEventListener("load",function(){
        // Buscar el slideshow
        var ids=["mooveslideshow","mooveheroimage","moove-slideshow","heroimage","page-header","moodle-page-header-wrapper"];
        ids.forEach(function(id){
            var el=document.getElementById(id);
            show((el?"✅ ":"❌ ")+"#"+id);
        });
        // Buscar por clase
        var cls=["header-slideshow","moove-header","slick-slider","carousel","carousel-inner"];
        cls.forEach(function(c){
            var el=document.querySelector("."+c);
            show((el?"✅ ":".❌ ")+"."+c+(el?" → parent:"+el.parentElement.id:""));
        });
        // Mostrar parent de #page-header
        var ph=document.getElementById("page-header");
        if(ph) show("page-header.parentId="+ph.parentElement.id);
        setTimeout(function(){
            var d=document.getElementById("tau-diag-box");
            if(d)d.remove();
        },15000);
    });
})();
</script>';

// Limpiar diagnóstico anterior y footer scripts tau
$footer = get_config('theme_moove', 'additionalhtmlfooter');
$footer = preg_replace('/<script id="tau-diag">.*?<\/script>/s', '', $footer);
set_config('additionalhtmlfooter', trim($footer)."\n".$diag, 'theme_moove');
theme_reset_all_caches();
echo "Script diagnóstico inyectado — abre localhost:8080 y verás un cuadro rojo con los IDs reales\n";
