<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');
global $DB;
// find feedback items
$items = $DB->get_records('feedback_item', ['feedback' => 967], 'position ASC');
foreach($items as $i) {
    echo "ID:{$i->id} Typ:{$i->typ} Name:{$i->name}\n";
}
