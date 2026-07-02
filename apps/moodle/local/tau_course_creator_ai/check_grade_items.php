<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
global $DB;
$items = $DB->get_records('grade_items', ['courseid'=>47], 'id ASC', 'id, itemtype, itemmodule, grademax, needsupdate');
foreach ($items as $i) {
    echo $i->itemtype . ' ' . $i->itemmodule . ' max: ' . $i->grademax . ' update: ' . $i->needsupdate . "\n";
}
