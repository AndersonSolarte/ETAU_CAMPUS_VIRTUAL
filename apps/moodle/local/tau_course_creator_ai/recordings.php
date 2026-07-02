<?php
require_once(__DIR__ . '/../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
$context = context_course::instance($courseid);

if (!is_siteadmin() && !has_capability('moodle/course:manageactivities', $context)) {
    throw new moodle_exception('nopermissions', 'error', '', 'Solo docentes editores y administradores pueden gestionar grabaciones.');
}

require_once(__DIR__ . '/classes/recordings_manager.php');

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_course_creator_ai/recordings.php', ['courseid' => $courseid]));
$PAGE->set_title('Gestionar grabaciones');
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('incourse');

\local_tau_course_creator_ai\recordings_manager::ensure_recordings_section($courseid);

$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();
    $action = required_param('action', PARAM_ALPHA);

    try {
        if ($action === 'create') {
            $sectionname = trim(required_param('sectionname', PARAM_TEXT));
            $title = trim(required_param('title', PARAM_TEXT));
            $videourl = trim(required_param('videourl', PARAM_RAW_TRIMMED));
            
            if ($title === '') {
                throw new moodle_exception('invalidparameter', 'error', '', 'Debes ingresar un nombre para la grabacion.');
            }
            if ($sectionname === '') {
                throw new moodle_exception('invalidparameter', 'error', '', 'Debes ingresar o seleccionar un módulo.');
            }
            if (!preg_match('~^https?://~i', $videourl)) {
                throw new moodle_exception('invalidparameter', 'error', '', 'El enlace debe iniciar con http:// o https://');
            }

            $sectionid = 0;
            $customgroup = $sectionname;

            foreach ($sections as $sec) {
                if ($sec['modulelabel'] === $sectionname) {
                    $sectionid = (int)$sec['id'];
                    $customgroup = '';
                    break;
                }
            }

            \local_tau_course_creator_ai\recordings_manager::create_recording($courseid, $sectionid, $title, $videourl, $USER->id, $customgroup);
            $notice = 'Grabacion agregada correctamente.';
        } else if ($action === 'delete') {
            $recordingid = required_param('recordingid', PARAM_INT);
            \local_tau_course_creator_ai\recordings_manager::delete_recording($recordingid, $courseid);
            $notice = 'Grabacion eliminada.';
        }
    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }
}

$sections = \local_tau_course_creator_ai\recordings_manager::get_section_picker_data($courseid);
$recordings = \local_tau_course_creator_ai\recordings_manager::get_recordings_for_course($courseid);
$grouped = [];
foreach ($recordings as $recording) {
    $grouped[$recording['modulelabel']][$recording['sectionlabel']][] = $recording;
}

