<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

// Purgar caches de JS/tema
js_reset_all_caches();
purge_all_caches();
echo "JS y caches purgados\n";

// Verificar que el archivo fue copiado correctamente
$file = '/var/www/html/local/tau_course_creator_ai/assets/js/tau_frontpage.js';
$content = file_get_contents($file);
echo "tau_frontpage.js: " . strlen($content) . " bytes\n";
echo "Tiene 'Universidad CESMAG': " . (strpos($content,'Universidad CESMAG')!==false?'SI':'NO') . "\n";
echo "Tiene 'tau-deco-lema': " . (strpos($content,'tau-deco-lema')!==false?'SI':'NO') . "\n";
echo "Tiene 'tau-official-icon': " . (strpos($content,'tau-official-icon')!==false?'SI':'NO') . "\n";
echo "Tiene 'Conectando saberes': " . (strpos($content,'Conectando saberes')!==false?'SI':'NO') . "\n";
