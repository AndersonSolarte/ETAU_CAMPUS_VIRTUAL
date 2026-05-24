<?php
defined('MOODLE_INTERNAL') || die();

$THEME->name = 'tau_enterprise';
$THEME->parents = ['remui', 'boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->scss = function($theme) {
    return theme_tau_enterprise_get_main_scss_content($theme);
};

