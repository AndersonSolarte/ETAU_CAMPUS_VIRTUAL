<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Enable full debug mode
set_config('debug', 32767);       // DEBUG_ALL
set_config('debugdisplay', 1);
set_config('debugstringids', 0);
set_config('perfdebug', 7);

purge_caches();
echo "Debug mode ON. Visit localhost:8080 to see full error trace." . PHP_EOL;
echo "Run disable-debug.php when done." . PHP_EOL;
