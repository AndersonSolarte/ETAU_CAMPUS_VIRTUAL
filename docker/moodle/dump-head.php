<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
echo get_config('core', 'additionalhtmlhead');
