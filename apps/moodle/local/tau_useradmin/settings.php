<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('accounts', new admin_externalpage(
        'local_tau_useradmin_create',
        get_string('menucreateuser', 'local_tau_useradmin'),
        new moodle_url('/local/tau_useradmin/create_user.php'),
        'moodle/site:config'
    ));

    $ADMIN->add('accounts', new admin_externalpage(
        'local_tau_useradmin_upload',
        get_string('menuuploadusers', 'local_tau_useradmin'),
        new moodle_url('/local/tau_useradmin/upload_users.php'),
        'moodle/site:config'
    ));
}
