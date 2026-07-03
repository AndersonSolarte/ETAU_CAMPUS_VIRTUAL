<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require('/var/www/html/config.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/adminlib.php');

$systemcontext = context_system::instance();
$filestorage = get_file_storage();
$brandingroot = $CFG->dirroot . '/theme/tau_branding';
$scsspath = $brandingroot . '/scss/custom.scss';

$manropefont = '<link rel="preconnect" href="https://fonts.googleapis.com">'
    . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
    . '<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">';

$footnote = <<<HTML
<div class="tau-footer">
  <span>E-TAU Campus Virtual</span>
  <span>Vigilada Mineducacion</span>
  <a href="mailto:tau-ayuda@unicesmag.edu.co">tau-ayuda@unicesmag.edu.co</a>
</div>
HTML;

$settings = [
    'brandcolor' => '#1f4b99',
    'buttonbrandcolor' => '#d9a441',
    'linkcolor' => '#1f4b99',

    'navbarcolor' => 'coloreddark',
    'navbartint' => '#0b1f3a',

    'loginformposition' => 'center',
    'loginformtransparency' => 'yes',
    'loginpagebrand' => 'logoheadingtagline',
    'loginbackgroundimageposition' => 'cover',
    'loginbackgroundimagetext' => '',
    'loginlocalloginenable' => '0',
    'loginidploginenable' => '1',
    'loginguestloginenable' => '0',
    'loginselfregistrationenable' => '0',
    'primarylogin' => 'idp',
    'loginidpshowintro' => '0',
    'loginidpbuttoncolor' => 'primaryfilled',

    'courselistingpresentation' => 'cards',
    'categorylistingpresentation' => 'boxlist',
    'coursecardscolumncount' => '3',
    'courseoverviewshowcourseprogress' => 'yes',

    'courseheaderenabled' => 'yes',
    'courseheaderlayout' => 'headingabove',
    'courseheadershowcategory' => 'yes',
    'courseheadershowprogress' => 'yes',
    'courseheadershowshortname' => 'yes',
    'courseheadercanvasbackground' => 'brandcolorgradientlight',
    'courseheadercanvasborder' => 'brandcolor',
    'courseheadertextonimagestyle' => 'lightshadow',

    'footnote' => $footnote,
    'additionalhtml_head' => $manropefont,

    'infobannerenabled' => 'yes',
    'infobannertext' => 'Bienvenido a E-TAU Campus Virtual. Acceso institucional para la comunidad CESMAG.',
    'infobannerbgcolor' => '#1f4b99',
    'infobannerorder' => 1,
    'infobannermode' => 'perpetual',

    'backtotopbuttonenable' => 'yes',
    'activityiconcolorinheritbrandcolor' => 'yes',
];

foreach ($settings as $name => $value) {
    set_config($name, $value, 'theme_boost_union');
}

set_config('rememberusername', 0);
set_config('guestloginbutton', 0);
set_config('registerauth', '');

if (is_readable($scsspath)) {
    $scss = file_get_contents($scsspath);
    set_config('scss', $scss === false ? '' : $scss, 'theme_boost_union');
    fwrite(STDOUT, "Custom SCSS loaded (" . strlen((string)$scss) . " bytes).\n");
}

$assets = [
    [
        'source' => $brandingroot . '/assets/official/cesmag-tau-wordmark.png',
        'filearea' => 'logo',
        'itemid' => 0,
    ],
    [
        'source' => $brandingroot . '/assets/official/cesmag-tau-wordmark.png',
        'filearea' => 'logocompact',
        'itemid' => 0,
    ],
    [
        'source' => $brandingroot . '/assets/official/tau-official-icon.png',
        'filearea' => 'favicon',
        'itemid' => 0,
    ],
    [
        'source' => $brandingroot . '/assets/official/tau-login-photo.jpg',
        'filearea' => 'loginbackgroundimage',
        'itemid' => 1,
    ],
];

foreach ($assets as $asset) {
    if (!is_readable($asset['source'])) {
        fwrite(STDOUT, "  [skip] Asset not found: " . $asset['source'] . "\n");
        continue;
    }

    $filename = basename($asset['source']);
    $filestorage->delete_area_files(
        $systemcontext->id,
        'theme_boost_union',
        $asset['filearea'],
        $asset['itemid']
    );
    $filestorage->create_file_from_pathname([
        'contextid' => $systemcontext->id,
        'component' => 'theme_boost_union',
        'filearea' => $asset['filearea'],
        'itemid' => $asset['itemid'],
        'filepath' => '/',
        'filename' => $filename,
    ], $asset['source']);
    set_config($asset['filearea'], $filename, 'theme_boost_union');
    fwrite(STDOUT, "  [ok] Uploaded: " . $filename . " -> " . $asset['filearea'] . "\n");
}

theme_reset_all_caches();

fwrite(STDOUT, "Boost Union TAU branding applied successfully.\n");
