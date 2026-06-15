<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_tau_ai_provider', get_string('pluginname', 'local_tau_ai_provider'));

    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_tau_ai_provider/api_url',
        get_string('setting_api_url', 'local_tau_ai_provider'),
        get_string('setting_api_url_desc', 'local_tau_ai_provider'),
        'http://host.docker.internal:4000/api',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_tau_ai_provider/api_key',
        get_string('setting_api_key', 'local_tau_ai_provider'),
        get_string('setting_api_key_desc', 'local_tau_ai_provider'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_tau_ai_provider/timeout',
        get_string('setting_timeout', 'local_tau_ai_provider'),
        get_string('setting_timeout_desc', 'local_tau_ai_provider'),
        60,
        PARAM_INT
    ));
}
