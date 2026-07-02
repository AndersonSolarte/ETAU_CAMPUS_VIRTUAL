<?php
define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');
global $DB;
$item = clone ($DB->get_record('feedback_item', ['typ'=>'multichoice'], '*', IGNORE_MULTIPLE) ?: new stdClass);
var_dump($item->presentation);
$item2 = clone ($DB->get_record('feedback_item', ['typ'=>'multichoicerated'], '*', IGNORE_MULTIPLE) ?: new stdClass);
var_dump($item2->presentation);
