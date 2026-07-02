<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');

$ai = new \local_tau_course_creator_ai\ai_service();
try {
    echo "Running AI stream planning...\n";
    $on_token = function($t) {
        echo $t;
    };
    $result = $ai->stream_plan("Matemáticas, estadísticas y programación orientada a datos masivos", "es", "", [], $on_token);
    echo "\nSUCCESS!\n";
    print_r($result);
} catch (\Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
