<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'enrol_cesmag/payurl',
        get_string('payurl', 'enrol_cesmag'),
        get_string('payurl_desc', 'enrol_cesmag'),
        'https://ruah.unicesmag.edu.co/generar-recibos-pagos',
        PARAM_URL
    ));
}
