<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$new_client_id     = $argv[1] ?? '';
$new_client_secret = $argv[2] ?? '';

if (!$new_client_id || !$new_client_secret) {
    echo "Usage: php fix-oauth.php <client_id> <client_secret>" . PHP_EOL;
    exit(1);
}

$issuer = $DB->get_record('oauth2_issuer', ['name' => 'Google institucional']);
if (!$issuer) {
    $issuer = $DB->get_record('oauth2_issuer', []);
}

if (!$issuer) {
    echo "ERROR: No OAuth2 issuer found in Moodle" . PHP_EOL;
    exit(1);
}

$issuer->clientid     = $new_client_id;
$issuer->clientsecret = $new_client_secret;
$issuer->enabled      = 1;
$DB->update_record('oauth2_issuer', $issuer);

echo "OK - OAuth2 updated!" . PHP_EOL;
echo "Client ID:     " . $new_client_id . PHP_EOL;
echo "Secret (end):  ..." . substr($new_client_secret, -6) . PHP_EOL;
echo PHP_EOL;
echo "Now test Google login at: http://localhost:8080/login/index.php" . PHP_EOL;
