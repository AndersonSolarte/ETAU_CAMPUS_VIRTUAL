<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
set_config('debug', 0);
set_config('debugdisplay', 0);
purge_caches();
echo "Debug mode OFF." . PHP_EOL;
