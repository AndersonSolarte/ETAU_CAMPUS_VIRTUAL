<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

// Disable guest login and self-registration
set_config('guestloginbutton', 0);
set_config('registerauth', '');
set_config('authloginviaemail', 0);

// Force redirect to OAuth on login page (Moodle 4+/5)
// When only one OAuth provider exists, Moodle can auto-redirect
set_config('loginpageautofocus', 0);

purge_caches();
echo "Settings applied. Manual form hidden via CSS (SCSS), OAuth is the only login." . PHP_EOL;
