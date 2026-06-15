<?php
define('CLI_SCRIPT', true);
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('/var/www/html/config.php');

// Enable Moodle debug
$CFG->debug = 32767;
$CFG->debugdisplay = 1;

// Check the oauth2 issuer data
echo "=== OAuth2 Issuer ===" . PHP_EOL;
$issuer = $DB->get_record('oauth2_issuer', ['id' => 1]);
echo "ID: " . $issuer->id . PHP_EOL;
echo "Name: " . $issuer->name . PHP_EOL;
echo "Type: " . gettype($issuer) . PHP_EOL;

// Test the oauth2 API
echo PHP_EOL . "=== Testing OAuth2 API ===" . PHP_EOL;
try {
    $issuers = \core\oauth2\api::get_all_issuers();
    foreach ($issuers as $i) {
        echo "Issuer: " . $i->get('name') . " enabled=" . $i->get('enabled') . PHP_EOL;
    }
    echo "OAuth2 API: OK" . PHP_EOL;
} catch (Exception $e) {
    echo "OAuth2 API ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace:" . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
}
