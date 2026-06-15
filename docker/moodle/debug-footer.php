<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');
$footer = get_config('theme_moove', 'additionalhtmlfooter');
echo 'Footer bytes: ' . strlen($footer) . "\n";
echo 'tau-v12-script: ' . (strpos($footer,'tau-v12-script')!==false?'SI':'NO') . "\n";
echo 'tau-lema class: ' . (strpos($footer,'tau-lema')!==false?'SI':'NO') . "\n";
echo 'mooveslideshow ref: ' . (strpos($footer,'mooveslideshow')!==false?'SI':'NO') . "\n";
// Mostrar todo el script
$s = strpos($footer, '<script id="tau-v12-script">');
$e = strpos($footer, '</script>', $s);
if ($s !== false) {
    echo "\n--- SCRIPT COMPLETO ---\n";
    echo substr($footer, $s, $e - $s + 9) . "\n";
} else {
    echo "Script tau-v12 NO encontrado\n";
    // Listar todos los tags script/style
    preg_match_all('/<(script|style)[^>]*id="([^"]+)"/', $footer, $m);
    echo "IDs encontrados: " . implode(', ', $m[2]) . "\n";
}
