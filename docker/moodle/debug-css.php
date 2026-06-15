<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// 1. Ver exactamente qué dice el CSS COMPILADO sobre tau-banner-card
$dirs = ['/var/www/html/localcache/theme', '/var/www/moodledata/localcache/theme'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) { echo "No existe: $dir\n"; continue; }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile()) {
            $c = file_get_contents($f->getRealPath());
            if (strpos($c,'tau-banner-card')!==false) {
                echo "ARCHIVO: " . $f->getRealPath() . "\n";
                preg_match_all('/.{0,20}\.tau-banner-card\{[^}]{0,300}/s', $c, $m);
                foreach($m[0] as $h) echo "  CSS: " . trim(str_replace("\n"," ",$h)) . "\n";
            }
        }
    }
}

// 2. Ver sliderimage1 (el escudo)
echo "\nsliderimage1: " . get_config('theme_moove','sliderimage1') . "\n";

// 3. Ver si la imagen TAU icon existe como plugin file
$files = $DB->get_records_sql("SELECT filename, filepath FROM {files} WHERE component='theme_moove' AND filearea='sliderimage' LIMIT 5");
foreach($files as $f) echo "Slider file: {$f->filepath}{$f->filename}\n";

// 4. Confirmar FIX-HERO-FINAL en SCSS DB
$scss = get_config('theme_moove','scss');
echo "\nFIX-HERO-FINAL en SCSS: " . (strpos($scss,'FIX-HERO-FINAL')!==false?'SI':'NO') . "\n";
echo "display:none imagen slider en SCSS: " . (strpos($scss,'carousel-item > img')!==false?'SI':'NO') . "\n";

// Mostrar las últimas 500 chars del SCSS para ver el bloque final
echo "\n=== Últimas 600 chars del SCSS ===\n";
echo substr($scss, -600) . "\n";
