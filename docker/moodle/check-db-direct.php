<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

global $DB;

// Query directa a la tabla config
$r = $DB->get_record('config', ['name'=>'additionalhtmlfooter']);
if ($r) {
    $len = strlen($r->value);
    echo "additionalhtmlfooter: $len bytes\n";
    echo "tau-banner en footer: " . (strpos($r->value,'tau-banner')!==false?'SI':'NO') . "\n";
    if (strpos($r->value,'tau-banner')!==false) {
        $pos = strpos($r->value,'tau-banner');
        echo substr($r->value, max(0,$pos-50), 400) . "\n";
    }
} else {
    echo "additionalhtmlfooter: NO EXISTE en config table\n";
}

// También buscar en theme_moove config_plugins
$r2 = $DB->get_record('config_plugins', ['plugin'=>'theme_moove','name'=>'additionalhtmlfooter']);
if ($r2) {
    $len = strlen($r2->value);
    echo "\ntheme_moove additionalhtmlfooter: $len bytes\n";
    echo "tau-banner: " . (strpos($r2->value,'tau-banner')!==false?'SI':'NO') . "\n";
} else {
    echo "\ntheme_moove additionalhtmlfooter: NO EXISTE\n";
}

// Buscar en TODOS los config_plugins que tengan 'tau-banner'
echo "\n=== Buscar tau-banner en TODAS las tablas config ===\n";
$all = $DB->get_records_sql("SELECT plugin, name, LEFT(value,100) as val FROM {config_plugins} WHERE " . $DB->sql_like('value','?',false,false), ['%tau-banner%']);
foreach ($all as $item) {
    echo "Plugin: {$item->plugin} / {$item->name}: " . substr($item->val,0,100) . "\n";
}

$r3 = $DB->get_record_sql("SELECT LEFT(value,200) as val FROM {config} WHERE " . $DB->sql_like('value','?',false,false) . " LIMIT 1", ['%tau-banner%']);
if ($r3) echo "Core config con tau-banner: " . $r3->val . "\n";
else echo "Core config: no hay tau-banner\n";
