<?php
/**
 * Universal course resource editor.
 * ALL module types get name + description editing.
 * Type-specific extras: URL editor, Page AI improvement, Label content editor.
 * Accessible to: site admins + teachers with moodle/course:manageactivities.
 */
define('NO_OUTPUT_BUFFERING', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_once(__DIR__ . '/classes/ai_service.php');
require_once(__DIR__ . '/classes/course_builder.php');

$cmid     = optional_param('cmid',     0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

// Resolve courseid early from cmid
if ($cmid && !$courseid) {
    $tmp      = get_coursemodule_from_id(false, $cmid, 0, false, MUST_EXIST);
    $courseid = (int)$tmp->course;
}

// ── Access: admin OR teacher with editing rights ──────────────────────────────
$is_admin = is_siteadmin();
if (!$is_admin && $courseid) {
    $ctx_chk = context_course::instance($courseid, IGNORE_MISSING);
    if (!$ctx_chk || !has_capability('moodle/course:manageactivities', $ctx_chk)) {
        throw new moodle_exception('nopermissions', 'error', '',
            'Solo los editores del curso pueden usar esta herramienta.');
    }
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/tau_course_creator_ai/improve.php'));
$PAGE->set_title('Editar recursos del curso');
$PAGE->set_heading('Editar recursos del curso');
$PAGE->set_pagelayout('standard');

// ── State ────────────────────────────────────────────────────────────────────
$cm_record         = null;
$mod_record        = null;
$modname           = '';
$modules_in_course = [];
$success_msg       = '';
$error_msg         = '';

// ── Load module for step 3 ───────────────────────────────────────────────────
if ($cmid) {
    $cm_record  = get_coursemodule_from_id(false, $cmid, 0, false, MUST_EXIST);
    $modname    = $cm_record->modname;
    $mod_record = $DB->get_record($modname, ['id' => $cm_record->instance]);
}

// ── Load ALL modules for step 2 ──────────────────────────────────────────────
if ($courseid && !$cmid) {
    $modinfo = get_fast_modinfo($courseid);
    foreach ($modinfo->get_cms() as $cm) {
        if ($cm->deletioninprogress) continue;
        $modules_in_course[] = (object)[
            'cmid'    => $cm->id,
            'modname' => $cm->modname,
            'name'    => $cm->name,
            'visible' => $cm->visible,
        ];
    }
}

// ── POST handler ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cmid && $mod_record) {
    require_sesskey();
    $action = optional_param('action', 'info', PARAM_ALPHA);

    try {
        // ── 1. Universal: save name + intro for ANY module type ───────────────
        if ($action === 'info') {
            $new_name  = trim(optional_param('name_edit',  '', PARAM_TEXT));
            $new_intro = optional_param('intro_edit', '', PARAM_RAW);  // HTML allowed
            $upd = new stdClass();
            $upd->id = $mod_record->id;
            if ($new_name !== '') {
                $upd->name = $new_name;
            }
            // Only update intro if the module table has that column
            // (virtually all Moodle module types do)
            $upd->intro       = $new_intro;
            $upd->introformat = FORMAT_HTML;

            // Type-specific extra fields saved in the same form
            if ($modname === 'url') {
                $new_url = trim(optional_param('externalurl', '', PARAM_RAW));
                if ($new_url !== '' && !preg_match('/^https?:\/\/.+/i', $new_url)) {
                    throw new Exception('La URL debe empezar con http:// o https://');
                }
                if ($new_url !== '') {
                    $upd->externalurl = $new_url;
                }
            }

            if (property_exists($mod_record, 'timemodified')) {
                $upd->timemodified = time();
            }

            $DB->update_record($modname, $upd);
            rebuild_course_cache($courseid);
            $mod_record = $DB->get_record($modname, ['id' => $mod_record->id]); // refresh

            $success_msg = 'Recurso actualizado correctamente. ' .
                '<a href="' . (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false) .
                '" style="color:#fff;text-decoration:underline;">Ver curso →</a>';

        // ── 2. Convert Page placeholder → real URL module ────────────────────
        } elseif ($action === 'seturl' && $modname === 'page') {
            $real_url = trim(optional_param('real_url', '', PARAM_RAW));
            $new_name = trim(optional_param('name_edit', '', PARAM_TEXT));
            if (empty($real_url) || !preg_match('/^https?:\/\/.+\..+/i', $real_url)) {
                throw new Exception('Ingresa una URL válida que empiece con https:// o http://');
            }
            require_once($CFG->dirroot . '/mod/url/lib.php');
            $url_mod_id = $DB->get_field('modules', 'id', ['name' => 'url'], MUST_EXIST);

            // Create the URL record
            $urec              = new stdClass();
            $urec->course      = $courseid;
            $urec->name        = $new_name ?: $mod_record->name;
            $urec->intro       = $mod_record->intro ?? '';
            $urec->introformat = FORMAT_HTML;
            $urec->externalurl = $real_url;
            $urec->display     = 0;
            $urec->timemodified = time();
            $urec->timecreated  = time();
            $new_url_id = $DB->insert_record('url', $urec);

            // Swap the course_module to point to the new URL record
            $DB->set_field('course_modules', 'module',   $url_mod_id, ['id' => $cmid]);
            $DB->set_field('course_modules', 'instance', $new_url_id, ['id' => $cmid]);

            // Delete the old placeholder page
            $DB->delete_records('page', ['id' => $mod_record->id]);
            rebuild_course_cache($courseid);

            $course_url  = (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false);
            $success_msg = '✓ Enlace activado. Los estudiantes pueden abrirlo ahora. ' .
                '<a href="' . $course_url . '" style="color:#fff;text-decoration:underline;">Ver curso →</a>';

            // Redirect so the form doesn't re-show for a deleted page
            redirect(new moodle_url('/local/tau_course_creator_ai/improve.php',
                ['courseid' => $courseid]), $success_msg, 3);

        // ── 3. Page only: AI presentation improvement ─────────────────────────
        } elseif ($action === 'ai' && $modname === 'page') {
            $instruction = optional_param('instruction', '', PARAM_TEXT);
            $material    = optional_param('material',    '', PARAM_TEXT);
            $language    = optional_param('language',    'es', PARAM_ALPHA);
            $num_slides  = min(10, max(3, (int)optional_param('num_slides', 7, PARAM_INT)));

            $topic = $mod_record->name;
            if ($instruction) {
                $topic .= "\n\nInstrucciones adicionales del docente: " . $instruction;
            }

            $section_id  = (int)$DB->get_field('course_modules', 'section', ['id' => $cmid]);
            $section_rec = $DB->get_record('course_sections', ['id' => $section_id]);
            $week_label  = 'Sección de Aprendizaje';
            if ($section_rec && $section_rec->name) {
                if (preg_match('/Sección\s+(\d+)/i', $section_rec->name, $m)) {
                    $week_label = 'Sección ' . $m[1];
                } elseif (preg_match('/Semana\s+(\d+)/i', $section_rec->name, $m)) {
                    $week_label = 'Sección ' . $m[1];
                } elseif (preg_match('/Módulo\s+(\d+)/i', $section_rec->name, $m)) {
                    $week_label = 'Módulo ' . $m[1];
                }
            }

            $ai        = new local_tau_course_creator_ai\ai_service();
            $result    = $ai->generate_presentation_content($topic, $num_slides, $material, $language);
            $ai_slides = $result['slides'] ?? [];

            if (empty($ai_slides)) {
                throw new Exception('La IA no generó diapositivas válidas. Intenta de nuevo.');
            }

            $teacher  = fullname($USER);
            $new_html = local_tau_course_creator_ai\course_builder::render_presentation_html(
                $mod_record->name, $teacher, $week_label, $ai_slides
            );

            $DB->set_field('page', 'content',      $new_html, ['id' => $mod_record->id]);
            $DB->set_field('page', 'timemodified', time(),    ['id' => $mod_record->id]);
            rebuild_course_cache($courseid);
            $mod_record = $DB->get_record('page', ['id' => $mod_record->id]);

            $success_msg = count($ai_slides) . ' diapositivas generadas y guardadas. ' .
                '<a href="' . (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false) .
                '" style="color:#fff;text-decoration:underline;">Ver curso →</a>';
        }

    } catch (Exception $e) {
        $error_msg = $e->getMessage();
    }
}

// ── Course list ───────────────────────────────────────────────────────────────
if ($is_admin) {
    $all_courses = $DB->get_records_menu('course', null, 'fullname ASC', 'id,fullname', 1);
} else {
    $enrolled    = enrol_get_users_courses($USER->id, true, ['id', 'fullname']);
    $all_courses = [];
    foreach ($enrolled as $c) {
        if ($c->id <= 1) continue;
        $ctx_c = context_course::instance($c->id);
        if (has_capability('moodle/course:manageactivities', $ctx_c)) {
            $all_courses[$c->id] = $c->fullname;
        }
    }
    asort($all_courses);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function tau_mod_icon(string $mn): string {
    return ['page'=>'📄','url'=>'🔗','resource'=>'📎','folder'=>'📁','forum'=>'💬',
            'quiz'=>'❓','assign'=>'📝','label'=>'🏷️','h5pactivity'=>'🎮',
            'scorm'=>'📦','wiki'=>'📖','glossary'=>'🗂️','feedback'=>'📋',
            'survey'=>'📊','choice'=>'🗳️','chat'=>'💭','lesson'=>'🎓',
            'bigbluebuttonbn'=>'🎥','zoom'=>'🎥','workshop'=>'🔧',
           ][$mn] ?? '📌';
}
function tau_mod_label(string $mn): string {
    return ['page'=>'Página','url'=>'URL','resource'=>'Archivo','folder'=>'Carpeta',
            'forum'=>'Foro','quiz'=>'Cuestionario','assign'=>'Tarea','label'=>'Etiqueta',
            'h5pactivity'=>'H5P','scorm'=>'SCORM','wiki'=>'Wiki','glossary'=>'Glosario',
            'feedback'=>'Retroalimentación','lesson'=>'Lección','workshop'=>'Taller',
            'choice'=>'Consulta','chat'=>'Chat',
           ][$mn] ?? ucfirst($mn);
}
function tau_mod_color(string $mn): string {
    return ['page'=>'#6366f1','url'=>'#f59e0b','resource'=>'#10b981','folder'=>'#8b5cf6',
            'forum'=>'#3b82f6','quiz'=>'#ef4444','assign'=>'#f97316','label'=>'#94a3b8',
            'h5pactivity'=>'#ec4899','scorm'=>'#14b8a6','wiki'=>'#84cc16',
            'glossary'=>'#06b6d4','feedback'=>'#8b5cf6','lesson'=>'#d946ef',
           ][$mn] ?? '#64748b';
}

echo $OUTPUT->header();
?>
<style>
.tau-imp-wrap{max-width:920px;margin:0 auto;padding:24px 16px;font-family:'Inter',-apple-system,sans-serif}
.tau-imp-card{background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,.08);margin-bottom:20px;border:1px solid #f0f0f0}
.tau-imp-title{font-size:1.4rem;font-weight:800;margin:0 0 4px;color:#1a1a2e}
.tau-imp-sub{font-size:.87rem;color:#888;margin:0 0 22px}

/* fields */
.tau-field{margin-bottom:14px}
.tau-field label{display:block;font-weight:700;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:5px}
.tau-field input[type=text],.tau-field input[type=url],.tau-field input[type=number],.tau-field select{
    width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;box-sizing:border-box;background:#fff}
.tau-field textarea{width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.9rem;resize:vertical;box-sizing:border-box}
.tau-field input:focus,.tau-field textarea:focus,.tau-field select:focus{border-color:#c62b3a;outline:none;box-shadow:0 0 0 3px rgba(198,43,58,.08)}
.tau-hint{font-size:.76rem;color:#94a3b8;margin-top:3px}

/* buttons */
.tau-btn-save{background:#1e293b;color:#fff;border:none;padding:11px 22px;border-radius:9px;font-size:.88rem;font-weight:700;cursor:pointer;transition:opacity .2s}
.tau-btn-save:hover{opacity:.85}
.tau-btn-ai{background:linear-gradient(135deg,#c62b3a,#8d182a);color:#fff;border:none;padding:11px 22px;border-radius:9px;font-size:.88rem;font-weight:700;cursor:pointer;transition:opacity .2s}
.tau-btn-ai:hover{opacity:.88}
.tau-btn-ai:disabled{opacity:.5;cursor:not-allowed}
.tau-btn-moodle{display:inline-flex;align-items:center;gap:6px;color:#475569;font-weight:600;font-size:.82rem;padding:9px 16px;border:1.5px solid #cbd5e1;border-radius:8px;text-decoration:none;transition:all .15s;white-space:nowrap}
.tau-btn-moodle:hover{background:#f1f5f9;color:#1e293b}
.tau-btn-outline{display:inline-flex;align-items:center;gap:5px;color:#c62b3a;font-weight:700;font-size:.82rem;padding:7px 13px;border:1.5px solid #c62b3a;border-radius:7px;text-decoration:none;transition:all .15s;white-space:nowrap}
.tau-btn-outline:hover{background:#c62b3a;color:#fff}

/* alerts */
.tau-success{background:#c62b3a;color:#fff;padding:14px 20px;border-radius:10px;margin-bottom:20px;font-weight:600}
.tau-error{background:#fee2e2;color:#991b1b;padding:14px 20px;border-radius:10px;margin-bottom:20px;font-weight:600;border:1px solid #fca5a5}

/* module list */
.tau-mod-list{list-style:none;padding:0;margin:0}
.tau-group-hdr{font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;padding:14px 0 5px;margin-top:6px}
.tau-mod-item{display:flex;align-items:center;gap:10px;padding:11px 0;border-bottom:1px solid #f4f4f4}
.tau-mod-item:last-child{border-bottom:none}
.tau-mod-badge{font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:20px;color:#fff;flex-shrink:0}
.tau-mod-name{flex:1;font-weight:600;font-size:.88rem;color:#1a1a2e;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tau-mod-hidden .tau-mod-name{opacity:.5;font-style:italic}
.tau-mod-actions{display:flex;gap:7px;flex-shrink:0}

/* section dividers */
.tau-section{border-top:2px solid #f0f0f0;margin-top:26px;padding-top:22px}
.tau-section-title{font-size:.95rem;font-weight:800;color:#1a1a2e;margin:0 0 16px;display:flex;align-items:center;gap:8px}
.tau-section-title span{font-size:1.1rem}

/* url preview */
.tau-url-box{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:9px;padding:12px 14px;font-size:.84rem;word-break:break-all;margin-bottom:14px}
.tau-url-box a{color:#c62b3a;font-weight:600;text-decoration:none}
.tau-url-box a:hover{text-decoration:underline}
.tau-url-bad{color:#991b1b;font-weight:700;font-size:.8rem;margin-top:5px}

/* two-column */
.tau-row{display:flex;gap:14px}
.tau-row .tau-field{flex:1;margin-bottom:0}

/* spinner */
.tau-spinner{display:none;text-align:center;padding:14px;color:#888;font-size:.88rem}

/* back link */
.tau-back{display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;color:#94a3b8;font-size:.84rem;text-decoration:none;font-weight:600}
.tau-back:hover{color:#c62b3a}

/* action bar */
.tau-action-bar{display:flex;align-items:center;gap:12px;margin-top:20px;flex-wrap:wrap}
</style>

<div class="tau-imp-wrap">

<?php if ($success_msg): ?>
<div class="tau-success">✅ <?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if ($error_msg): ?>
<div class="tau-error">⚠ <?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<?php
// ══════════════════════════════════════════════════════════════════════════════
//  STEP 3 — Edit a specific module
// ══════════════════════════════════════════════════════════════════════════════
if ($cmid && $mod_record):
    $back_url        = (new moodle_url('/local/tau_course_creator_ai/improve.php', ['courseid' => $courseid]))->out();
    $moodle_edit_url = (new moodle_url('/course/modedit.php', ['update' => $cmid, 'return' => 1]))->out();
    $form_url        = (new moodle_url('/local/tau_course_creator_ai/improve.php', ['cmid' => $cmid]))->out();
    $cur_intro       = $mod_record->intro ?? '';
    $cur_name        = $mod_record->name  ?? '';
?>
<a href="<?php echo $back_url; ?>" class="tau-back">← Volver a la lista</a>

<?php
// Detect URL-placeholder pages (previously broken URL resources)
$is_url_placeholder = ($modname === 'page')
    && (strpos($mod_record->content ?? '', 'Recurso Externo') !== false
        || strpos($mod_record->content ?? '', 'Nota para el docente') !== false);
?>

<?php if ($is_url_placeholder): ?>
<!-- ══ URL PLACEHOLDER: show SET-URL form prominently ══ -->
<div style="background:#fffbeb;border:2px solid #f59e0b;border-radius:16px;padding:28px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
        <span style="font-size:1.8rem;">🔗</span>
        <div>
            <div style="font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:#92400e;">Enlace externo pendiente</div>
            <div style="font-size:1.2rem;font-weight:800;color:#78350f;"><?php echo htmlspecialchars($cur_name); ?></div>
        </div>
    </div>
    <p style="color:#92400e;font-size:.9rem;margin:0 0 18px;line-height:1.6;">
        Este recurso está reservado para un enlace externo. Ingresa la URL real para que los estudiantes puedan abrirlo.
    </p>
    <form method="post" action="<?php echo $form_url; ?>">
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
        <input type="hidden" name="action"  value="seturl">
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <input type="url" name="real_url" required
                placeholder="https://drive.google.com/... o https://youtube.com/..."
                style="flex:1;min-width:260px;padding:12px 16px;border:2px solid #fbbf24;border-radius:10px;font-size:.95rem;background:#fff;outline:none;box-sizing:border-box;">
            <button type="submit"
                style="background:#d97706;color:#fff;border:none;padding:12px 24px;border-radius:10px;font-size:.95rem;font-weight:800;cursor:pointer;white-space:nowrap;">
                🔗 Activar enlace
            </button>
        </div>
        <div style="font-size:.78rem;color:#b45309;margin-top:8px;">
            Ejemplo: enlace de Google Drive, YouTube, PDF en línea, página web del reglamento, etc.
        </div>
    </form>
</div>
<?php endif; ?>

<div class="tau-imp-card">
    <!-- Header -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:22px;flex-wrap:wrap;">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                <span style="font-size:1.6rem;"><?php echo tau_mod_icon($modname); ?></span>
                <span style="font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;padding:3px 10px;border-radius:20px;color:#fff;background:<?php echo tau_mod_color($modname); ?>;"><?php echo tau_mod_label($modname); ?></span>
            </div>
            <div class="tau-imp-title"><?php echo htmlspecialchars($cur_name); ?></div>
        </div>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">⚙️ Editar en Moodle</a>
    </div>

    <!-- ═══ UNIVERSAL SECTION: name + description (ALL module types) ═══ -->
    <form method="post" action="<?php echo $form_url; ?>" id="form-info">
        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
        <input type="hidden" name="action"  value="info">

        <div class="tau-field">
            <label>Nombre del recurso</label>
            <input type="text" name="name_edit"
                value="<?php echo htmlspecialchars($cur_name); ?>"
                placeholder="Título visible en el curso">
        </div>

        <?php if ($modname === 'label'): ?>
        <!-- Label: intro IS the visible content -->
        <div class="tau-field">
            <label>Contenido de la etiqueta <span style="font-weight:400;color:#94a3b8;">(HTML permitido)</span></label>
            <textarea name="intro_edit" rows="6"
                placeholder="<h3>Título</h3><p>Descripción del módulo...</p>"><?php echo htmlspecialchars($cur_intro); ?></textarea>
            <div class="tau-hint">Este texto/HTML se muestra directamente en la página del curso como etiqueta.</div>
        </div>

        <?php elseif ($modname === 'url'): ?>
        <!-- URL: description + URL field together -->
        <div class="tau-field">
            <label>Descripción (opcional)</label>
            <textarea name="intro_edit" rows="3"
                placeholder="Describe brevemente el enlace..."><?php echo htmlspecialchars($cur_intro); ?></textarea>
        </div>

        <?php $cur_url = $mod_record->externalurl ?? ''; ?>
        <?php $url_ok  = preg_match('/^https?:\/\/.+/i', $cur_url); ?>

        <?php if (!$url_ok): ?>
        <div class="tau-url-box">
            <div class="tau-url-bad">⚠ URL actual inválida o vacía: <?php echo htmlspecialchars($cur_url ?: '(vacío)'); ?></div>
            <div style="margin-top:5px;font-size:.78rem;color:#64748b;">Por eso el recurso muestra el error en el curso. Corrígela abajo.</div>
        </div>
        <?php else: ?>
        <div class="tau-url-box">
            URL actual: <a href="<?php echo htmlspecialchars($cur_url); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($cur_url); ?> ↗</a>
        </div>
        <?php endif; ?>

        <div class="tau-field">
            <label>URL del enlace</label>
            <input type="url" name="externalurl"
                value="<?php echo htmlspecialchars($cur_url); ?>"
                placeholder="https://...">
            <div class="tau-hint">Debe empezar con https:// o http://</div>
        </div>

        <?php else: ?>
        <!-- All other types: description/intro -->
        <div class="tau-field">
            <label>Descripción / Introducción <span style="font-weight:400;color:#94a3b8;">(se muestra en el recurso)</span></label>
            <textarea name="intro_edit" rows="4"
                placeholder="Describe el contenido, objetivos o instrucciones de este recurso..."><?php echo htmlspecialchars($cur_intro); ?></textarea>
        </div>
        <?php endif; ?>

        <div class="tau-action-bar">
            <button type="submit" class="tau-btn-save" form="form-info">💾 Guardar cambios</button>
            <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">⚙️ Configuración avanzada en Moodle</a>
        </div>
    </form>

    <?php
    // ═══ TYPE-SPECIFIC EXTRAS ═══

    // ── PAGE: AI slide regeneration ──────────────────────────────────────────
    if ($modname === 'page'):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span>🤖</span> Regenerar presentación con IA</div>

        <form method="post" action="<?php echo $form_url; ?>" id="form-ai">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
            <input type="hidden" name="action"  value="ai">

            <div class="tau-field">
                <label>Instrucciones para la IA</label>
                <textarea name="instruction" rows="3"
                    placeholder="Ej: Agrega ejemplos con Python. Incluye datos reales. Hazlo más visual. Enfócate en casos prácticos."></textarea>
                <div class="tau-hint">Opcional — si lo dejas vacío la IA regenera desde el nombre del recurso.</div>
            </div>

            <div class="tau-field">
                <label>Material fuente <span style="font-weight:400;color:#94a3b8;">(opcional — .txt o pega texto)</span></label>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;">
                    <label style="display:inline-flex;align-items:center;gap:5px;background:#f4f4f4;border:1px solid #ddd;border-radius:7px;padding:6px 12px;cursor:pointer;font-size:.8rem;font-weight:700;color:#555;margin:0;">
                        📎 Cargar .txt
                        <input type="file" id="f-mat-file" accept=".txt" style="display:none;">
                    </label>
                    <span id="f-mat-name" style="font-size:.78rem;color:#94a3b8;"></span>
                </div>
                <textarea id="f-mat-text" name="material" rows="4"
                    placeholder="Pega aquí tu syllabus, guía o apuntes del tema (máx. 8000 caracteres)..."></textarea>
            </div>

            <div class="tau-row">
                <div class="tau-field">
                    <label>Número de diapositivas</label>
                    <input type="number" name="num_slides" value="7" min="3" max="10">
                </div>
                <div class="tau-field">
                    <label>Idioma</label>
                    <select name="language">
                        <option value="es" selected>Español</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>

            <div class="tau-action-bar" style="margin-top:16px;">
                <button type="submit" class="tau-btn-ai" id="btn-ai" form="form-ai">🤖 Regenerar presentación con IA</button>
            </div>
            <div class="tau-spinner" id="spinner-ai">⏳ Generando diapositivas… esto puede tomar 20-40 segundos.</div>
        </form>
    </div>

    <?php
    // ── RESOURCE / FOLDER: file info + redirect note ─────────────────────────
    elseif ($modname === 'resource' || $modname === 'folder'):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span>📎</span> Gestionar archivos</div>
        <p style="color:#64748b;font-size:.88rem;margin:0 0 14px;">
            Para subir un archivo nuevo, reemplazar el actual o gestionar los archivos de esta carpeta,
            usa el editor de Moodle (Selecciona botón de abajo).
        </p>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">📎 Gestionar archivos en Moodle</a>
    </div>

    <?php
    // ── FORUM: tips for configuration ────────────────────────────────────────
    elseif ($modname === 'forum'):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span>💬</span> Configuración del foro</div>
        <p style="color:#64748b;font-size:.88rem;margin:0 0 14px;">
            Puedes cambiar el nombre y descripción del foro con el formulario de arriba.
            Para configurar tipo de foro, permisos, suscripciones y calificaciones, usa Moodle.
        </p>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">💬 Configurar foro en Moodle</a>
    </div>

    <?php
    // ── ASSIGN: submission details ────────────────────────────────────────────
    elseif ($modname === 'assign'):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span>📝</span> Configuración de la tarea</div>
        <p style="color:#64748b;font-size:.88rem;margin:0 0 14px;">
            Edita el nombre e instrucciones de la tarea arriba.
            Para configurar fechas, tipos de entrega, calificación y retroalimentación, usa Moodle.
        </p>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">📝 Configurar tarea en Moodle</a>
    </div>

    <?php
    // ── QUIZ: questions note ──────────────────────────────────────────────────
    elseif ($modname === 'quiz'):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span>❓</span> Cuestionario</div>
        <p style="color:#64748b;font-size:.88rem;margin:0 0 14px;">
            Edita el nombre e introducción arriba. Para agregar/editar preguntas, configurar intentos,
            tiempo y calificación, usa el editor de Moodle.
        </p>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">❓ Editar cuestionario en Moodle</a>
    </div>

    <?php
    // ── H5P ───────────────────────────────────────────────────────────────────
    elseif ($modname === 'h5pactivity'):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span>🎮</span> Contenido H5P</div>
        <p style="color:#64748b;font-size:.88rem;margin:0 0 14px;">
            Para reemplazar o editar el contenido H5P interactivo, usa el editor de Moodle.
        </p>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">🎮 Editar H5P en Moodle</a>
    </div>

    <?php
    // ── Any other type: generic Moodle link ───────────────────────────────────
    elseif (!in_array($modname, ['label', 'url'])):
    ?>
    <div class="tau-section">
        <div class="tau-section-title"><span><?php echo tau_mod_icon($modname); ?></span> Opciones avanzadas</div>
        <p style="color:#64748b;font-size:.88rem;margin:0 0 14px;">
            Para opciones específicas de este tipo de recurso, usa el editor de Moodle.
        </p>
        <a href="<?php echo $moodle_edit_url; ?>" class="tau-btn-moodle">⚙️ Editar en Moodle</a>
    </div>
    <?php endif; ?>

</div><!-- .tau-imp-card -->

<?php
// ══════════════════════════════════════════════════════════════════════════════
//  STEP 2 — List ALL modules in the course
// ══════════════════════════════════════════════════════════════════════════════
elseif ($courseid):
    $course_rec = $DB->get_record('course', ['id' => $courseid], 'fullname');
    $back_url   = (new moodle_url('/local/tau_course_creator_ai/improve.php'))->out();
?>
<a href="<?php echo $back_url; ?>" class="tau-back">← Volver</a>

<div class="tau-imp-card">
    <div class="tau-imp-title">📋 Recursos del curso</div>
    <div class="tau-imp-sub"><?php echo htmlspecialchars($course_rec->fullname ?? ''); ?></div>

    <?php if (empty($modules_in_course)): ?>
    <p style="color:#94a3b8;">No se encontraron recursos en este curso.</p>
    <?php else: ?>

    <?php
    // Group by module type, priority order
    $groups = [];
    foreach ($modules_in_course as $mod) {
        $groups[$mod->modname][] = $mod;
    }
    $priority = ['page'=>0,'url'=>1,'label'=>2,'resource'=>3,'folder'=>4,
                 'forum'=>5,'assign'=>6,'quiz'=>7,'h5pactivity'=>8];
    uksort($groups, function($a, $b) use ($priority) {
        return ($priority[$a] ?? 99) <=> ($priority[$b] ?? 99);
    });
    ?>

    <ul class="tau-mod-list">
    <?php foreach ($groups as $gmod => $items): ?>
        <li class="tau-group-hdr">
            <?php echo tau_mod_icon($gmod); ?>
            <?php echo tau_mod_label($gmod); ?>s
            <span style="font-weight:400;font-size:.68rem;color:#cbd5e1;">(<?php echo count($items); ?>)</span>
        </li>
        <?php foreach ($items as $mod): ?>
        <li class="tau-mod-item <?php echo $mod->visible ? '' : 'tau-mod-hidden'; ?>">
            <span class="tau-mod-badge" style="background:<?php echo tau_mod_color($mod->modname); ?>;">
                <?php echo tau_mod_label($mod->modname); ?>
            </span>
            <span class="tau-mod-name">
                <?php echo htmlspecialchars($mod->name); ?>
                <?php if (!$mod->visible): ?><span style="font-size:.7rem;opacity:.7;"> (oculto)</span><?php endif; ?>
            </span>
            <div class="tau-mod-actions">
                <a href="<?php echo (new moodle_url('/local/tau_course_creator_ai/improve.php', ['cmid' => $mod->cmid]))->out(); ?>"
                   class="tau-btn-outline">✏️ Editar</a>
            </div>
        </li>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>

<?php
// ══════════════════════════════════════════════════════════════════════════════
//  STEP 1 — Course selector
// ══════════════════════════════════════════════════════════════════════════════
else:
?>
<div class="tau-imp-card">
    <div class="tau-imp-title">✏️ Editar recursos del curso</div>
    <div class="tau-imp-sub">
        Selecciona un curso para gestionar todos sus recursos: editar nombres, descripciones,
        URLs, etiquetas, y mejorar presentaciones con IA.
    </div>

    <form method="get" action="<?php echo (new moodle_url('/local/tau_course_creator_ai/improve.php'))->out(false); ?>">
        <div class="tau-field">
            <label>Curso</label>
            <select name="courseid">
                <option value="">— selecciona un curso —</option>
                <?php foreach ($all_courses as $cid => $cname): ?>
                <option value="<?php echo (int)$cid; ?>"><?php echo htmlspecialchars($cname); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="tau-action-bar" style="margin-top:16px;">
            <button type="submit" class="tau-btn-save">Ver recursos →</button>
        </div>
    </form>

    <?php if (empty($all_courses)): ?>
    <p style="margin-top:14px;color:#94a3b8;font-size:.85rem;">
        No tienes permisos de edición en ningún curso. Contacta al administrador.
    </p>
    <?php endif; ?>
</div>
<?php endif; ?>

</div><!-- .tau-imp-wrap -->

<script>
// File → textarea
var fi = document.getElementById('f-mat-file');
if (fi) fi.addEventListener('change', function () {
    var file = this.files[0]; if (!file) return;
    document.getElementById('f-mat-name').textContent = file.name;
    var r = new FileReader();
    r.onload = function (e) {
        var t = e.target.result;
        document.getElementById('f-mat-text').value = t.length > 8000 ? t.slice(0, 8000) : t;
    };
    r.readAsText(file, 'UTF-8');
});

// Spinner on AI submit
var formAi = document.getElementById('form-ai');
if (formAi) formAi.addEventListener('submit', function () {
    document.getElementById('btn-ai').disabled = true;
    document.getElementById('spinner-ai').style.display = 'block';
});
</script>

<?php echo $OUTPUT->footer(); ?>
