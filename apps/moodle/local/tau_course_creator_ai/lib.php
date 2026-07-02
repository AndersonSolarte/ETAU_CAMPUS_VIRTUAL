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

function local_tau_course_creator_ai_get_current_user_role(): string {
    global $USER, $DB;

    if (!isloggedin() || isguestuser()) {
        return '';
    }

    if (is_siteadmin()) {
        return 'admin';
    }

    $sql = "SELECT COUNT(1)
              FROM {role_assignments} ra
              JOIN {role} r ON r.id = ra.roleid
             WHERE ra.userid = ?
               AND r.archetype IN ('editingteacher', 'teacher', 'manager')";
    $isteacher = (int)$DB->count_records_sql($sql, [$USER->id]) > 0;

    return $isteacher ? 'teacher' : 'student';
}



function local_tau_course_creator_ai_before_footer(): string {
    global $PAGE, $DB, $COURSE, $USER;

    // Debug logging enabled
    @file_put_contents('/var/www/html/local/tau_course_creator_ai/debug_footer.log', "Called! Page type: " . ($PAGE->pagetype ?? 'null') . " URL: " . ($PAGE->url ? $PAGE->url->out() : 'null') . "\n", FILE_APPEND);

    // Ensure custom fields category and field exist
    local_tau_course_creator_ai_ensure_custom_fields();

    $path = $PAGE->url->get_path();
    $html = '';
    $taurole = local_tau_course_creator_ai_get_current_user_role();

    // Always load core preloader and dark mode theme scripts
    $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_preloader.js?v=' . time() . '" defer></script>';
    $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_darkmode.js?v=' . time() . '" defer></script>';

    // Page-specific custom scripts
    if ($PAGE->pagetype === 'login-index') {
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_login.js?v=20260618h" defer></script>';
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
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_frontpage.js?v=20260618b" defer></script>';
    }

    if ($PAGE->pagetype === 'my-index' || $PAGE->pagetype === 'my-courses') {
        $is_admin = is_siteadmin() ? 'true' : 'false';
        $html .= "<script>window.tauIsSiteAdmin = {$is_admin};</script>";
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_dashboard.js?v=' . time() . '" defer></script>';
    }

    if ($path === '/course/view.php' && !empty($COURSE->id) && (int)$COURSE->id > 1) {
        $coursecontext = context_course::instance((int)$COURSE->id);
        $canmanagerecordings = (is_siteadmin() || has_capability('moodle/course:manageactivities', $coursecontext)) ? 'true' : 'false';
        \local_tau_course_creator_ai\recordings_manager::ensure_recordings_section((int)$COURSE->id);
        $recordingsdata = json_encode(\local_tau_course_creator_ai\recordings_manager::get_recordings_for_course((int)$COURSE->id));
        $manageurl = json_encode((new moodle_url('/local/tau_course_creator_ai/recordings.php', ['courseid' => (int)$COURSE->id]))->out(false));

        $html .= "<script>
            window.tauCourseRecordingsData = {$recordingsdata};
            window.tauCourseRecordingsCanManage = {$canmanagerecordings};
            window.tauCourseRecordingsManageUrl = {$manageurl};
        </script>";
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_recordings.js?v=20260618a" defer></script>';
    }

    if ($PAGE->pagetype === 'course-index' || $PAGE->pagetype === 'course-management' || $PAGE->pagetype === 'site-index' || $PAGE->pagetype === 'my-index') {
        $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_course_admin.js?v=20260616e" defer></script>';
    }

    // Injections strictly limited to site administrators
    if (is_siteadmin()) {
        if ($PAGE->pagetype === 'course-edit') {
            $html .= '<script src="/local/tau_course_creator_ai/assets/js/tau_course_edit_ai.js?v=20260615" defer></script>';
        }
    }

    if ($taurole !== '') {
        $safeclass = s('tau-role-' . $taurole);
        $html .= '<script>document.addEventListener("DOMContentLoaded",function(){document.body.classList.add("' . $safeclass . '");});</script>';
    }

    if ($taurole === 'student') {
        $html .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            function tauNormalizeText(value) {
                return String(value || "")
                    .normalize("NFD")
                    .replace(/[\\u0300-\\u036f]/g, "")
                    .toLowerCase()
                    .trim();
            }

            function tauHideStudentResetButtons() {
                document.querySelectorAll("a, button, input[type=submit], .singlebutton, form").forEach(function(node) {
                    var text = "";
                    if (node.tagName === "INPUT") {
                        text = node.value || "";
                    } else {
                        text = node.textContent || "";
                    }

                    var normalized = tauNormalizeText(text);
                    if (
                        normalized.indexOf("restablecer pagina a por defecto") !== -1 ||
                        normalized.indexOf("reset page to default") !== -1 ||
                        normalized.indexOf("personalizar esta pagina") !== -1 ||
                        normalized.indexOf("customise this page") !== -1 ||
                        normalized.indexOf("customize this page") !== -1
                    ) {
                        var target = node.closest(".singlebutton, form, .header-actions, .page-header-headings + div") || node;
                        target.style.setProperty("display", "none", "important");
                    }
                });
            }

            function tauHideStudentPrimaryNav() {
                document.querySelectorAll(".primary-navigation a, .moremenu.navigation a, .navbar a").forEach(function(node) {
                    var normalized = tauNormalizeText(node.textContent || "");
                    if (
                        normalized === "pagina principal" ||
                        normalized === "inicio del sitio" ||
                        normalized === "inicio" ||
                        normalized === "home" ||
                        normalized === "site home" ||
                        normalized === "area personal" ||
                        normalized === "dashboard"
                    ) {
                        var navitem = node.closest(".nav-item, li, .moremenu-navigation-secondary, .menu-item") || node;
                        navitem.style.setProperty("display", "none", "important");
                        node.style.setProperty("display", "none", "important");
                    }
                });
            }

            function tauHideStudentProfileChrome() {
                if (!document.body || (document.body.id !== "page-user-edit" && document.body.id !== "page-user-profile")) {
                    return;
                }

                document.querySelectorAll(
                    "#page-header, .page-header-headings, .page-context-header, .header-actions-container, .breadcrumb-nav, .secondary-navigation"
                ).forEach(function(node) {
                    node.style.setProperty("display", "none", "important");
                });

                var regionmain = document.querySelector("#region-main");
                if (regionmain) {
                    regionmain.style.setProperty("margin-top", "0", "important");
                    regionmain.style.setProperty("padding-top", "0", "important");
                }
            }

            tauHideStudentResetButtons();
            tauHideStudentPrimaryNav();
            tauHideStudentProfileChrome();

            new MutationObserver(function() {
                tauHideStudentResetButtons();
                tauHideStudentPrimaryNav();
                tauHideStudentProfileChrome();
            }).observe(document.body, {childList: true, subtree: true});
        });
        </script>';
    }

    $html .= '<style>
    body.tau-role-student .editmode-switch-form,
    body.tau-role-student [data-region="editmode"],
    body.tau-role-student .editmode-toggle,
    body.tau-role-student [data-action="turn-editing-on"],
    body.tau-role-student [data-action="turn-editing-off"] {
        display: none !important;
    }
    /* Hide Site Home / Pagina principal AND Area personal (Dashboard) for students globally via CSS */
    body.tau-role-student .primary-navigation a[data-key="home"],
    body.tau-role-student .moremenu.navigation a[data-key="home"],
    body.tau-role-student .navbar a[data-key="home"],
    body.tau-role-student .nav-item[data-key="home"],
    body.tau-role-student a[href$="/?redirect=0"],
    body.tau-role-student .primary-navigation a[data-key="myhome"],
    body.tau-role-student .moremenu.navigation a[data-key="myhome"],
    body.tau-role-student .navbar a[data-key="myhome"],
    body.tau-role-student .nav-item[data-key="myhome"],
    body.tau-role-student .primary-navigation a[data-key="dashboard"],
    body.tau-role-student .moremenu.navigation a[data-key="dashboard"],
    body.tau-role-student .navbar a[data-key="dashboard"],
    body.tau-role-student .nav-item[data-key="dashboard"],
    body.tau-role-student a[href$="/my/"],
    body.tau-role-student a[href$="/my/index.php"] {
        display: none !important;
    }
    body.tau-role-student#page-user-edit #page-header,
    body.tau-role-student#page-user-edit .page-header-headings,
    body.tau-role-student#page-user-edit .page-context-header,
    body.tau-role-student#page-user-edit .header-actions-container,
    body.tau-role-student#page-user-edit .breadcrumb-nav,
    body.tau-role-student#page-user-edit .secondary-navigation,
    body.tau-role-student#page-user-profile #page-header,
    body.tau-role-student#page-user-profile .page-header-headings,
    body.tau-role-student#page-user-profile .page-context-header,
    body.tau-role-student#page-user-profile .header-actions-container,
    body.tau-role-student#page-user-profile .breadcrumb-nav,
    body.tau-role-student#page-user-profile .secondary-navigation {
        display: none !important;
    }
    body.tau-role-student#page-user-edit #region-main,
    body.tau-role-student#page-user-profile #region-main {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    #courseindex .courseindex-item:hover,
    #courseindex .courseindex-item:focus-within,
    #courseindex .courseindex-item.pageitem:hover,
    #courseindex .courseindex-item.pageitem:focus-within,
    .courseindex .courseindex-item:hover,
    .courseindex .courseindex-item:focus-within,
    .courseindex .courseindex-item.pageitem:hover,
    .courseindex .courseindex-item.pageitem:focus-within {
        background: #c62b3a !important;
        border-radius: 10px !important;
    }
    #courseindex .courseindex-item:hover *,
    #courseindex .courseindex-item:focus-within *,
    #courseindex .courseindex-item.pageitem:hover *,
    #courseindex .courseindex-item.pageitem:focus-within *,
    .courseindex .courseindex-item:hover *,
    .courseindex .courseindex-item:focus-within *,
    .courseindex .courseindex-item.pageitem:hover *,
    .courseindex .courseindex-item.pageitem:focus-within * {
        color: #fff !important;
        fill: #fff !important;
    }
    #courseindex .courseindex-item.active,
    #courseindex .courseindex-item[aria-current="true"],
    #courseindex .courseindex-item.pageitem.active,
    .courseindex .courseindex-item.active,
    .courseindex .courseindex-item[aria-current="true"],
    .courseindex .courseindex-item.pageitem.active {
        background: #8d182a !important;
        border-radius: 10px !important;
    }
    #courseindex .courseindex-item.active *,
    #courseindex .courseindex-item[aria-current="true"] *,
    #courseindex .courseindex-item.pageitem.active *,
    .courseindex .courseindex-item.active *,
    .courseindex .courseindex-item[aria-current="true"] *,
    .courseindex .courseindex-item.pageitem.active * {
        color: #fff !important;
        fill: #fff !important;
    }
    .tau-banner-modulo {
        position: relative !important;
        overflow: hidden !important;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.14) 0, rgba(255,255,255,0) 30%),
            linear-gradient(135deg, #4b0f18 0%, #7b1627 46%, #c62b3a 100%) !important;
        border: 1px solid rgba(198, 43, 58, 0.18) !important;
        border-radius: 24px !important;
        padding: 24px 28px 22px 30px !important;
        margin: 32px 0 22px 0 !important;
        display: flex !important;
        align-items: center !important;
        min-height: 108px !important;
        box-shadow: 0 18px 42px rgba(75, 15, 24, 0.18) !important;
    }
    .tau-banner-modulo::before {
        content: "TAU CAMPUS VIRTUAL" !important;
        position: absolute !important;
        top: 18px !important;
        left: 30px !important;
        padding: 7px 14px !important;
        border-radius: 999px !important;
        background: rgba(255,255,255,.12) !important;
        color: rgba(255,255,255,.94) !important;
        font-size: .70rem !important;
        font-weight: 800 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        backdrop-filter: blur(8px) !important;
    }
    .tau-banner-modulo::after {
        content: "" !important;
        position: absolute !important;
        right: -34px !important;
        top: -28px !important;
        width: 180px !important;
        height: 180px !important;
        border-radius: 50% !important;
        background: radial-gradient(circle, rgba(255,255,255,.18) 0%, rgba(255,255,255,.03) 42%, rgba(255,255,255,0) 72%) !important;
        pointer-events: none !important;
    }
    .tau-banner-modulo span {
        position: relative !important;
        z-index: 1 !important;
        display: block !important;
        max-width: 88% !important;
        margin-top: 22px !important;
        font-size: clamp(1.3rem, 2vw, 1.72rem) !important;
        line-height: 1.2 !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        letter-spacing: -0.03em !important;
        text-wrap: balance !important;
    }
    .tau-banner-separador {
        position: relative !important;
        overflow: hidden !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 12px !important;
        background: linear-gradient(135deg, #ffffff 0%, #fff7f8 100%) !important;
        border: 1px solid rgba(198, 43, 58, 0.12) !important;
        border-radius: 18px !important;
        padding: 12px 18px 12px 20px !important;
        margin: 22px 0 14px 0 !important;
        box-shadow: 0 10px 24px rgba(75, 15, 24, 0.08) !important;
    }
    .tau-banner-separador::before {
        content: "" !important;
        width: 10px !important;
        height: 10px !important;
        min-width: 10px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #c62b3a 0%, #8d182a 100%) !important;
        box-shadow: 0 0 0 5px rgba(198, 43, 58, 0.10) !important;
        position: static !important;
    }
    .tau-banner-separador::after {
        content: "" !important;
        position: absolute !important;
        inset: auto 18px 0 18px !important;
        height: 1px !important;
        background: linear-gradient(90deg, rgba(198,43,58,.24) 0%, rgba(198,43,58,.05) 100%) !important;
    }
    .tau-banner-separador span {
        position: relative !important;
        z-index: 1 !important;
        font-size: .95rem !important;
        font-weight: 800 !important;
        letter-spacing: .02em !important;
        color: #4b0f18 !important;
    }
    [data-bs-theme="dark"] .tau-banner-modulo {
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.12) 0, rgba(255,255,255,0) 28%),
            linear-gradient(135deg, #20060c 0%, #4a0e17 46%, #8d182a 100%) !important;
        border-color: rgba(255,255,255,.08) !important;
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.34) !important;
    }
    [data-bs-theme="dark"] .tau-banner-separador {
        background: linear-gradient(135deg, #1f1820 0%, #2a2029 100%) !important;
        border-color: rgba(255,255,255,.08) !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.22) !important;
    }
    [data-bs-theme="dark"] .tau-banner-separador span {
        color: #fff5f6 !important;
    }
    </style>';

    // Globally replace the top navbar username with the user's generic role (Estudiante, Profesor, Administrador)
    if (isloggedin() && !isguestuser()) {
        global $USER, $DB;
        $role_label = 'Estudiante';
        if (is_siteadmin()) {
            $role_label = 'Administrador';
        } else {
            // Check if they have the teacher role anywhere
            $teacher_roles = $DB->get_records_sql("
                SELECT DISTINCT r.shortname 
                FROM {role_assignments} ra 
                JOIN {role} r ON ra.roleid = r.id 
                WHERE ra.userid = ? AND r.shortname IN ('editingteacher', 'teacher')
            ", [$USER->id]);
            if (!empty($teacher_roles)) {
                $role_label = 'Profesor';
            }
        }
        
        $html .= '<style>
            .usermenu .usertext, .usermenu .userinfo, .usermenu .username, .usermenu .user-name, .usermenu .user-text, .userbutton .usertext, .userbutton .name {
                font-size: 0 !important;
                display: none !important;
            }
            .usermenu .userbutton {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .usermenu .tau-role-label {
                font-weight: 500 !important;
                text-transform: capitalize !important;
                font-size: 0.95rem !important;
                margin-right: 12px !important;
                line-height: 1 !important;
                display: inline-flex !important;
                align-items: center !important;
            }
            
            /* Course Section Header Styling (e.g. "General", "Tema 1") */
            html body .course-section-header {
                background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%) !important;
                border: 1px solid rgba(0,0,0,0.06) !important;
                border-left: 5px solid #8d182a !important;
                border-radius: 12px !important;
                padding: 10px 20px !important;
                margin-bottom: 20px !important;
                margin-top: 10px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
                display: flex !important;
                align-items: center !important;
                width: 100% !important;
                transition: all 0.3s ease !important;
            }
            html body[data-bs-theme="dark"] .course-section-header {
                background: linear-gradient(135deg, #252525 0%, #1e1e1e 100%) !important;
                border-color: rgba(255,255,255,0.06) !important;
            }
            
            html body .course-section-header .sectionname {
                background: transparent !important;
                border: none !important;
                padding: 0 0 0 12px !important;
                margin: 0 !important;
                box-shadow: none !important;
                font-weight: 800 !important;
                font-size: 1.4rem !important;
                color: #222 !important;
                flex-grow: 1 !important;
                display: flex !important;
                align-items: center !important;
            }
            html body[data-bs-theme="dark"] .course-section-header .sectionname {
                color: #eee !important;
            }
            html body .course-section-header .sectionname a {
                color: inherit !important;
                text-decoration: none !important;
            }
            
            /* Hide the redundant red banners */
            html body .tau-banner-modulo {
                display: none !important;
            }
            
            /* Hide the redundant "GENERAL" badge inside the red welcome banner */
            html body .course-content ul.topics li.section .summary div[style*="border-radius:999px"][style*="background:rgba(255,255,255,.12)"],
            html body .course-content ul.topics li.section .summary div[style*="text-transform:uppercase"][style*="letter-spacing"] {
                display: none !important;
            }

            /* Nuclear specificity to fix transparent dropdown menus across Moodle */
            html body .dropdown-menu,
            html body .card .dropdown-menu,
            html body .coursebox .dropdown-menu,
            html body .action-menu .dropdown-menu,
            html body .block_myoverview .dropdown-menu {
                z-index: 10000 !important;
                background-color: #ffffff !important;
                background: #ffffff !important;
                background-image: none !important;
                background-clip: padding-box !important;
                box-shadow: 0 0 0 999px #ffffff inset, 0 10px 40px rgba(0, 0, 0, 0.4) !important;
                border: 1px solid rgba(0,0,0,0.1) !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                opacity: 1 !important;
                filter: none !important;
                mix-blend-mode: normal !important;
                isolation: isolate !important;
                overflow: hidden !important;
            }
            html body .dropdown-menu::before,
            html body .card .dropdown-menu::before,
            html body .coursebox .dropdown-menu::before,
            html body .action-menu .dropdown-menu::before,
            html body .block_myoverview .dropdown-menu::before {
                content: "" !important;
                position: absolute !important;
                inset: 0 !important;
                z-index: 0 !important;
                background: #ffffff !important;
                background-image: none !important;
                opacity: 1 !important;
                pointer-events: none !important;
            }
            html body .dropdown-menu > * {
                position: relative !important;
                z-index: 1 !important;
            }
            html body .card.dashboard-card,
            html body .card.course-card,
            html body .card.dashboard-card.tau-card-ready,
            html body .card.course-card.tau-card-ready,
            html body .card.dashboard-card .dropdown,
            html body .card.course-card .dropdown,
            html body .card.dashboard-card .action-menu,
            html body .card.course-card .action-menu,
            html body .card.dashboard-card .dropdown-menu,
            html body .card.course-card .dropdown-menu,
            html body .card.dashboard-card .action-menu .dropdown-menu,
            html body .card.course-card .action-menu .dropdown-menu {
                overflow: visible !important;
            }
            html body .dropdown-menu a,
            html body .dropdown-menu .dropdown-item {
                display: block !important;
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #333333 !important;
                font-weight: 500 !important;
            }
            html body .dropdown-menu a:hover,
            html body .dropdown-menu .dropdown-item:hover {
                background: #f5f5f5 !important;
                background-color: #f5f5f5 !important;
                color: #000000 !important;
            }
            
            html body[data-bs-theme="dark"] .dropdown-menu,
            html body.theme-dark .dropdown-menu {
                background-color: #1e1e1e !important;
                background: #1e1e1e !important;
                background-image: none !important;
                border: 1px solid rgba(255,255,255,0.1) !important;
                box-shadow: 0 0 0 999px #1e1e1e inset, 0 10px 40px rgba(0, 0, 0, 0.5) !important;
            }
            html body[data-bs-theme="dark"] .dropdown-menu::before,
            html body.theme-dark .dropdown-menu::before {
                background: #1e1e1e !important;
            }
            html body[data-bs-theme="dark"] .dropdown-menu a,
            html body[data-bs-theme="dark"] .dropdown-menu .dropdown-item,
            html body.theme-dark .dropdown-menu a,
            html body.theme-dark .dropdown-menu .dropdown-item {
                background: #1e1e1e !important;
                background-color: #1e1e1e !important;
                color: #eeeeee !important;
            }
            html body[data-bs-theme="dark"] .dropdown-menu a:hover,
            html body[data-bs-theme="dark"] .dropdown-menu .dropdown-item:hover,
            html body.theme-dark .dropdown-menu a:hover,
            html body.theme-dark .dropdown-menu .dropdown-item:hover {
                background-color: #2a2a2a !important;
                color: #ffffff !important;
            }
            html body .dropdown-menu > *,
            html body .dropdown-menu li,
            html body .dropdown-menu div,
            html body .dropdown-menu span,
            html body .dropdown-menu button {
                background: #ffffff !important;
                background-color: #ffffff !important;
                background-image: none !important;
                opacity: 1 !important;
            }
            html body .dropdown-menu button {
                display: block !important;
                width: 100% !important;
                border-radius: 8px !important;
                color: #333333 !important;
            }
            html body .dropdown-menu button:hover {
                background: #f5f5f5 !important;
                background-color: #f5f5f5 !important;
                color: #000000 !important;
            }
            html body[data-bs-theme="dark"] .dropdown-menu > *,
            html body[data-bs-theme="dark"] .dropdown-menu li,
            html body[data-bs-theme="dark"] .dropdown-menu div,
            html body[data-bs-theme="dark"] .dropdown-menu span,
            html body[data-bs-theme="dark"] .dropdown-menu button,
            html body.theme-dark .dropdown-menu > *,
            html body.theme-dark .dropdown-menu li,
            html body.theme-dark .dropdown-menu div,
            html body.theme-dark .dropdown-menu span,
            html body.theme-dark .dropdown-menu button {
                background: #1e1e1e !important;
                background-color: #1e1e1e !important;
                background-image: none !important;
            }
            html body[data-bs-theme="dark"] .dropdown-menu button,
            html body.theme-dark .dropdown-menu button {
                color: #eeeeee !important;
            }
            html body[data-bs-theme="dark"] .dropdown-menu button:hover,
            html body.theme-dark .dropdown-menu button:hover {
                background-color: #2a2a2a !important;
                color: #ffffff !important;
            }

            /* Nuclear specificity to fix elongated avatar circles */
            html body .userinitials, 
            html body .userpicture, 
            html body .avatar, 
            html body img.userpicture,
            html body .course-contact-avatar {
                flex-shrink: 0 !important;
                border-radius: 50% !important;
                object-fit: cover !important;
                aspect-ratio: 1 / 1 !important;
                width: 38px !important;
                height: 38px !important;
                min-width: 38px !important;
                min-height: 38px !important;
                max-width: 38px !important;
                max-height: 38px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                overflow: hidden !important;
            }
        </style>';

        $html .= '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var injectRoleLabel = function() {
                    var usermenu = document.querySelector(".usermenu");
                    if (!usermenu) return;
                    
                    var toggleBtn = usermenu.querySelector(".dropdown-toggle") || 
                                    usermenu.querySelector("a[data-toggle=\'dropdown\']") || 
                                    usermenu.querySelector(".userbutton") ||
                                    usermenu.querySelector("a[role=\'button\']");
                                    
                    if (toggleBtn) {
                        // 1. Hide the original name (spans and text nodes)
                        var spans = toggleBtn.querySelectorAll("span:not(.tau-role-label)");
                        spans.forEach(function(s) {
                            if (!s.classList.contains("avatar") && !s.classList.contains("useravatar") && !s.querySelector("img")) {
                                s.style.setProperty("display", "none", "important");
                            }
                        });
                        
                        // Destroy any direct text nodes inside the button, but spare our own role label!
                        var walker = document.createTreeWalker(toggleBtn, NodeFilter.SHOW_TEXT, null, false);
                        var node;
                        while (node = walker.nextNode()) {
                            if (node.parentElement && node.parentElement.classList.contains("tau-role-label")) {
                                continue;
                            }
                            if (node.nodeValue.trim().length > 0) {
                                node.nodeValue = "";
                            }
                        }
                        
                        // 2. Inject our own clean role label if it doesn\'t exist
                        if (!toggleBtn.querySelector(".tau-role-label")) {
                            var roleSpan = document.createElement("span");
                            roleSpan.className = "tau-role-label";
                            roleSpan.textContent = "' . $role_label . '";
                            
                            var userbuttonWrapper = toggleBtn.querySelector(".userbutton") || toggleBtn;
                            userbuttonWrapper.insertBefore(roleSpan, userbuttonWrapper.firstChild);
                        }
                    }
                };
                
                setInterval(injectRoleLabel, 100);
            });
        </script>';
    }

    return $html;
}
