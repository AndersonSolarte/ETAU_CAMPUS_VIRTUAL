<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

global $DB;

$search = 'deseas crear';
echo "Searching database for '{$search}'...\n";

// Get list of all tables
$tables = $DB->get_tables();
foreach ($tables as $table) {
    $columns = $DB->get_columns($table);
    $textcolumns = array();
    foreach ($columns as $colname => $col) {
        // Search text-like columns
        if (in_array($col->meta_type, array('C', 'X'))) {
            $textcolumns[] = $colname;
        }
    }
    
    if (empty($textcolumns)) {
        continue;
    }
    
    // Build OR conditions
    $where = array();
    $params = array();
    foreach ($textcolumns as $col) {
        $where[] = $DB->sql_like($col, ':search_' . $col, false, false);
        $params['search_' . $col] = '%' . $search . '%';
    }
    
    $wheresql = implode(' OR ', $where);
    try {
        $count = $DB->count_records_sql("SELECT COUNT(*) FROM {" . $table . "} WHERE " . $wheresql, $params);
        if ($count > 0) {
            echo "Found {$count} match(es) in table: mdl_{$table}\n";
            $records = $DB->get_records_sql("SELECT * FROM {" . $table . "} WHERE " . $wheresql, $params, 0, 5);
            foreach ($records as $record) {
                echo "  Record ID: {$record->id}\n";
                foreach ($textcolumns as $col) {
                    if (stripos($record->$col, $search) !== false) {
                        echo "    Column '{$col}': " . substr(strip_tags($record->$col), 0, 300) . "\n";
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        // Ignore table errors
    }
}
echo "Search completed.\n";
