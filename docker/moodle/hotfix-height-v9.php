<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');

// Agregar min-height al carousel-item para que no colapse
$patch = '

/* TAU-HOTFIX: min-height para evitar colapso del hero */
#mooveslideshow .carousel-item {
    min-height: clamp(320px, 48vh, 520px) !important;
    display: flex !important;
    align-items: center !important;
}
#mooveslideshow .carousel-caption {
    position: relative !important;
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    padding-bottom: 0 !important;
    max-width: 54% !important;
}
';

// Solo agregar si no está ya
if (strpos($scss, 'TAU-HOTFIX') === false) {
    set_config('scss', $scss . $patch, 'theme_moove');
    echo "Hotfix aplicado\n";
} else {
    echo "Hotfix ya existe\n";
}

theme_reset_all_caches();
echo "Cache purgada\n";
echo "SCSS: " . strlen(get_config('theme_moove', 'scss')) . " bytes\n";
