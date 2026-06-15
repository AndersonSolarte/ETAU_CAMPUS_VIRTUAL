<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Imagen del slider guardada en Moodle files
$files = $DB->get_records_sql("SELECT id,filename,filepath,filesize FROM {files} WHERE component='theme_moove' AND filearea LIKE '%slider%' AND filesize>0");
echo "=== Slider images en Moodle DB ===\n";
foreach($files as $f) echo "  {$f->filepath}{$f->filename} ({$f->filesize} bytes)\n";

// config del slider
echo "\nsliderimage1: " . get_config('theme_moove','sliderimage1') . "\n";
echo "slidercount: " . get_config('theme_moove','slidercount') . "\n";

// Revisar el CSS compilado - mostrar TODAS las reglas tau-banner-card en orden
$dirs = ['/var/www/html/localcache/theme'];
foreach($dirs as $dir){
    if(!is_dir($dir))continue;
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS));
    foreach($it as $f){
        if(!$f->isFile())continue;
        $c=file_get_contents($f->getRealPath());
        if(strpos($c,'tau-banner-card')===false)continue;
        echo "\n=== " . $f->getRealPath() . " ===\n";
        // Mostrar solo la parte FINAL del CSS (últimas 2000 chars)
        echo "...FINAL:\n" . substr($c,-2000) . "\n";
    }
}
