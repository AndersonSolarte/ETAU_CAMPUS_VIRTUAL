<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$client_id     = '981280840907-30m28rou2aa2f3f9rqi17uknrag400ig.apps.googleusercontent.com';
$client_secret = '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';

// Update the OAuth2 issuer
$issuer = $DB->get_record('oauth2_issuer', []);
if (!$issuer) {
    echo "ERROR: No OAuth2 issuer found" . PHP_EOL;
    exit(1);
}

$issuer->clientid     = $client_id;
$issuer->clientsecret = $client_secret;
$issuer->enabled      = 1;
$DB->update_record('oauth2_issuer', $issuer);

// Purge OAuth2 caches
purge_caches();

echo "OK - Google OAuth updated!" . PHP_EOL;
echo "Client ID: ..." . substr($client_id, -20) . PHP_EOL;
echo "Secret:    ..." . substr($client_secret, -8) . PHP_EOL;
