<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// 1. Buscar archivos compilados en disco
$dirs = [
    $CFG->dataroot . '/localcache/theme',
    $CFG->dataroot . '/cache/theme',
    $CFG->localcachedir . '/theme',
];

echo "=== ARCHIVOS CSS COMPILADOS EN DISCO ===\n";
$found_files = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) { echo "No existe: $dir\n"; continue; }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile() && (strpos($f->getFilename(),'.css')!==false || strpos($f->getFilename(),'scss')!==false)) {
            $found_files[] = $f->getRealPath();
        }
    }
}
echo "Total CSS en disco: " . count($found_files) . "\n";

foreach ($found_files as $fp) {
    $content = file_get_contents($fp);
    if (strpos($content, 'tau-banner-card') !== false) {
        echo "\n[ENCONTRADO] $fp\n";
        // Extract context around tau-banner-card
        preg_match_all('/.{0,30}tau-banner-card.{0,200}/s', $content, $m);
        foreach ($m[0] as $hit) {
            echo "  " . substr(trim(str_replace("\n"," ",$hit)),0,300) . "\n";
        }
    }
}

// 2. Ver el SCSS en DB
$scss = get_config('theme_moove', 'scss');
echo "\n=== SCSS EN DB - todos los bloques .tau-banner-card ===\n";
preg_match_all('/.{0,50}\.tau-banner-card[^}]{0,400}/s', $scss, $m2);
foreach ($m2[0] as $hit) {
    echo trim(str_replace(["\n","    "]," ",$hit)) . "\n---\n";
}

// 3. ¿Dónde está border-radius:24px?
echo "\n=== border-radius:24px en SCSS ===\n";
preg_match_all('/.{0,80}border-radius: 24px.{0,80}/s', $scss, $m3);
foreach ($m3[0] as $hit) {
    echo "  " . trim(str_replace("\n"," ",$hit)) . "\n";
}
