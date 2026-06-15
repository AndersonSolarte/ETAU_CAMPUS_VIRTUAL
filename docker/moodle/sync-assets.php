<?php
define('CLI_SCRIPT', true);
$target_dir_css = '/var/www/html/local/tau_course_creator_ai/assets/css/';
$target_dir_js = '/var/www/html/local/tau_course_creator_ai/assets/js/';

if (!is_dir($target_dir_css)) {
    mkdir($target_dir_css, 0755, true);
}
if (!is_dir($target_dir_js)) {
    mkdir($target_dir_js, 0755, true);
}

copy('/scripts/tau_frontpage.css', $target_dir_css . 'tau_frontpage.css');
copy('/scripts/tau_preloader.css', $target_dir_css . 'tau_preloader.css');
copy('/scripts/tau_global.css', $target_dir_css . 'tau_global.css');
copy('/scripts/tau_frontpage.js', $target_dir_js . 'tau_frontpage.js');
copy('/scripts/tau_preloader.js', $target_dir_js . 'tau_preloader.js');

echo "Assets copied successfully in container filesystem.\n";
echo "tau_frontpage.css size: " . filesize($target_dir_css . 'tau_frontpage.css') . " bytes\n";
echo "tau_preloader.css size: " . filesize($target_dir_css . 'tau_preloader.css') . " bytes\n";
