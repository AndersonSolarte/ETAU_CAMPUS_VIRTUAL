<?php
define('NO_OUTPUT_BUFFERING', true);
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

header('Content-Type: application/json; charset=utf-8');

try {
    global $DB;

    $sql = "SELECT c.id, c.fullname, c.shortname, c.summary, c.category
              FROM {course} c
             WHERE c.id <> 1
               AND c.visible = 1
               AND NOT EXISTS (
                    SELECT 1
                      FROM {customfield_data} d
                      JOIN {customfield_field} f
                        ON f.id = d.fieldid
                     WHERE d.instanceid = c.id
                       AND f.shortname = 'publish_apoyo_academico'
                       AND d.intvalue = 1
               )";

    $courses = $DB->get_records_sql($sql);
    if (empty($courses)) {
        echo json_encode([]);
        exit;
    }

    $categories = $DB->get_records('course_categories', ['visible' => 1], 'sortorder ASC');
    $catmap = [];

    foreach ($categories as $cat) {
        $catmap[$cat->id] = [
            'id' => (int)$cat->id,
            'name' => $cat->name,
            'parent' => (int)$cat->parent,
            'depth' => (int)$cat->depth,
            'subcategories' => [],
            'courses' => [],
            'coursecount' => 0,
            'totalcourses' => 0,
        ];
    }

    foreach ($courses as $course) {
        if (!isset($catmap[$course->category])) {
            continue;
        }

        $catmap[$course->category]['courses'][] = [
            'id' => (int)$course->id,
            'fullname' => $course->fullname,
            'shortname' => $course->shortname,
            'summary' => file_rewrite_pluginfile_urls(
                $course->summary,
                'pluginfile.php',
                context_course::instance($course->id)->id,
                'course',
                'summary',
                null
            ),
        ];
        $catmap[$course->category]['coursecount']++;
    }

    $active = [];
    foreach ($catmap as $id => $category) {
        if ($category['coursecount'] < 1) {
            continue;
        }

        $current = $id;
        while ($current > 0 && isset($catmap[$current])) {
            $active[$current] = true;
            $current = $catmap[$current]['parent'];
        }
    }

    foreach ($catmap as $id => $category) {
        if (empty($active[$id])) {
            continue;
        }

        $parent = $category['parent'];
        if ($parent > 0 && isset($catmap[$parent]) && !empty($active[$parent])) {
            $catmap[$parent]['subcategories'][] = &$catmap[$id];
        }
    }

    $calculateTotals = function(array &$category) use (&$calculateTotals): int {
        $total = (int)$category['coursecount'];
        foreach ($category['subcategories'] as &$subcategory) {
            $total += $calculateTotals($subcategory);
        }
        $category['totalcourses'] = $total;
        return $total;
    };

    $tree = [];
    foreach ($catmap as $id => &$category) {
        if (!empty($active[$id]) && $category['parent'] === 0) {
            $calculateTotals($category);
            $tree[] = $category;
        }
    }

    echo json_encode(array_values($tree), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
