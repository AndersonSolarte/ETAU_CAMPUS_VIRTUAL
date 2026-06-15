<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Injects a clean, minimalist dynamic course builder into Moodle's native course creation form.
 * Supports a Base Template + Individual Module Customization.
 */
function local_tau_course_creator_ai_ensure_custom_fields() {
    if (get_config('local_tau_course_creator_ai', 'customfields_initialized')) {
        return;
    }
    
    try {
        $handler = \core_course\customfield\course_handler::create();
        $categories = $handler->get_categories_with_fields();
        $ourcategory = null;
        foreach ($categories as $cat) {
            if ($cat->get('name') === 'Configuración de Publicación') {
                $ourcategory = $cat;
                break;
            }
        }
        
        if (!$ourcategory) {
            $categoryrecord = (object)[
                'name' => 'Configuración de Publicación',
                'component' => 'core_course',
                'area' => 'course',
                'itemid' => 0,
            ];
            $ourcategory = \core_customfield\category_controller::create(0, $categoryrecord, $handler);
            $ourcategory->save();
        }
        
        if ($ourcategory) {
            $fields = $ourcategory->get_fields();
            $ourfield = null;
            foreach ($fields as $f) {
                if ($f->get('shortname') === 'publish_apoyo_academico') {
                    $ourfield = $f;
                    break;
                }
            }
            
            if (!$ourfield) {
                $fieldrecord = (object)[
                    'type' => 'checkbox',
                    'categoryid' => $ourcategory->get('id'),
                ];
                $ourfield = \core_customfield\field_controller::create(0, $fieldrecord, $ourcategory);
                $ourfield->set('name', 'Publicar en Cursos de Apoyo Académico');
                $ourfield->set('shortname', 'publish_apoyo_academico');
                $ourfield->set('description', 'Determina si el curso se publica en la sección de cursos abiertos al público en la página principal.');
                $ourfield->set('descriptionformat', FORMAT_HTML);
                
                $configdata = [
                    'required' => 0,
                    'uniquevalues' => 0,
                    'defaultvalue' => 0,
                    'checkbydefault' => 0,
                ];
                $ourfield->set('configdata', json_encode($configdata));
                $ourfield->save();
            }
        }
        
        set_config('customfields_initialized', 1, 'local_tau_course_creator_ai');
    } catch (\Throwable $e) {
        debugging('local_tau_course_creator_ai: custom fields initialization failed: ' . $e->getMessage());
    }
}

function local_tau_course_creator_ai_before_footer(): string {
    global $PAGE, $DB;

    // Debug logging disabled for performance
    // @file_put_contents('/var/www/html/local/tau_course_creator_ai/debug_footer.log', "Called! Page type: " . ($PAGE->pagetype ?? 'null') . " URL: " . ($PAGE->url ? $PAGE->url->out() : 'null') . "\n", FILE_APPEND);

    // Ensure custom fields category and field exist
    local_tau_course_creator_ai_ensure_custom_fields();

    $path = $PAGE->url->get_path();
    $html = '';

    // Always load core preloader and dark mode theme scripts
    $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_preloader.js?v=20260615f" defer></script>';
    $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_darkmode.js" defer></script>';

    // Page-specific custom scripts
    if ($PAGE->pagetype === 'login-index') {
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_login.js" defer></script>';
    }

    if ($PAGE->pagetype === 'site-index') {
        // Query database for all public course IDs (where publish_apoyo_academico = 1)
        $sql_public = "SELECT c.id
                         FROM {course} c
                         JOIN {customfield_data} d ON d.instanceid = c.id
                         JOIN {customfield_field} f ON f.id = d.fieldid
                        WHERE f.shortname = 'publish_apoyo_academico' AND d.intvalue = 1";
        $public_courses = $DB->get_fieldset_sql($sql_public);
        
        $allowed_categories = [];
        if (!empty($public_courses)) {
            list($insql, $inparams) = $DB->get_in_or_equal($public_courses);
            $cat_ids = $DB->get_fieldset_sql("SELECT DISTINCT category FROM {course} WHERE id $insql", $inparams);
            foreach ($cat_ids as $cat_id) {
                $allowed_categories[] = (int)$cat_id;
                $cat = \core_course_category::get($cat_id, IGNORE_MISSING);
                if ($cat) {
                    foreach ($cat->get_parents() as $ancestor_id) {
                        $allowed_categories[] = (int)$ancestor_id;
                    }
                }
            }
            $allowed_categories = array_values(array_unique($allowed_categories));
        }
        
        $public_courses_json = json_encode(array_map('intval', $public_courses));
        $allowed_categories_json = json_encode($allowed_categories);
        $is_logged_in = (isloggedin() && !isguestuser()) ? 'true' : 'false';
        
        $html .= "<script>
            window.tauPublicCourses = {$public_courses_json};
            window.tauAllowedCategories = {$allowed_categories_json};
            window.tauIsLoggedIn = {$is_logged_in};
        </script>";

        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>';
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>';
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_frontpage.js?v=20260615a" defer></script>';
    }

    if ($PAGE->pagetype === 'my-index') {
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_dashboard.js?v=20260615" defer></script>';
    }

    // Injections strictly limited to site administrators
    if (is_siteadmin()) {
        if ($PAGE->pagetype === 'course-index' || $PAGE->pagetype === 'course-management' || $PAGE->pagetype === 'site-index' || $PAGE->pagetype === 'my-index') {
            $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_course_admin.js?v=20260615" defer></script>';
        }
        if ($PAGE->pagetype === 'course-edit') {
            $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_course_edit_ai.js?v=20260615" defer></script>';
        }
    }

    return $html;
}
