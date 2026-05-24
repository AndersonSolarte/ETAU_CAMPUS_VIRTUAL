<?php
defined('MOODLE_INTERNAL') || die();

function theme_tau_enterprise_get_main_scss_content($theme): string {
    $scss = '';
    $path = __DIR__ . '/scss/preset.scss';

    if (file_exists($path)) {
        $scss .= file_get_contents($path);
    }

    return $scss;
}

