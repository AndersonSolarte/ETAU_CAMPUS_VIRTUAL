<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

global $DB;

echo "=== Buscando 'Conectando saberes' en todas las configs ===\n";
$rows = $DB->get_records_sql(
    "SELECT plugin, name, LEFT(value,300) as val FROM {config_plugins} WHERE " . $DB->sql_like('value','?',false,false),
    ['%Conectando%']
);
foreach ($rows as $r) {
    echo "Plugin:{$r->plugin} / {$r->name}: " . substr($r->val,0,200) . "\n---\n";
}

$rows2 = $DB->get_records_sql(
    "SELECT name, LEFT(value,300) as val FROM {config} WHERE " . $DB->sql_like('value','?',false,false),
    ['%Conectando%']
);
foreach ($rows2 as $r) {
    echo "Core/{$r->name}: " . substr($r->val,0,200) . "\n---\n";
}

// Verificar additionalhtmlfooter actual
echo "\n=== additionalhtmlfooter actual ===\n";
$f = get_config(false,'additionalhtmlfooter');
echo "Bytes: " . strlen($f) . "\n";
echo "Contiene tau-banner-card: " . (strpos($f,'tau-banner-card')!==false?'SI':'NO') . "\n";
echo "Contiene E-TAU Campus Virtual: " . (strpos($f,'E-TAU Campus Virtual')!==false?'SI':'NO') . "\n";
echo "Contiene Universidad CESMAG: " . (strpos($f,'Universidad CESMAG')!==false?'SI':'NO') . "\n";
echo "\nPrimeros 300 chars:\n" . substr($f,0,300) . "\n";
