<?php
define('NO_OUTPUT_BUFFERING', true);
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

header('Content-Type: application/json; charset=utf-8');

try {
    global $DB;
    
    // 1. Get all public courses (publish_apoyo_academico = 1)
    $sql = "SELECT c.id, c.fullname, c.shortname, c.summary, c.category
              FROM {course} c
              JOIN {customfield_data} d ON d.instanceid = c.id
              JOIN {customfield_field} f ON f.id = d.fieldid
             WHERE f.shortname = 'publish_apoyo_academico' AND d.intvalue = 1";
             
    $courses = $DB->get_records_sql($sql);
    if (empty($courses)) {
        echo json_encode([]);
        exit;
    }
    
    // 2. Fetch all categories
    $categories = $DB->get_records('course_categories', null, 'sortorder ASC');
    $cat_map = [];
    foreach ($categories as $cat) {
        $cat_map[$cat->id] = [
            'id' => (int)$cat->id,
            'name' => $cat->name,
            'parent' => (int)$cat->parent,
            'depth' => (int)$cat->depth,
            'subcategories' => [],
            'courses' => []
        ];
    }
    
    // 3. Map courses to their respective categories
    foreach ($courses as $c) {
        $course_data = [
            'id' => (int)$c->id,
            'fullname' => $c->fullname,
            'shortname' => $c->shortname,
            'summary' => file_rewrite_pluginfile_urls(
                $c->summary, 
                'pluginfile.php', 
                context_course::instance($c->id)->id, 
                'course', 
                'summary', 
                null
            )
        ];
        if (isset($cat_map[$c->category])) {
            $cat_map[$c->category]['courses'][] = $course_data;
        }
    }
    
    // 4. Build the hierarchy tree
    // We want to find which categories are active (have courses or have descendants with courses)
    $active_cats = [];
    // Mark categories with courses as active, and trace upwards
    foreach ($cat_map as $id => $cat) {
        if (!empty($cat['courses'])) {
            $curr = $id;
            while ($curr > 0 && isset($cat_map[$curr])) {
                $active_cats[$curr] = true;
                $curr = $cat_map[$curr]['parent'];
            }
        }
    }
    
    // Nest subcategories
    foreach ($cat_map as $id => $cat) {
        if (!isset($active_cats[$id])) {
            continue;
        }
        $parent = $cat['parent'];
        if ($parent > 0 && isset($cat_map[$parent])) {
            // Nest under parent
            $cat_map[$parent]['subcategories'][] = &$cat_map[$id];
        }
    }
    
    // Get root level categories (only those with parent = 0)
    $tree = [];
    foreach ($cat_map as $id => $cat) {
        if (isset($active_cats[$id]) && $cat['parent'] == 0) {
            $tree[] = $cat_map[$id];
        }
    }
    
    echo json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
