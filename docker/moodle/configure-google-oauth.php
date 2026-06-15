<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require('/var/www/html/config.php');
require_once($CFG->libdir . '/filelib.php');

$clientid = trim((string)getenv('GOOGLE_OAUTH_CLIENT_ID'));
$clientsecret = trim((string)getenv('GOOGLE_OAUTH_CLIENT_SECRET'));
$alloweddomain = trim((string)getenv('GOOGLE_OAUTH_ALLOWED_DOMAIN'));

if ($clientid === '') {
    fwrite(STDOUT, "Google OAuth skipped: GOOGLE_OAUTH_CLIENT_ID is empty.\n");
    exit(0);
}

if ($clientsecret === '') {
    fwrite(STDOUT, "Google OAuth pending: GOOGLE_OAUTH_CLIENT_SECRET is empty.\n");
    fwrite(STDOUT, "Register this redirect URI in Google: {$CFG->wwwroot}/admin/oauth2callback.php\n");
    exit(0);
}

$existing = \core\oauth2\issuer::get_record(['servicetype' => 'google']);
$googleissuer = $existing ?: \core\oauth2\service\google::init();

if (!$googleissuer) {
    fwrite(STDERR, "Google OAuth error: built-in Google issuer could not be initialized.\n");
    exit(1);
}

$googleissuer->set('name', 'Google institucional');
$googleissuer->set('clientid', $clientid);
$googleissuer->set('clientsecret', $clientsecret);
$googleissuer->set('enabled', true);
$googleissuer->set('showonloginpage', \core\oauth2\issuer::EVERYWHERE);
$googleissuer->set('loginpagename', 'Google institucional');
$googleissuer->set('alloweddomains', $alloweddomain);
$googleissuer->set('requireconfirmation', false);
$googleissuer->set('loginparams', '');
$googleissuer->set('loginparamsoffline', 'access_type=offline&prompt=consent');

if ($existing) {
    $googleissuer->update();
    fwrite(STDOUT, "Google OAuth issuer updated.\n");
} else {
    $googleissuer->create();
    fwrite(STDOUT, "Google OAuth issuer created.\n");
}

\core\oauth2\service\google::create_endpoints($googleissuer);

$currentauth = (string)get_config('core', 'auth');
$authplugins = array_values(array_filter(array_map('trim', explode(',', $currentauth))));
foreach (['manual', 'email', 'oauth2'] as $plugin) {
    if (!in_array($plugin, $authplugins, true)) {
        $authplugins[] = $plugin;
    }
}
set_config('auth', implode(',', $authplugins));

fwrite(STDOUT, "Google OAuth configured for domain {$alloweddomain}.\n");
fwrite(STDOUT, "Authorized redirect URI: {$CFG->wwwroot}/admin/oauth2callback.php\n");
