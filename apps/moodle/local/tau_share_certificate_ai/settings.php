<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_tau_share_certificate_ai', get_string('pluginname', 'local_tau_share_certificate_ai'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_tau_share_certificate_ai/linkedin_client_id',
        get_string('setting_linkedin_id', 'local_tau_share_certificate_ai'),
        get_string('setting_linkedin_id_desc', 'local_tau_share_certificate_ai'),
        ''
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_tau_share_certificate_ai/linkedin_client_secret',
        get_string('setting_linkedin_secret', 'local_tau_share_certificate_ai'),
        get_string('setting_linkedin_secret_desc', 'local_tau_share_certificate_ai'),
        ''
    ));
}
