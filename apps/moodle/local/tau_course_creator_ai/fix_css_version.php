<?php
define('CLI_SCRIPT', true);
require('config.php');
$html = get_config('core', 'additionalhtmlhead');
$html = str_replace('v=20260630c', 'v=20260630d', $html);
set_config('additionalhtmlhead', $html);
echo 'Updated successfully.';
