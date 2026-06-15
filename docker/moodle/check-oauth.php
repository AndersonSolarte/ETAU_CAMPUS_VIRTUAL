<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$issuers = $DB->get_records('oauth2_issuer');
foreach ($issuers as $i) {
    echo "=== OAuth2 Issuer ===" . PHP_EOL;
    echo "ID:           " . $i->id . PHP_EOL;
    echo "Name:         " . $i->name . PHP_EOL;
    echo "ClientID:     " . $i->clientid . PHP_EOL;
    echo "Secret(end):  ..." . substr($i->clientsecret, -6) . PHP_EOL;
    echo "Enabled:      " . $i->enabled . PHP_EOL;
    echo "BaseURL:      " . $i->baseurl . PHP_EOL;
}
