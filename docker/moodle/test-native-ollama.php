<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require('/var/www/html/config.php');
require_once($CFG->dirroot . '/lib/externallib.php');

$prompt = $argv[1] ?? 'Escribe un parrafo breve sobre seguridad informatica.';
$contextid = \context_system::instance()->id;
$admin = get_admin();
\core\session\manager::set_user($admin);

$result = \aiplacement_editor\external\generate_text::execute($contextid, $prompt);

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), PHP_EOL;