echo $OUTPUT->header();
?>
<div class="tau-recordings-admin-page">
    <div class="tau-recordings-admin-header">
        <a class="btn btn-sm" style="background-color:#8d182a;color:#fff;border-radius:20px;font-weight:600;padding:6px 16px;margin-bottom:16px;display:inline-block;" href="<?php echo new moodle_url('/course/view.php', ['id' => $courseid]); ?>">&larr; Volver al curso</a>
        <h2>Subir grabación de clase</h2>
    </div>

    <?php if ($notice): ?>
        <div class="alert alert-success" style="border-radius:12px;"><?php echo s($notice); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger" style="border-radius:12px;"><?php echo s($error); ?></div>
    <?php endif; ?>

    <section class="tau-recordings-admin-card">
        <form method="post" class="tau-recordings-form">
            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
            <input type="hidden" name="action" value="create">

            <div class="tau-step">
                <div class="tau-step-number">1</div>
                <div class="tau-step-content">
                    <label>¿A qué módulo pertenece esta clase? <span class="tau-help-icon" data-tooltip="Busca uno de la lista o escribe un nombre nuevo">?</span></label>
                    
                    <style>
                    .tau-custom-combobox { position: relative; width: 100%; }
                    .tau-custom-combobox input { width: 100%; padding-right: 36px; border-radius: 8px; border: 1px solid #ccc; padding: 12px 16px; }
                    .tau-custom-combobox input:focus { border-color: #8d182a; outline: none; box-shadow: 0 0 0 3px rgba(141,24,42,0.1); }
                    .tau-combobox-arrow { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #888; font-size: 10px; transition: transform 0.2s; }
                    .tau-custom-combobox.open .tau-combobox-arrow { transform: translateY(-50%) rotate(180deg); }
                    .tau-combobox-list { position: absolute; top: calc(100% - 4px); left: 0; width: 100%; max-height: 250px; overflow-y: auto; background: #fff; border: 1px solid #ccc; border-top: none; border-radius: 0 0 8px 8px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); z-index: 1000; list-style: none; margin: 0; padding: 0; display: none; }
                    .tau-custom-combobox.open .tau-combobox-list { display: block; }
                    .tau-combobox-list li { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #eee; color: #333; font-size: 0.95rem; transition: all 0.2s; }
                    .tau-combobox-list li:last-child { border-bottom: none; }
                    .tau-combobox-list li:hover { background: #f8f9fa; color: #8d182a; padding-left: 20px; font-weight: 600; }
                    </style>

                    <div class="tau-custom-combobox" id="tau-combobox-container">
                        <input type="text" name="sectionname" id="tau-combobox-input" placeholder="-- Selecciona o escribe el módulo --" required autocomplete="off">
                        <div class="tau-combobox-arrow">&#x25BC;</div>
                        <ul class="tau-combobox-list" id="tau-combobox-list">
                            <?php foreach ($sections as $section): ?>
                                <li data-value="<?php echo s($section['modulelabel']); ?>"><?php echo s($section['modulelabel']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var container = document.getElementById('tau-combobox-container');
                        var input = document.getElementById('tau-combobox-input');
                        var list = document.getElementById('tau-combobox-list');
                        var items = list.querySelectorAll('li');

                        input.addEventListener('focus', function() {
                            container.classList.add('open');
                        });

                        input.addEventListener('input', function() {
                            var filter = input.value.toLowerCase();
                            var hasVisible = false;
                            items.forEach(function(item) {
                                if (item.innerText.toLowerCase().indexOf(filter) > -1) {
                                    item.style.display = '';
                                    hasVisible = true;
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                            if (hasVisible) {
                                container.classList.add('open');
                            } else {
                                container.classList.remove('open');
                            }
                        });

                        document.addEventListener('click', function(e) {
                            if (!container.contains(e.target)) {
                                container.classList.remove('open');
                            }
                        });

                        items.forEach(function(item) {
                            item.addEventListener('click', function() {
                                input.value = this.getAttribute('data-value');
                                container.classList.remove('open');
                                items.forEach(function(i) { i.style.display = ''; });
                            });
                        });
                    });
                    </script>
                </div>
            </div>

            <div class="tau-step">
                <div class="tau-step-number">2</div>
                <div class="tau-step-content">
                    <label>Título del video <span class="tau-help-icon" data-tooltip="Ej: Clase 03 - Introducción. Sé descriptivo.">?</span></label>
                    <input type="text" name="title" placeholder="Ej. Clase 03 - Diseño Urbano" required>
                </div>
            </div>

            <div class="tau-step">
                <div class="tau-step-number">3</div>
                <div class="tau-step-content">
                    <label>Pega el enlace (YouTube o Google Drive) <span class="tau-help-icon" data-tooltip="Asegúrate de que el enlace sea público o visible para los estudiantes">?</span></label>
                    <input type="url" name="videourl" placeholder="https://..." required>
                </div>
            </div>

            <div class="tau-step-submit">
                <button type="submit" class="btn btn-primary tau-btn-save">Guardar Grabación</button>
            </div>
        </form>
    </section>

    <div class="tau-recordings-admin-list">
        <h3>Grabaciones publicadas</h3>
        <?php if (empty($grouped)): ?>
            <div class="tau-recordings-empty-state">No hay videos subidos aún.</div>
        <?php else: ?>
            <?php foreach ($grouped as $modulelabel => $sectionsgroup): ?>
                <div class="tau-recordings-module-group">
                    <h4><?php echo s($modulelabel); ?></h4>
                    <?php foreach ($sectionsgroup as $sectionlabel => $items): ?>
                        <div class="tau-recordings-section-group">
                            <?php if ($sectionlabel !== ''): ?>
                                <div class="tau-section-title"><?php echo s($sectionlabel); ?></div>
                            <?php endif; ?>
                            <div class="tau-items-list">
                                <?php foreach ($items as $item): ?>
                                    <div class="tau-item-row">
                                        <div class="tau-item-name"><?php echo s($item['title']); ?></div>
                                        <form method="post" style="margin:0;">
                                            <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="recordingid" value="<?php echo (int)$item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta grabación?');">Eliminar</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<style>
.tau-recordings-admin-page { max-width: 640px; margin: 0 auto; padding: 20px 0 60px; font-family: system-ui, -apple-system, sans-serif; }
.tau-recordings-admin-header { margin-bottom: 24px; text-align: center; }
.tau-recordings-admin-header a { margin-bottom: 16px; border-radius: 20px; font-weight: 600; }
.tau-recordings-admin-header h2 { font-size: 1.75rem; font-weight: 800; color: #222; margin: 0; }
.tau-recordings-admin-card { background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 20px; padding: 32px 32px 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); margin-bottom: 32px; }
.tau-recordings-form { display: flex; flex-direction: column; gap: 24px; }
.tau-step { display: flex; gap: 16px; align-items: flex-start; }
.tau-step-number { width: 32px; height: 32px; border-radius: 50%; background: #fdeeee; color: #c62b3a; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; flex-shrink: 0; margin-top: 4px; }
.tau-step-content { flex-grow: 1; display: flex; flex-direction: column; gap: 8px; }
.tau-step-content label { font-size: 0.95rem; font-weight: 700; color: #333; margin: 0; display: inline-flex; align-items: center; }
.tau-help-icon { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; background: #eaeaea; color: #666; font-size: 12px; font-weight: 800; margin-left: 8px; cursor: help; position: relative; }
.tau-help-icon:hover { background: #8d182a; color: #fff; }
.tau-help-icon::after { content: attr(data-tooltip); position: absolute; bottom: 130%; left: 50%; transform: translateX(-50%); background: #222; color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: normal; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity 0.2s, bottom 0.2s; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.tau-help-icon::before { content: ''; position: absolute; bottom: calc(130% - 4px); left: 50%; transform: translateX(-50%); border-width: 5px; border-style: solid; border-color: #222 transparent transparent transparent; opacity: 0; pointer-events: none; transition: opacity 0.2s, bottom 0.2s; z-index: 1000; }
.tau-help-icon:hover::after, .tau-help-icon:hover::before { opacity: 1; bottom: 145%; }
.tau-step-content input, .tau-step-content select { border: 1px solid #ccc; border-radius: 10px; padding: 12px 14px; font-size: 1rem; width: 100%; transition: all 0.2s; background: #fafafa; }
.tau-step-content input:focus, .tau-step-content select:focus { border-color: #c62b3a; outline: none; background: #fff; box-shadow: 0 0 0 3px rgba(198,43,58,0.1); }
.tau-step-submit { padding-top: 16px; border-top: 1px solid rgba(0,0,0,0.05); text-align: right; }
.tau-btn-save { background: #8d182a; border-color: #8d182a; border-radius: 12px; padding: 12px 24px; font-weight: 700; font-size: 1.05rem; }
.tau-btn-save:hover { background: #6e1224; border-color: #6e1224; }
.tau-recordings-admin-list h3 { font-size: 1.25rem; font-weight: 800; color: #222; margin-bottom: 16px; padding-left: 8px; }
.tau-recordings-empty-state { padding: 24px; text-align: center; color: #666; background: #f9f9f9; border-radius: 12px; border: 1px dashed #ccc; }
.tau-recordings-module-group { background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 20px; margin-bottom: 16px; }
.tau-recordings-module-group h4 { margin: 0 0 16px; font-size: 1.1rem; font-weight: 800; color: #8d182a; }
.tau-recordings-section-group { margin-bottom: 16px; }
.tau-recordings-section-group:last-child { margin-bottom: 0; }
.tau-section-title { font-size: 0.85rem; font-weight: 700; color: #777; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
.tau-items-list { display: flex; flex-direction: column; gap: 8px; }
.tau-item-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #fafafa; border-radius: 10px; border: 1px solid #f0f0f0; }
.tau-item-name { font-weight: 600; font-size: 0.95rem; color: #333; }
</style>
<?php
echo $OUTPUT->footer();
