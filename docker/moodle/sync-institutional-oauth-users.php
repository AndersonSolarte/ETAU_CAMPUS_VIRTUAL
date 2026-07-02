<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require('/var/www/html/config.php');
require_once($CFG->dirroot . '/user/lib.php');

global $DB;

$domain = trim((string)getenv('GOOGLE_OAUTH_ALLOWED_DOMAIN'));
if ($domain === '') {
    $domain = 'unicesmag.edu.co';
}
$domainsuffix = '@' . core_text::strtolower($domain);

$users = $DB->get_records_select('user', 'deleted = 0 AND id > 2');
$updated = 0;
$skipped = 0;

foreach ($users as $user) {
    $email = core_text::strtolower(trim((string)$user->email));
    if ($email === '') {
        $skipped++;
        continue;
    }

    if (!str_ends_with($email, $domainsuffix)) {
        if ($user->auth === 'oauth2' && (int)$user->suspended !== 1) {
            $user->suspended = 1;
            $user->timemodified = time();
            $DB->update_record('user', $user);
            fwrite(STDOUT, "[blocked] {$user->id} :: {$email}\n");
        } else {
            $skipped++;
        }
        continue;
    }

    $changed = false;

    if ($user->username !== $email) {
        $user->username = $email;
        $changed = true;
    }
    if ($user->auth !== 'oauth2') {
        $user->auth = 'oauth2';
        $changed = true;
    }
    if ((string)$user->password !== '') {
        $user->password = AUTH_PASSWORD_NOT_CACHED;
        $changed = true;
    }
    if ((int)$user->policyagreed !== 1) {
        $user->policyagreed = 1;
        $changed = true;
    }
    if ((int)$user->confirmed !== 1) {
        $user->confirmed = 1;
        $changed = true;
    }

    if ($changed) {
        $user->timemodified = time();
        $DB->update_record('user', $user);
    }

    unset_user_preference('auth_forcepasswordchange', $user);
    $updated++;
    fwrite(STDOUT, "[ok] {$user->id} :: {$email}\n");
}

set_config('authpreventaccountcreation', 1);
purge_all_caches();

fwrite(STDOUT, "Done. Updated: {$updated}. Skipped: {$skipped}.\n");
