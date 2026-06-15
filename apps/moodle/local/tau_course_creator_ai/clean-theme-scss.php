<?php
define('CLI_SCRIPT', true);
chdir('/var/www/html');
require('/var/www/html/config.php');

$scss = get_config('theme_moove', 'scss');

// 1. Remove the TAU-ESCUDO-HERO block
$pos_escudo_start = strpos($scss, '/* TAU-ESCUDO-HERO');
if ($pos_escudo_start !== false) {
    $pos_escudo_end = strpos($scss, '/* TAU-ESCUDO-HERO-FIN', $pos_escudo_start);
    if ($pos_escudo_end !== false) {
        $end_len = strlen('/* TAU-ESCUDO-HERO-FIN');
        $end_of_line = strpos($scss, "\n", $pos_escudo_end);
        if ($end_of_line !== false) {
            $length = $end_of_line - $pos_escudo_start + 1;
        } else {
            $length = ($pos_escudo_end + $end_len) - $pos_escudo_start;
        }
        $scss = substr_replace($scss, '', $pos_escudo_start, $length);
        echo "Removed TAU-ESCUDO-HERO block.\n";
    }
}

// 2. Remove other hero/card fix blocks to clean up the DB
$markers = [
    '/* FORCE-HERO',
    '/* TAU-HERO',
    '/* TAU-FINAL',
    '/* TAU-LOGO',
    '/* FORCE-CARD',
    '/* FIX-HERO',
    '/* FIX-HERO-FINAL'
];
foreach ($markers as $marker) {
    $pos = strpos($scss, $marker);
    if ($pos !== false) {
        $pos_end = strpos($scss, $marker . '-FIN', $pos);
        if ($pos_end !== false) {
            $end_marker = $marker . '-FIN';
            $end_len = strlen($end_marker);
            $end_of_line = strpos($scss, "\n", $pos_end);
            if ($end_of_line !== false) {
                $length = $end_of_line - $pos + 1;
            } else {
                $length = ($pos_end + $end_len) - $pos;
            }
            $scss = substr_replace($scss, '', $pos, $length);
            echo "Removed marker block: $marker\n";
        } else {
            $scss = substr($scss, 0, $pos);
            echo "Trimmed from marker: $marker\n";
        }
    }
}

// 3. Clear additional html footer settings
set_config('additionalhtmlfooter', '', 'theme_moove');
set_config('additionalhtmlfooter', '');

// 4. Save the SCSS setting
set_config('scss', $scss, 'theme_moove');
echo "Saved clean SCSS config.\n";

// 5. Clear theme and compiled SCSS caches
$dirs = [
    $CFG->dataroot . '/localcache/theme',
    $CFG->localcachedir . '/theme',
];
$total = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $n = 0;
    foreach ($it as $f) {
        if ($f->isFile()) {
            unlink($f->getRealPath());
            $n++;
        } elseif ($f->isDir()) {
            @rmdir($f->getRealPath());
        }
    }
    echo "Deleted $n files in $dir\n";
    $total += $n;
}

theme_reset_all_caches();
purge_all_caches();
echo "Theme caches reset and all caches purged.\n";
