<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// 1. Borrar archivos CSS compilados del tema en disco
$dirs = [
    $CFG->dataroot . '/cache/theme',
    $CFG->dataroot . '/localcache/theme',
    $CFG->localcachedir . '/theme',
];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        $count = 0;
        foreach ($files as $f) {
            if ($f->isFile()) { unlink($f->getRealPath()); $count++; }
            elseif ($f->isDir()) { rmdir($f->getRealPath()); }
        }
        echo "Borrados $count archivos en $dir\n";
    } else {
        echo "No existe: $dir\n";
    }
}

// 2. Verificar que el SCSS tiene los cambios
$scss = get_config('theme_moove', 'scss');
echo "\nVerificación SCSS:\n";
echo "rgba(15, 2, 5, 0.5) existe: " . (strpos($scss,'rgba(15, 2, 5, 0.5)')!==false?'SI':'NO') . "\n";
echo "border-left: 5px solid #c62b3a existe: " . (strpos($scss,'border-left: 5px solid #c62b3a')!==false?'SI':'NO') . "\n";
echo "border-radius: 24px existe: " . (strpos($scss,'border-radius: 24px')!==false?'SI':'NO') . "\n";

// Si aún quedan, hacer str_replace de nuevo
if (strpos($scss,'rgba(15, 2, 5, 0.5)') !== false) {
    $scss = str_replace('rgba(15, 2, 5, 0.5)', 'transparent', $scss);
    echo "Reemplazado rgba\n";
}
if (strpos($scss,'border-left: 5px solid #c62b3a') !== false) {
    $scss = str_replace('border-left: 5px solid #c62b3a', 'border-left: none', $scss);
    echo "Reemplazado border-left\n";
}
if (strpos($scss,'border-radius: 24px') !== false) {
    $scss = str_replace('border-radius: 24px', 'border-radius: 0', $scss);
    echo "Reemplazado border-radius\n";
}
// Quitar backdrop y box-shadow del card
$scss = str_replace('backdrop-filter: blur(20px)', 'backdrop-filter: none', $scss);
$scss = str_replace('-webkit-backdrop-filter: blur(20px)', '-webkit-backdrop-filter: none', $scss);
$scss = str_replace('box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45)', 'box-shadow: none', $scss);
$scss = str_replace('border: 1px solid rgba(255, 255, 255, 0.12)', 'border: none', $scss);

set_config('scss', $scss, 'theme_moove');

// 3. Forzar recompilación
theme_reset_all_caches();
purge_all_caches();

echo "\nCaché completamente eliminada y SCSS actualizado\n";
echo "INSTRUCCIÓN: Ahora recarga con Ctrl+Shift+R en el navegador\n";
