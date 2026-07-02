<?php
namespace local_tau_course_creator_ai;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir  . '/questionlib.php');

class course_builder {

    /** @var callable|null Progress callback fn(string $message) */
    private $on_progress;

    /** @var array The course blueprint */
    private array $blueprint = [];

    /** @var int|null ID of the created feedback module */
    private ?int $last_feedback_cmid = null;

    /** @var int|null ID of the last graded/interactive activity */
    private ?int $last_graded_cmid = null;

    public function __construct(?callable $on_progress = null) {
        $this->on_progress = $on_progress;
    }

    /**
     * Build a complete Moodle course from a blueprint array.
     * Returns the new course id.
     */
    public function build(array $blueprint, int $category_id): int {
        global $DB, $USER;

        if (isset($blueprint['sections'])) {
            $this->inject_evaluation_survey_to_blueprint($blueprint['sections']);
        }
        $this->blueprint = $blueprint;
        $this->progress('Creating course structure...');

        $course = $this->create_course($blueprint, $category_id);

        // Auto-enrol the creator (current user) in the newly created course.
        if (!empty($USER->id) && $USER->id > 0 && !isguestuser($USER)) {
            try {
                $enrol = enrol_get_plugin('manual');
                if ($enrol) {
                    $instance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
                    if (!$instance) {
                        $fields = array('courseid' => $course->id, 'enrol' => 'manual', 'status' => 0); // Active
                        $enrol->add_instance($course, $fields);
                        $instance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
                    }
                    if ($instance) {
                        $roleid = get_config('core', 'creatornewroleid');
                        if (empty($roleid)) {
                            $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
                            $roleid = $teacherrole ? $teacherrole->id : 3;
                        }
                        $enrol->enrol_user($instance, $USER->id, $roleid);
                        $this->progress('Auto-enrolled creator as course manager/teacher.');
                    }
                }
            } catch (\Throwable $e) {
                $this->progress('Auto-enrolment failed: ' . $e->getMessage());
                debugging('tau_course_creator_ai: auto-enrolment failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        $sections = $blueprint['sections'] ?? [];
        $num_sections = $this->get_visible_section_count($sections);

        // Ensure sections exist
        course_create_sections_if_missing($course, $num_sections);

        foreach ($sections as $idx => $section_data) {
            $this->build_section($course, $idx, $section_data);
        }

        // Rebuild section cache
        rebuild_course_cache($course->id, true);
        \local_tau_course_creator_ai\recordings_manager::ensure_recordings_section((int)$course->id);

        return $course->id;
    }

    /**
     * Apply a blueprint to an already created Moodle course.
     */
    public function apply_to_existing_course(int $courseid, array $blueprint): void {
        global $DB;

        if (isset($blueprint['sections'])) {
            $this->inject_evaluation_survey_to_blueprint($blueprint['sections']);
        }
        $this->blueprint = $blueprint;
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $sections = $blueprint['sections'] ?? [];
        if (empty($sections)) {
            return;
        }

        $this->progress('Applying predesigned structure to the created course...');
        course_create_sections_if_missing($course, $this->get_visible_section_count($sections));

        foreach ($sections as $idx => $sectiondata) {
            $this->build_section($course, $idx, $sectiondata);
        }

        rebuild_course_cache($course->id, true);
        \local_tau_course_creator_ai\recordings_manager::ensure_recordings_section((int)$course->id);
    }

    /**
     * Add activities directly to an existing section of a Moodle course.
     */
    public function add_activities_to_section(int $courseid, int $section_num, array $activities): void {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        
        // Ensure section exists (in case it is higher than current max sections)
        course_create_sections_if_missing($course, $section_num);

        $this->progress('Generating resources using AI inside section ' . $section_num . '...');

        foreach ($activities as $activity) {
            try {
                $this->create_activity($course, $section_num, $activity);
            } catch (\Throwable $e) {
                $title = $activity['title'] ?? ($activity['type'] ?? 'activity');
                $this->progress('Activity failed: ' . $title . ' -> ' . $e->getMessage());
                debugging('tau_course_creator_ai: activity creation failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        rebuild_course_cache($course->id, true);
    }

    // ── Private: course ────────────────────────────────────────────────────────

    private function create_course(array $blueprint, int $category_id): \stdClass {
        global $CFG;
        $name      = $blueprint['courseName']        ?? 'Nuevo Curso';
        $desc      = $blueprint['courseDescription'] ?? '';
        $num_sec   = $this->get_visible_section_count($blueprint['sections'] ?? []);

        $data                = new \stdClass();
        $data->fullname      = $name;
        $data->shortname     = !empty($blueprint['courseShortName'])
            ? $this->ensure_unique_shortname((string) $blueprint['courseShortName'])
            : $this->unique_shortname($name);
        $data->category      = $category_id;
        $data->summary       = $desc;
        $data->summaryformat = FORMAT_HTML;
        $data->format        = 'topics';
        $data->hiddensections = 0; // Show hidden sections in collapsed form for 'Próximamente' UI
        $data->numsections   = $num_sec;
        $data->newsitems     = 0;
        $data->visible       = 1;
        $data->lang          = '';
        $data->enablecompletion = 1;
        
        // Map custom field value
        $data->customfield_publish_apoyo_academico = !empty($blueprint['publishApoyo']) ? 1 : 0;

        $newcourse = create_course($data);
        
        // Force the gradebook to use Simple Weighted Mean of Grades (2) instead of Natural (13)
        require_once($CFG->libdir.'/gradelib.php');
        $category = \grade_category::fetch_course_category($newcourse->id);
        if ($category) {
            $category->aggregation = 2; // Simple weighted mean
            $category->update();
        }

        return $newcourse;
    }

    private function get_visible_section_count(array $sections): int {
        return max(count($sections) - 1, 0);
    }

    private function unique_shortname(string $name): string {
        global $DB;
        $base = preg_replace('/[^a-zA-Z0-9]/', '', strtolower(substr($name, 0, 18)));
        if (!$base) { $base = 'curso'; }
        $base .= '_' . date('ymd');
        $sn = $base;
        $i  = 2;
        while ($DB->record_exists('course', ['shortname' => $sn])) {
            $sn = $base . '_' . $i++;
        }
        return $sn;
    }

    private function ensure_unique_shortname(string $sn): string {
        global $DB;
        $sn = preg_replace('/[^a-zA-Z0-9_\-]/', '', $sn);
        if (!$sn) {
            return $this->unique_shortname('');
        }
        $base = $sn;
        $i    = 2;
        while ($DB->record_exists('course', ['shortname' => $sn])) {
            $sn = $base . '_' . $i++;
        }
        return $sn;
    }

    // ── Private: sections ─────────────────────────────────────────────────────

    private function build_section(\stdClass $course, int $idx, array $section): void {
        global $DB;
        $isfrontsection = $this->is_institutional_front_section($section);
        $sectionvisible = $this->should_section_start_visible($idx, $section);

        $rec = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $idx]);
        if ($rec) {
            $rec->name          = $section['title']   ?? '';
            $rec->summary       = $isfrontsection ? '' : ($section['summary'] ?? '');
            $rec->summaryformat = FORMAT_HTML;
            if (property_exists($rec, 'visible')) {
                $rec->visible = $sectionvisible ? 1 : 0;
            }
            if (property_exists($rec, 'availability')) {
                $rec->availability = null;
            }
            $DB->update_record('course_sections', $rec);
        }

        if (!$isfrontsection) {
            // Inyectar Banner Principal del Módulo
            $mod_title = htmlspecialchars($section['title'] ?? "Módulo $idx");
            $main_banner = '<div class="tau-banner-modulo"><span>' . $mod_title . '</span><div class="tau-banner-logo"></div></div>';
            $this->create_label($course, $idx, [
                'title' => 'Banner Principal',
                'description' => $main_banner
            ]);
        }

        $last_category = null;
        foreach (($section['activities'] ?? []) as $activity) {
            try {
                $current_category = $activity['category'] ?? null;
                if (!$isfrontsection && $current_category && $current_category !== $last_category) {
                    $banner_titles = [
                        'tema' => 'Temas',
                        'complementario' => 'Material Complementario',
                        'actividad' => 'Actividades',
                        'evaluacion' => 'Evaluación del curso'
                    ];
                    $banner_title = $banner_titles[$current_category] ?? 'Recursos';
                    $sub_banner = '<div class="tau-banner-separador tau-banner-' . $current_category . '"><span>' . $banner_title . '</span></div>';
                    
                    $this->create_label($course, $idx, [
                        'title' => 'Separador ' . $banner_title,
                        'description' => $sub_banner
                    ]);
                    $last_category = $current_category;
                }
                $this->create_activity($course, $idx, $activity);
            } catch (\Throwable $e) {
                $title = $activity['title'] ?? ($activity['type'] ?? 'activity');
                $this->progress('Activity failed: ' . $title . ' -> ' . $e->getMessage());
                // Log but continue with remaining activities
                debugging('tau_course_creator_ai: activity creation failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
    }

    // ── Private: activity dispatch ────────────────────────────────────────────

    public function migrate_legacy_duplicate_general_section(int $courseid): bool {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $sections = $DB->get_records('course_sections', ['course' => $courseid], 'section ASC');
        if (!$this->course_has_legacy_duplicate_general($sections)) {
            return false;
        }

        $indexed = [];
        foreach ($sections as $record) {
            $indexed[(int)$record->section] = $record;
        }
        $maxsection = max(array_keys($indexed));

        for ($sectionnum = 1; $sectionnum <= $maxsection; $sectionnum++) {
            if (!isset($indexed[$sectionnum], $indexed[$sectionnum - 1])) {
                continue;
            }

            $source = clone $indexed[$sectionnum];
            $target = clone $indexed[$sectionnum - 1];

            foreach ($this->parse_section_sequence((string)($source->sequence ?? '')) as $cmid) {
                $cm = $DB->get_record('course_modules', ['id' => $cmid], 'id,section', IGNORE_MISSING);
                if ($cm) {
                    $cm->section = $target->id;
                    $DB->update_record('course_modules', $cm);
                }
            }

            $target->name = $source->name;
            $target->summary = $source->summary;
            $target->summaryformat = $source->summaryformat;
            $target->sequence = $source->sequence;
            if (property_exists($target, 'visible') && property_exists($source, 'visible')) {
                $target->visible = $source->visible;
            }
            if (property_exists($target, 'availability') && property_exists($source, 'availability')) {
                $target->availability = $source->availability;
            }
            $DB->update_record('course_sections', $target);
        }

        if (isset($indexed[$maxsection])) {
            $last = clone $indexed[$maxsection];
            $last->name = '';
            $last->summary = '';
            $last->summaryformat = FORMAT_HTML;
            $last->sequence = '';
            if (property_exists($last, 'availability')) {
                $last->availability = null;
            }
            $DB->update_record('course_sections', $last);
        }

        course_get_format($course)->update_course_format_options([
            'numsections' => max($maxsection - 1, 0),
        ]);
        rebuild_course_cache($courseid, true);
        return true;
    }

    private function course_has_legacy_duplicate_general(array $sections): bool {
        $indexed = [];
        foreach ($sections as $section) {
            if (is_object($section) && isset($section->section)) {
                $indexed[(int)$section->section] = $section;
            }
        }

        if (!isset($indexed[0], $indexed[1], $indexed[2])) {
            return false;
        }

        $sectionzero = $indexed[0];
        $sectionone = $indexed[1];
        $sectiontwo = $indexed[2];

        $zeroempty = trim((string)($sectionzero->sequence ?? '')) === ''
            && trim(strip_tags((string)($sectionzero->summary ?? ''))) === '';
        if (!$zeroempty) {
            return false;
        }

        return $this->normalize_section_name((string)($sectionone->name ?? '')) === 'general'
            && $this->normalize_section_name((string)($sectiontwo->name ?? '')) === 'informacion general';
    }

    private function normalize_section_name(string $name): string {
        $name = \core_text::strtolower(trim($name));
        $name = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $name
        );
        return preg_replace('/\s+/', ' ', $name);
    }

    private function parse_section_sequence(string $sequence): array {
        if ($sequence === '') {
            return [];
        }
        return array_values(array_filter(array_map('intval', explode(',', $sequence))));
    }

    private function is_institutional_front_section(array $section): bool {
        $title = \core_text::strtolower(trim((string)($section['title'] ?? '')));
        return $title === 'general' || $title === 'información general' || $title === 'informacion general';
    }

    private function should_section_start_visible(int $idx, array $section): bool {
        if ($idx === 0 || $this->is_institutional_front_section($section)) {
            return true;
        }

        $visibleacademicsections = 0;
        foreach (($this->blueprint['sections'] ?? []) as $sectionindex => $sectiondata) {
            if ((int)$sectionindex === 0 || $this->is_institutional_front_section((array)$sectiondata)) {
                continue;
            }

            $visibleacademicsections++;
            if ((int)$sectionindex === $idx) {
                return $visibleacademicsections <= 1;
            }
        }

        return true;
    }

    private function create_activity(\stdClass $course, int $section, array $a): void {
        $type  = $a['type']  ?? 'label';
        $title = $a['title'] ?? 'Activity';

        $this->progress("Creating {$type}: {$title}");

        switch ($type) {
            case 'page':     $this->create_page($course, $section, $a);     break;
            case 'quiz':     $this->create_quiz($course, $section, $a);     break;
            case 'forum':    $this->create_forum($course, $section, $a);    break;
            case 'assign':   $this->create_assign($course, $section, $a);   break;
            case 'url':      $this->create_url($course, $section, $a);      break;
            case 'glossary': $this->create_glossary($course, $section, $a); break;
            case 'feedback': $this->create_feedback($course, $section, $a); break;
            case 'resource':
                if (!empty($a['uploadedfile']['content']) && !empty($a['uploadedfile']['name'])) {
                    $this->create_resource_file($course, $section, $a);
                    break;
                }
                // Check if title has presentation/slide/diapositiva
                $title_lower = mb_strtolower($title);
                if (strpos($title_lower, 'presentación') !== false ||
                    strpos($title_lower, 'presentacion') !== false ||
                    strpos($title_lower, 'diapositiva') !== false ||
                    strpos($title_lower, 'slide') !== false ||
                    strpos($title_lower, 'ppt') !== false) {
                    $this->create_presentation($course, $section, $a);
                } else {
                    $this->create_page($course, $section, $a);
                }
                break;
            case 'book':        $this->create_page($course, $section, $a);        break;
            case 'h5pactivity': $this->create_h5pactivity($course, $section, $a); break;
            default:            $this->create_label($course, $section, $a);       break;
        }

        global $DB;
        $new_cm = $DB->get_record('course_modules', ['course' => $course->id], 'id', 'id DESC', IGNORE_MULTIPLE);
        
        if ($new_cm) {
            // Track last graded activity to use as a condition for the feedback
            if (!in_array($type, ['label', 'feedback'])) {
                $this->last_graded_cmid = (int)$new_cm->id;
            }

            // If this is the feedback, and it requires the previous activity
            if ($type === 'feedback' && !empty($a['requires_previous']) && !empty($this->last_graded_cmid)) {
                $new_cm->availability = '{"op":"&","c":[{"type":"completion","cm":' . $this->last_graded_cmid . ',"e":1}],"showc":[true]}';
                $DB->update_record('course_modules', $new_cm);
            }
        }

        if (!empty($a['requires_feedback']) && !empty($this->last_feedback_cmid)) {
            if ($new_cm) {
                $new_cm->availability = '{"op":"&","c":[{"type":"completion","cm":' . $this->last_feedback_cmid . ',"e":1}],"showc":[true]}';
                $DB->update_record('course_modules', $new_cm);
            }
        }
    }

    // ── Private: activity creators ────────────────────────────────────────────

    private function create_page(\stdClass $course, int $section, array $a): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/page/lib.php');

        $content = $a['content'] ?? '';
        if ($this->is_inline_institutional_page($a, $content)) {
            $inline = $a;
            $inline['description'] = $content ?: ($a['description'] ?? '');
            $this->create_label($course, $section, $inline);
            return;
        }
        if (!$content) {
            $title_esc = htmlspecialchars($a['title']       ?? '', ENT_QUOTES, 'UTF-8');
            $desc_esc  = htmlspecialchars($a['description'] ?? '', ENT_QUOTES, 'UTF-8');
            $content   = '
<div style="font-family:\'Inter\',-apple-system,sans-serif;max-width:700px;margin:0 auto;padding:24px 16px;">
  <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border-radius:14px;padding:26px;border:1.5px solid #bae6fd;margin-bottom:20px;">
    <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#0369a1;margin-bottom:8px;">📄 Material de estudio</div>
    <h2 style="margin:0 0 12px;font-size:1.3rem;font-weight:800;color:#0c4a6e;">' . $title_esc . '</h2>
    ' . ($desc_esc ? '<p style="margin:0;color:#0369a1;font-size:.93rem;line-height:1.65;">' . $desc_esc . '</p>' : '') . '
  </div>
  <div style="background:#fff8f0;border-radius:12px;padding:18px;border:1.5px solid #fed7aa;">
    <div style="font-size:.8rem;font-weight:800;color:#c2410c;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">📌 Nota para el docente</div>
    <p style="margin:0;color:#9a3412;font-size:.87rem;line-height:1.6;">
      El docente debe completar esta página con el material de estudio correspondiente.
      Activa <strong>Modo de edición</strong> → ⚙️ → <strong>Editar ajustes</strong> para agregar el contenido.
    </p>
  </div>
</div>';
        }

        $mod                = new \stdClass();
        $mod->modulename    = 'page';
        $mod->name          = $a['title']       ?? 'Page';
        $mod->intro         = $a['description'] ?? '';
        $mod->introformat   = FORMAT_HTML;
$mod->content       = $content;
        $mod->contentformat = FORMAT_HTML;
        $mod->course        = $course->id;
        $mod->section       = $section;
        $mod->visible       = 1;
        $this->apply_introeditor($mod);

        create_module($mod);
    }

    private function is_inline_institutional_page(array $activity, string $content): bool {
        $title = \core_text::strtolower(trim((string)($activity['title'] ?? '')));
        if ($title !== 'bienvenida') {
            return false;
        }
        return $content !== '';
    }

    private function create_resource_file(\stdClass $course, int $section, array $a): void {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/resource/lib.php');

        $upload = $a['uploadedfile'] ?? null;
        $filename = clean_param($upload['name'] ?? 'documento.pdf', PARAM_FILE);
        if (!$filename) {
            $filename = 'documento.pdf';
        }
        if (!preg_match('/\.pdf$/i', $filename)) {
            $filename .= '.pdf';
        }

        $content = base64_decode((string)($upload['content'] ?? ''), true);
        if ($content === false || $content === '') {
            $this->create_page($course, $section, $a);
            return;
        }

        $draftitemid = file_get_unused_draft_itemid();
        $usercontext = \context_user::instance($USER->id ?? 2);
        $fs = get_file_storage();
        $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftitemid,
            'filepath'  => '/',
            'filename'  => $filename,
        ], $content);

        $mod                  = new \stdClass();
        $mod->modulename      = 'resource';
        $mod->name            = $a['title'] ?? 'Documento PDF';
        $mod->intro           = $a['description'] ?? '';
        $mod->introformat     = FORMAT_HTML;
        $mod->files           = $draftitemid;
        $mod->display         = 0;
        $mod->showsize        = 1;
        $mod->showtype        = 1;
        $mod->showdescription = 1;
        $mod->course          = $course->id;
        $mod->section         = $section;
        $mod->visible         = 1;
        $this->apply_introeditor($mod);

        create_module($mod);
    }

    private function create_forum(\stdClass $course, int $section, array $a): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $mod                = new \stdClass();
        $mod->modulename    = 'forum';
        $mod->name          = $a['title']       ?? 'Forum';
        $mod->intro         = $a['intro']        ?? ($a['description'] ?? '');
        $mod->introformat   = FORMAT_HTML;
        $mod->type          = $a['forumtype']    ?? 'general';
        $mod->course        = $course->id;
        $mod->section       = $section;
        $mod->visible       = 1;
        $this->apply_introeditor($mod);

        create_module($mod);
    }

    private function create_assign(\stdClass $course, int $section, array $a): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/lib.php');

        $mod                               = new \stdClass();
        $mod->modulename                   = 'assign';
        $mod->name                         = $a['title']       ?? 'Tarea / Entregable';
        $mod->intro                        = $a['description'] ?? 'Espacio para adjuntar y entregar tus actividades.';
        $mod->introformat                  = FORMAT_HTML;
        $mod->course                       = $course->id;
        $mod->section                      = $section;
        $mod->visible                      = 1;
        
        // Native Moodle assign settings
        $mod->submissiondrafts             = 0;
        $mod->requiresubmissionstatement   = 0;
        $mod->sendnotifications            = 0;
        $mod->sendlatenotifications       = 0;
        $mod->duedate                      = 0;
        $mod->cutoffdate                   = 0;
        $mod->gradingduedate               = 0;
        $mod->allowsubmissionsfromdate     = 0;
        $mod->teamsubmission               = 0;
        $mod->requireallteammemberssubmit  = 0;
        $mod->teamsubmissiongroupingid     = 0;
        $mod->nosubmissions                = 0;
        $mod->preventsubmissionnotingroup  = 0;
        $mod->alwaysshowdescription        = 1;
        $mod->markingworkflow              = 0;
        $mod->markingallocation            = 0;
        $mod->maxattempts                  = -1;
        $mod->attemptreopenmethod          = 'none';
        $mod->blindmarking                 = 0;
        $mod->hidegrader                   = 0;
        $mod->sendstudentnotifications     = 1;
        $mod->timemodified                 = time();
        $mod->completionsubmit             = 0;
        
        // Enable online text and file submission plugins:
        $mod->assignsubmission_onlinetext_enabled = 1;
        $mod->assignsubmission_file_enabled       = 1;
        $mod->assignfeedback_comments_enabled     = 1;
        $mod->assignfeedback_file_enabled         = 1;
        
        $mod->grade                        = 5;
        
        $this->apply_introeditor($mod);

        create_module($mod);
    }

    private function create_url(\stdClass $course, int $section, array $a): void {
        global $CFG;

        $title       = $a['title']       ?? 'Recurso de referencia';
        $description = $a['description'] ?? '';
        $raw_url     = trim($a['externalurl'] ?? '');

        // Only create a real URL module when the URL is genuinely valid
        $is_valid = !empty($raw_url)
            && preg_match('/^https?:\/\/.+\..+/i', $raw_url)
            && !in_array(parse_url($raw_url, PHP_URL_HOST), ['example.com', 'example.org', 'example.net']);

        if ($is_valid) {
            require_once($CFG->dirroot . '/mod/url/lib.php');
            $mod              = new \stdClass();
            $mod->modulename  = 'url';
            $mod->name        = $title;
            $mod->intro       = $description;
            $mod->introformat = FORMAT_HTML;
            $mod->externalurl = $raw_url;
            $mod->course      = $course->id;
            $mod->section     = $section;
            $mod->visible     = 1;
            $this->apply_introeditor($mod);
            create_module($mod);
            return;
        }

        // No valid URL — create a rich Page so students see useful content
        // and the teacher knows what link to add later.
        require_once($CFG->dirroot . '/mod/page/lib.php');

        $esc_title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $esc_desc  = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

        $content = '
<div style="font-family:\'Inter\',-apple-system,sans-serif;max-width:680px;margin:0 auto;padding:24px 16px;">

  <div style="background:linear-gradient(135deg,#fef9c3,#fef08a);border-radius:14px;padding:26px;border:1.5px solid #fbbf24;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
      <span style="font-size:2.2rem;">🔗</span>
      <div>
        <div style="font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#92400e;">Recurso Externo</div>
        <h2 style="margin:3px 0 0;font-size:1.25rem;font-weight:800;color:#78350f;">' . $esc_title . '</h2>
      </div>
    </div>
    ' . ($description ? '<p style="margin:0;color:#78350f;font-size:.93rem;line-height:1.65;">' . $esc_desc . '</p>' : '') . '
  </div>

  <div style="background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;padding:22px;margin-bottom:16px;">
    <h3 style="margin:0 0 10px;font-size:.95rem;font-weight:800;color:#1a1a2e;display:flex;align-items:center;gap:8px;">
      🔍 ¿Cómo encontrar este recurso?
    </h3>
    <p style="margin:0 0 14px;color:#475569;font-size:.9rem;line-height:1.6;">
      Busca en Google, Google Scholar o en la biblioteca digital de tu institución:
    </p>
    <div style="background:#f8fafc;border-radius:8px;padding:12px 16px;font-family:monospace;font-size:.88rem;color:#1e293b;border:1px solid #e2e8f0;">
      🔎 &quot;' . $esc_title . '&quot;
    </div>
  </div>

  <div style="background:#eff6ff;border-radius:12px;padding:18px;border:1.5px solid #bfdbfe;">
    <div style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#1d4ed8;margin-bottom:8px;">📌 Nota para el docente</div>
    <p style="margin:0;color:#1e40af;font-size:.87rem;line-height:1.6;">
      Este recurso fue generado como marcador para un enlace externo.
      Para agregar la URL real: activa <strong>Modo de edición</strong> → ⚙️ sobre este recurso
      → <strong>Editar ajustes</strong> → cambia el tipo de recurso a <em>URL</em> y pega el enlace.
    </p>
  </div>

</div>';

        $mod                = new \stdClass();
        $mod->modulename    = 'page';
        $mod->name          = $title;
        $mod->intro         = $description;
        $mod->introformat   = FORMAT_HTML;
        $mod->content       = $content;
        $mod->contentformat = FORMAT_HTML;
        $mod->course        = $course->id;
        $mod->section       = $section;
        $mod->visible       = 1;
        $this->apply_introeditor($mod);
        create_module($mod);
    }

    private function create_label(\stdClass $course, int $section, array $a): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/label/lib.php');

        $text = $a['intro'] ?? ($a['description'] ?? ($a['title'] ?? ''));

        $mod              = new \stdClass();
        $mod->modulename  = 'label';
        $mod->name        = $a['title'] ?? 'Label';
        $mod->intro       = $text;
        $mod->introformat = FORMAT_HTML;
        $mod->course      = $course->id;
        $mod->section     = $section;
        $mod->visible     = 1;
        $this->apply_introeditor($mod);

        create_module($mod);
    }

    private function create_glossary(\stdClass $course, int $section, array $a): void {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/glossary/lib.php');

        $mod                         = new \stdClass();
        $mod->modulename             = 'glossary';
        $mod->name                   = $a['title']       ?? 'Glosario del curso';
        $mod->intro                  = $a['description'] ?? '';
        $mod->introformat            = FORMAT_HTML;
        $mod->displayformat          = 'dictionary';
        $mod->mainglossary           = 0;
        $mod->showspecial            = 1;
        $mod->showalphabet           = 1;
        $mod->showall                = 1;
        $mod->allowduplicatedentries = 0;
        $mod->allowcomments          = 0;
        $mod->usedynalink            = 0;
        $mod->defaultapproval        = 1;
        $mod->maxbytes               = 0;
        $mod->maxattachments         = 1;
        $mod->assessed               = 0;
        $mod->course                 = $course->id;
        $mod->section                = $section;
        $mod->visible                = 1;
        $this->apply_introeditor($mod);

        $cm = create_module($mod);

        if ($cm && !empty($a['terms']) && is_array($a['terms'])) {
            $uid = $USER->id ?? 2;
            foreach ($a['terms'] as $term) {
                $entry                   = new \stdClass();
                $entry->course           = $course->id;
                $entry->glossaryid       = $cm->instance;
                $entry->userid           = $uid;
                $entry->concept          = substr($term['concept'] ?? 'Término', 0, 255);
                $entry->definition       = $term['definition'] ?? '';
                $entry->definitionformat = FORMAT_HTML;
                $entry->definitiontrust  = 0;
                $entry->attachment       = '';
                $entry->timecreated      = time();
                $entry->timemodified     = time();
                $entry->teacherentry     = 1;
                $entry->sourceglossaryid = 0;
                $entry->usedynalink      = 1;
                $entry->casesensitive    = 0;
                $entry->fullmatch        = 0;
                $entry->approved         = 1;
                $DB->insert_record('glossary_entries', $entry);
            }
        }
    }

    private function create_feedback(\stdClass $course, int $section, array $a): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $mod                     = new \stdClass();
        $mod->modulename         = 'feedback';
        $mod->name               = $a['title'] ?? 'Encuesta de satisfacción del curso';
        $mod->intro              = $a['description'] ?? 'Ayúdanos a mejorar este curso completando esta breve encuesta anónima.';
        $mod->introformat        = FORMAT_HTML;
        $mod->anonymous          = 1;
        $mod->email_notification = 0;
        $mod->multiple_submit    = 0;
        $mod->autonumbering      = 1;
        $mod->publish_stats      = 0;
        $mod->timeopen           = 0;
        $mod->timeclose          = 0;
        $mod->course             = $course->id;
        $mod->section            = $section;
        $mod->visible            = 1;
        $mod->page_after_submit            = '¡Gracias por completar la encuesta!';
        $mod->page_after_submitformat      = FORMAT_HTML;
        $mod->site_after_submit            = '';
        $mod->completionsubmit             = 1;
        $mod->completion                   = 2; // COMPLETION_TRACKING_AUTOMATIC
        $mod->timemodified                 = time();
        $this->apply_introeditor($mod);

        $cm = create_module($mod);
        if ($cm) {
            $this->last_feedback_cmid = $cm->coursemodule;
            if (!empty($cm->instance)) {
                $this->add_questions_to_feedback($cm->instance);
            }
        }
    }

    private function create_quiz(\stdClass $course, int $section, array $a): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/quiz/lib.php');

        $mod                        = new \stdClass();
        $mod->modulename            = 'quiz';
        $mod->name                  = $a['title']       ?? 'Quiz';
        $mod->intro                 = $a['description'] ?? '';
        $mod->introformat           = FORMAT_HTML;
        $mod->timeopen              = 0;
        $mod->timeclose             = 0;
        $mod->timelimit             = 0;
        $mod->preferredbehaviour    = 'deferredfeedback';
        $mod->attempts              = 0;
        $mod->grademethod           = 1;
        $mod->decimalpoints         = 2;
        $mod->questionsperpage      = 0;
        $mod->navmethod             = 'free';
        $mod->shuffleanswers        = 1;
        $mod->sumgrades             = 0;
        $mod->grade                 = 5;
        $mod->timecreated           = time();
        $mod->timemodified          = time();
        $mod->overduehandling       = 'autosubmit';
        $mod->graceperiod           = 0;
        $mod->canredoquestions      = 0;
        $mod->attemptonlast         = 0;
        $mod->questiondecimalpoints = -1;
        $mod->reviewattempt         = 65536;
        $mod->reviewcorrectness     = 65536;
        $mod->reviewmarks           = 65536;
        $mod->reviewspecificfeedback= 65536;
        $mod->reviewgeneralfeedback = 65536;
        $mod->reviewrightanswer     = 65536;
        $mod->reviewoverallfeedback = 65536;
        $mod->reviewmaxmarks        = 65536;
        $mod->password              = '';
        $mod->quizpassword          = '';
        $mod->subnet                = '';
        $mod->showuserpicture       = 0;
        $mod->showblocks            = 0;
        $mod->completionattemptsexhausted = 0;
        $mod->completionpass        = 0;
        $mod->completionminattempts = 0;
        $mod->allowofflineattempts  = 0;
        $mod->course                = $course->id;
        $mod->section               = $section;
        $mod->visible               = 1;
        $this->apply_introeditor($mod);

        $cm = create_module($mod);

        if (!empty($a['questions']) && $cm && $cm->instance) {
            $quiz = $DB->get_record('quiz', ['id' => $cm->instance]);
            if ($quiz) {
                $this->add_questions_to_quiz($course, $quiz, $a['questions']);
            }
        }
    }

    // ── Private: quiz question creation ──────────────────────────────────────

    private function add_questions_to_quiz(\stdClass $course, \stdClass $quiz, array $questions): void {
        global $DB;

        $context = \context_course::instance($course->id);

        // Get default question category for this course context
        $cat = $DB->get_record('question_categories', ['contextid' => $context->id, 'parent' => 0]);
        if (!$cat) {
            $cat            = new \stdClass();
            $cat->name      = 'Default for ' . $course->fullname;
            $cat->contextid = $context->id;
            $cat->info      = '';
            $cat->infoformat= FORMAT_HTML;
            $cat->parent    = 0;
            $cat->sortorder = 999;
            $cat->stamp     = make_unique_id_code();
            $cat->id        = $DB->insert_record('question_categories', $cat);
        }

        $slot      = 1;
        $sumgrades = 0;

        foreach ($questions as $q_data) {
            try {
                $qid = $this->insert_question($q_data, $cat);
                $this->add_question_to_quiz_slot($quiz->id, $qid, $slot, $context);
                $slot++;
                $sumgrades++;
            } catch (\Throwable $e) {
                debugging('tau_course_creator_ai: question insert failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }

        if ($sumgrades > 0) {
            $DB->set_field('quiz', 'sumgrades', $sumgrades, ['id' => $quiz->id]);
        }
    }

    private function insert_question(array $q_data, \stdClass $cat): int {
        global $DB, $USER;

        $q                    = new \stdClass();
        $q->category          = $cat->id;
        $q->parent            = 0;
        $q->name              = substr($q_data['question'] ?? 'Question', 0, 255);
        $q->questiontext      = $q_data['question'] ?? '';
        $q->questiontextformat= FORMAT_HTML;
        $q->generalfeedback   = $q_data['feedback'] ?? '';
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark       = 1.0;
        $q->penalty           = 0.3333333;
        $q->qtype             = 'multichoice';
        $q->length            = 1;
        $q->stamp             = make_unique_id_code();
        $q->timecreated       = time();
        $q->timemodified      = time();
        $q->createdby         = $USER->id ?? 2;
        $q->modifiedby        = $USER->id ?? 2;
        $q->id                = $DB->insert_record('question', $q);

        // Question bank entry
        $qbe                       = new \stdClass();
        $qbe->questioncategoryid   = $cat->id;
        $qbe->idnumber             = null;
        $qbe->id                   = $DB->insert_record('question_bank_entries', $qbe);

        // Question version
        $qv                    = new \stdClass();
        $qv->questionbankentryid = $qbe->id;
        $qv->version           = 1;
        $qv->questionid        = $q->id;
        $qv->status            = 'ready';
        $DB->insert_record('question_versions', $qv);

        // Multichoice answers
        $answers  = $q_data['answers'] ?? ['Option A', 'Option B', 'Option C', 'Option D'];
        $correct  = (int)($q_data['correct'] ?? 0);
        foreach ($answers as $i => $text) {
            $ans                  = new \stdClass();
            $ans->question        = $q->id;
            $ans->answer          = $text;
            $ans->answerformat    = FORMAT_HTML;
            $ans->fraction        = ($i === $correct) ? 1.0 : 0.0;
            $ans->feedback        = '';
            $ans->feedbackformat  = FORMAT_HTML;
            $DB->insert_record('question_answers', $ans);
        }

        // Multichoice options
        $mc                                   = new \stdClass();
        $mc->questionid                       = $q->id;
        $mc->layout                           = 0;
        $mc->answers                          = null;
        $mc->single                           = 1;
        $mc->shuffleanswers                   = 1;
        $mc->correctfeedback                  = 'Your answer is correct.';
        $mc->correctfeedbackformat            = FORMAT_HTML;
        $mc->partiallycorrectfeedback         = 'Your answer is partially correct.';
        $mc->partiallycorrectfeedbackformat   = FORMAT_HTML;
        $mc->incorrectfeedback                = 'Your answer is incorrect.';
        $mc->incorrectfeedbackformat          = FORMAT_HTML;
        $mc->answernumbering                  = 'abc';
        $mc->shownumcorrect                   = 0;
        $DB->insert_record('qtype_multichoice_options', $mc);

        return $q->id;
    }

    private function add_question_to_quiz_slot(int $quiz_id, int $question_id, int $slot, \context_course $context): void {
        global $DB;

        // quiz_slots record
        $slot_rec                 = new \stdClass();
        $slot_rec->quizid         = $quiz_id;
        $slot_rec->slot           = $slot;
        $slot_rec->page           = 1;
        $slot_rec->requireprevious= 0;
        $slot_rec->maxmark        = 1.0;
        if ($DB->get_manager()->field_exists('quiz_slots', 'displaynumber')) {
            $slot_rec->displaynumber = (string)$slot;
        }
        $slot_id = $DB->insert_record('quiz_slots', $slot_rec);

        // question_references: link slot → question bank entry
        if ($DB->get_manager()->table_exists('question_references')) {
            $qv = $DB->get_record('question_versions', ['questionid' => $question_id], 'questionbankentryid', IGNORE_MISSING);
            if ($qv) {
                $ref                    = new \stdClass();
                $ref->usingcontextid    = $context->id;
                $ref->component         = 'mod_quiz';
                $ref->questionarea      = 'slot';
                $ref->itemid            = $slot_id;
                $ref->questionbankentryid = $qv->questionbankentryid;
                $ref->version           = null;
                $DB->insert_record('question_references', $ref);
            }
        }
    }

    private function create_h5pactivity(\stdClass $course, int $section, array $a): void {
        global $DB;

        // If mod_h5pactivity table doesn't exist (H5P not installed), fall back to a
        // labelled page placeholder so the blueprint deploy never hard-fails.
        if (!$DB->get_manager()->table_exists('h5pactivity')) {
            $h5p_type    = $a['h5p_type']        ?? 'H5P';
            $import_note = $a['h5p_import_note'] ?? 'Importe el archivo .h5p desde el Banco de Contenido.';
            $a['content'] = '<div class="alert alert-warning" style="border-left:4px solid #f0a500;padding:16px;border-radius:6px;">'
                . '<strong>Actividad H5P pendiente de importación</strong><br>'
                . '<em>Tipo:</em> ' . htmlspecialchars($h5p_type) . '<br>'
                . '<em>Instrucción:</em> ' . htmlspecialchars($import_note)
                . '</div>'
                . ($a['content'] ?? '');
            $this->create_page($course, $section, $a);
            return;
        }

        // Auto-generate H5P content via AI when the blueprint requests it.
        if (!empty($a['ai_generate']) && !empty($a['h5p_type'])) {
            $course_context = \context_course::instance($course->id);
            try {
                $generator = new \local_tau_course_creator_ai\h5p\h5p_generator($this->on_progress);
                $cb_id     = $generator->generate_from_spec($a, $course_context);
                if ($cb_id > 0) {
                    $a['contentbank_id'] = $cb_id;
                }
            } catch (\Throwable $e) {
                // Generation failure is non-fatal: the module is still created
                // as a placeholder for manual H5P upload.
                $this->progress('H5P auto-generation failed (' . $e->getMessage() . ') — placeholder created.');
            }
        }

        $mod                 = new \stdClass();
        $mod->modulename     = 'h5pactivity';
        $mod->name           = $a['title']       ?? 'Actividad H5P';
        $mod->intro          = $a['description'] ?? '';
        $mod->introformat    = FORMAT_HTML;
        $mod->course         = $course->id;
        $mod->section        = $section;
        $mod->visible        = 1;
        $mod->grade          = (int) ($a['grade'] ?? 5);
        $mod->displayoptions = 15;  // show title + description + copyright + icon
        $mod->enabletracking = 1;   // record xAPI statements for gradebook
        $mod->grademethod    = 1;   // highest grade attempt
        $mod->timecreated    = time();
        $mod->timemodified   = time();
        $this->apply_introeditor($mod);

        $cm = create_module($mod);

        if ($cm && !empty($a['contentbank_id'])) {
            $cb_id    = (int) $a['contentbank_id'];
            $cm_ctx   = \context_module::instance($cm->id);
            $cb_ctx   = \context_course::instance($course->id);
            $publisher = new \local_tau_course_creator_ai\h5p\h5p_publisher();

            // Try native contentbankentryid link first; fall back to file copy.
            $linked = $publisher->link_to_activity($cb_id, (int) $cm->instance, $cb_ctx, $cm_ctx);
            if (!$linked) {
                // Fallback to the original file-copy method.
                $this->link_h5p_from_contentbank((int) $cm->instance, $cb_id, $course);
            }
        }
    }

    /**
     * Links an existing Content Bank H5P item to an h5pactivity instance.
     *
     * Tables involved:
     *   {contentbank_content}  — Content Bank registry (contenttype = 'contenttype_h5p')
     *   {h5p}                  — Core H5P content (instanceid from contentbank_content)
     *   {files}                — Moodle file records; source filearea = contentbank / public
     *                            target filearea  = mod_h5pactivity / package
     *
     * This method copies the .h5p file from the Content Bank file area into the
     * h5pactivity module file area so Moodle can deploy and render the content.
     *
     * @param int       $h5pactivity_id  ID of the newly created h5pactivity record
     * @param int       $contentbank_id  ID of the contentbank_content record
     * @param \stdClass $course          Course record
     */
    private function link_h5p_from_contentbank(int $h5pactivity_id, int $contentbank_id, \stdClass $course): void {
        global $DB;

        $cb = $DB->get_record('contentbank_content', ['id' => $contentbank_id], '*', IGNORE_MISSING);
        if (!$cb || $cb->contenttype !== 'contenttype_h5p') {
            debugging(
                "tau_course_creator_ai: contentbank_content id={$contentbank_id} not found or not H5P type.",
                DEBUG_DEVELOPER
            );
            return;
        }

        try {
            $fs          = get_file_storage();
            $cb_context  = \context::instance_by_id($cb->contextid, IGNORE_MISSING);
            if (!$cb_context) {
                return;
            }

            // The Content Bank stores the raw .h5p under component=contentbank, filearea=public
            $cb_files = $fs->get_area_files($cb_context->id, 'contentbank', 'public', $cb->id, 'itemid', false);
            if (empty($cb_files)) {
                return;
            }
            $source_file = reset($cb_files);

            // Target: mod_h5pactivity uses component=mod_h5pactivity, filearea=package, itemid=0
            $cm         = get_coursemodule_from_instance('h5pactivity', $h5pactivity_id, $course->id);
            $mod_context = \context_module::instance($cm->id);

            $fs->delete_area_files($mod_context->id, 'mod_h5pactivity', 'package');
            $fs->create_file_from_storedfile([
                'contextid' => $mod_context->id,
                'component' => 'mod_h5pactivity',
                'filearea'  => 'package',
                'itemid'    => 0,
                'filepath'  => '/',
                'filename'  => $source_file->get_filename(),
            ], $source_file);

            // Trigger H5P deployment so the content is rendered on first load
            $file_record = $fs->get_file(
                $mod_context->id, 'mod_h5pactivity', 'package', 0, '/', $source_file->get_filename()
            );
            if ($file_record) {
                $h5p_factory = new \core_h5p\factory();
                \core_h5p\api::create_content_from_pluginfile_url(
                    \moodle_url::make_pluginfile_url(
                        $mod_context->id, 'mod_h5pactivity', 'package', 0, '/', $source_file->get_filename()
                    )->out(),
                    $file_record,
                    $h5p_factory,
                    new \core_h5p\core($h5p_factory)
                );
            }
        } catch (\Throwable $e) {
            debugging('tau_course_creator_ai: H5P Content Bank link failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    private function create_presentation(\stdClass $course, int $section, array $a): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/page/lib.php');

        $title           = $a['title'] ?? 'Presentación de apoyo';
        $desc            = $a['description'] ?? '';
        $teacher         = $this->blueprint['teacherName'] ?? 'Docente de la Institución';
        $language        = $this->blueprint['language'] ?? 'es';
        $source_material = $this->blueprint['sourceMaterial'] ?? '';

        // Derive section label from the parent section record name
        $section_rec = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $section]);
        $sec_title   = $section_rec ? ($section_rec->name ?: '') : '';
        $week_label  = 'Sección de Aprendizaje';
        if (preg_match('/Sección\s+(\d+)/i', $sec_title, $matches)) {
            $week_label = 'Sección ' . $matches[1];
        } elseif (preg_match('/Semana\s+(\d+)/i', $sec_title, $matches)) {
            $week_label = 'Sección ' . $matches[1];
        } elseif (preg_match('/Módulo\s+(\d+)/i', $sec_title, $matches)) {
            $week_label = 'Módulo ' . $matches[1];
        }

        // Use the section title as topic — it has the real subject matter
        $ai_slides = [];
        try {
            $ai    = new ai_service();
            $topic = $sec_title ?: $title;
            if ($desc && mb_stripos($topic, $desc) === false) {
                $topic .= ': ' . $desc;
            }
            $result = $ai->generate_presentation_content($topic, 7, $source_material, $language);
            if (!empty($result['slides']) && is_array($result['slides'])) {
                $ai_slides = $result['slides'];
            }
        } catch (\Exception $e) {
            // Fallback handled inside render_presentation_html
        }

        $content = self::render_presentation_html($title, $teacher, $week_label, $ai_slides, $desc);


        $mod                = new \stdClass();
        $mod->modulename    = 'page';
        $mod->name          = $title;
        $mod->intro         = $desc;
        $mod->introformat   = FORMAT_HTML;
        $mod->content       = $content;
        $mod->contentformat = FORMAT_HTML;
        $mod->course        = $course->id;
        $mod->section       = $section;
        $mod->visible       = 1;
        $this->apply_introeditor($mod);

        create_module($mod);
    }

    // ── Presentation HTML renderer (shared by create_presentation + improve.php) ──

    /**
     * Build the complete HTML string for an interactive presentation page.
     * Used both when creating courses and when improving existing presentations.
     *
     * @param  string $title        Activity/page title shown on the cover slide
     * @param  string $teacher      Teacher name shown on the cover
     * @param  string $week_label   Section badge text (e.g. "Sección 2")
     * @param  array  $ai_slides    Slides from generate_presentation_content(); empty = use static fallback
     * @param  string $fallback_desc Description used only when $ai_slides is empty
     * @return string Full HTML (slider + CSS + JS)
     */
    public static function render_presentation_html(
        string $title,
        string $teacher,
        string $week_label,
        array  $ai_slides,
        string $fallback_desc = ''
    ): string {
        $uid = uniqid();

        $cover_grads = [
            'linear-gradient(135deg,#667eea 0%,#764ba2 100%)',
            'linear-gradient(135deg,#f093fb 0%,#f5576c 100%)',
            'linear-gradient(135deg,#11998e 0%,#38ef7d 100%)',
            'linear-gradient(135deg,#5ee7df 0%,#b490ca 100%)',
            'linear-gradient(135deg,#fa709a 0%,#fee140 100%)',
        ];
        $grad_cover = $cover_grads[array_rand($cover_grads)];

        $content_bgs = [
            ['bg' => 'linear-gradient(135deg,#0f2027 0%,#203a43 50%,#2c5364 100%)', 'dark' => true],
            ['bg' => 'linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%)',              'dark' => false],
            ['bg' => 'linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%)', 'dark' => true],
            ['bg' => 'linear-gradient(135deg,#e8f5e9 0%,#c8e6c9 100%)',              'dark' => false],
            ['bg' => 'linear-gradient(135deg,#fff3e0 0%,#ffe0b2 100%)',              'dark' => false],
            ['bg' => 'linear-gradient(135deg,#e3f2fd 0%,#bbdefb 100%)',              'dark' => false],
            ['bg' => 'linear-gradient(135deg,#f3e5f5 0%,#e1bee7 100%)',              'dark' => false],
        ];

        // Build content slides HTML
        $slides_html = '';
        if (!empty($ai_slides)) {
            foreach ($ai_slides as $idx => $sl) {
                $theme  = $content_bgs[$idx % count($content_bgs)];
                $dark   = $theme['dark'];
                $bg     = $theme['bg'];
                $tc     = $dark ? '#fff' : '#1a1a2e';
                $hc     = $dark ? '#fff' : '#c62b3a';
                $kc_bg  = $dark ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.06)';
                $kc_col = $dark ? '#fff' : '#c62b3a';
                $pc     = $dark ? 'rgba(255,255,255,0.85)' : '#495057';
                $dot_c  = $dark ? '#00d2ff' : '#c62b3a';

                $heading = htmlspecialchars($sl['heading'] ?? 'Contenido');
                $kicker  = htmlspecialchars($sl['kicker']  ?? 'Concepto');
                $icon    = htmlspecialchars($sl['icon']    ?? '📚');
                $body    = htmlspecialchars($sl['content'] ?? '');

                $pts = '';
                foreach ((array)($sl['points'] ?? []) as $pt) {
                    $pts .= '<li><strong style="color:' . $dot_c . ';">' . htmlspecialchars($pt) . '</strong></li>';
                }

                $slides_html .=
                    '<div class="tau-slide" style="background:' . $bg . ';color:' . $tc . ';">' .
                    '<div class="tau-slide-content">' .
                    '<div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;">' .
                    '<span style="font-size:2rem;">' . $icon . '</span>' .
                    '<span class="tau-slide-kicker" style="background:' . $kc_bg . ';color:' . $kc_col . ';">' . $kicker . '</span>' .
                    '</div>' .
                    '<h2 class="tau-slide-subtitle" style="color:' . $hc . ';">' . $heading . '</h2>' .
                    '<p style="font-size:1rem;line-height:1.6;color:' . $pc . ';margin-bottom:16px;">' . $body . '</p>' .
                    ($pts ? '<ul class="tau-slide-list">' . $pts . '</ul>' : '') .
                    '</div></div>';
            }
        } else {
            // Static fallback (2 slides)
            $slides_html =
                '<div class="tau-slide" style="background:linear-gradient(135deg,#0f2027 0%,#203a43 50%,#2c5364 100%);color:#fff;">' .
                '<div class="tau-slide-content">' .
                '<span class="tau-slide-kicker">Orientación</span>' .
                '<h2 class="tau-slide-subtitle">Objetivos de la Sección</h2>' .
                '<ul class="tau-slide-list">' .
                '<li><strong style="color:#00d2ff;">Comprender</strong> los fundamentos de ' . htmlspecialchars($title) . '.</li>' .
                '<li><strong style="color:#00d2ff;">Analizar</strong> casos prácticos aplicados.</li>' .
                '<li><strong style="color:#00d2ff;">Implementar</strong> soluciones efectivas.</li>' .
                '</ul></div></div>' .
                '<div class="tau-slide" style="background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);color:#1a1a2e;">' .
                '<div class="tau-slide-content">' .
                '<span class="tau-slide-kicker" style="background:rgba(0,0,0,0.06);color:#c62b3a;">Fundamentos</span>' .
                '<h2 class="tau-slide-subtitle" style="color:#c62b3a;">Temas Clave</h2>' .
                '<div style="background:#fff;padding:25px;border-radius:15px;box-shadow:0 8px 30px rgba(0,0,0,0.06);margin-top:15px;">' .
                '<p style="font-size:1.1rem;line-height:1.6;color:#495057;margin:0;">' . htmlspecialchars($fallback_desc ?: $title) . '</p>' .
                '</div></div></div>';
        }

        $total = 1 + (empty($ai_slides) ? 2 : count($ai_slides)) + 1;

        return '
<div class="tau-slider-wrap" id="tau-slider-' . $uid . '">
    <div class="tau-slides-container">
        <div class="tau-slide active" style="background:' . $grad_cover . ';color:#fff;">
            <div class="tau-slide-content tau-slide-cover">
                <div class="tau-slide-header">
                    <span class="tau-slide-kicker">' . htmlspecialchars($week_label) . '</span>
                    <h1 class="tau-slide-title">' . htmlspecialchars($title) . '</h1>
                    <p class="tau-slide-author">Docente: ' . htmlspecialchars($teacher) . '</p>
                </div>
                <div class="tau-slide-art"><div class="tau-slide-icon">✨</div></div>
            </div>
        </div>
        ' . $slides_html . '
        <div class="tau-slide" style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);color:#fff;">
            <div class="tau-slide-content">
                <span class="tau-slide-kicker">Metodología</span>
                <h2 class="tau-slide-subtitle">Ruta Pedagógica de la Sección</h2>
                <p style="font-size:1.1rem;opacity:.9;">Sigue este orden para garantizar un excelente desempeño académico:</p>
                <div style="display:flex;gap:15px;margin-top:20px;">
                    <div style="flex:1;background:rgba(255,255,255,.1);padding:18px;border-radius:12px;text-align:center;">
                        <span style="font-size:1.8rem;display:block;margin-bottom:8px;">📖</span>
                        <strong style="display:block;font-size:.95rem;">1. Materiales</strong>
                        <span style="font-size:.8rem;opacity:.8;">Estudia los recursos.</span>
                    </div>
                    <div style="flex:1;background:rgba(255,255,255,.1);padding:18px;border-radius:12px;text-align:center;">
                        <span style="font-size:1.8rem;display:block;margin-bottom:8px;">💬</span>
                        <strong style="display:block;font-size:.95rem;">2. Debate</strong>
                        <span style="font-size:.8rem;opacity:.8;">Participa en los foros.</span>
                    </div>
                    <div style="flex:1;background:rgba(255,255,255,.1);padding:18px;border-radius:12px;text-align:center;">
                        <span style="font-size:1.8rem;display:block;margin-bottom:8px;">📝</span>
                        <strong style="display:block;font-size:.95rem;">3. Entregable</strong>
                        <span style="font-size:.8rem;opacity:.8;">Completa tus talleres.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tau-slider-controls">
        <button type="button" class="tau-slider-btn" onclick="tauPrevSlide(\'' . $uid . '\')">❮ Anterior</button>
        <span class="tau-slider-indicator" id="tau-ind-' . $uid . '">Diapositiva 1 de ' . $total . '</span>
        <button type="button" class="tau-slider-btn" onclick="tauNextSlide(\'' . $uid . '\')">Siguiente ❯</button>
    </div>
</div>
<style>
.tau-slider-wrap{font-family:\'Outfit\',\'Inter\',-apple-system,sans-serif;border-radius:20px;overflow:hidden;box-shadow:0 16px 40px rgba(0,0,0,.12);background:#fff;margin:24px 0;position:relative;user-select:none;border:1px solid rgba(0,0,0,.06)}
.tau-slides-container{height:420px;position:relative}
.tau-slide{position:absolute;inset:0;opacity:0;visibility:hidden;transition:opacity .4s ease,visibility .4s ease;display:flex;align-items:center;justify-content:center;padding:40px 60px}
.tau-slide.active{opacity:1;visibility:visible}
.tau-slide-content{width:100%;max-width:800px}
.tau-slide-cover{display:flex;justify-content:space-between;align-items:center;gap:30px}
.tau-slide-header{flex:1}
.tau-slide-kicker{font-size:.76rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;background:rgba(255,255,255,.18);padding:6px 14px;border-radius:999px;display:inline-block;margin-bottom:18px}
.tau-slide-title{font-size:2.3rem;font-weight:900;line-height:1.25;margin:0 0 15px;letter-spacing:-.03em}
.tau-slide-subtitle{font-size:2.1rem;font-weight:800;margin:0 0 20px;letter-spacing:-.02em}
.tau-slide-author{font-size:1.15rem;opacity:.9;margin:0;font-weight:600}
.tau-slide-art{width:140px;height:140px;background:rgba(255,255,255,.15);border-radius:28px;display:flex;align-items:center;justify-content:center;transform:rotate(15deg)}
.tau-slide-icon{font-size:4.5rem;transform:rotate(-15deg)}
.tau-slide-list{font-size:1.15rem;line-height:1.75;margin:0;padding-left:20px}
.tau-slide-list li{margin-bottom:12px}
.tau-slider-controls{display:flex;justify-content:space-between;align-items:center;padding:16px 32px;background:#0f1115;border-top:1px solid rgba(255,255,255,.08)}
.tau-slider-btn{background:#c62b3a;color:#fff!important;border:none;padding:9px 22px;border-radius:999px;font-size:.88rem;font-weight:700;cursor:pointer;transition:background .2s,transform .1s;outline:none}
.tau-slider-btn:hover{background:#a32230;transform:translateY(-1px)}
.tau-slider-btn:active{transform:translateY(0)}
.tau-slider-indicator{color:rgba(255,255,255,.75);font-size:.9rem;font-weight:700}
</style>
<script>
if(typeof window.tauNextSlide==="undefined"){
    window.tauNextSlide=function(id){
        var w=document.getElementById("tau-slider-"+id);if(!w)return;
        var sl=w.querySelectorAll(".tau-slide"),ai=0;
        sl.forEach(function(s,i){if(s.classList.contains("active"))ai=i;});
        var ni=(ai+1)%sl.length;
        sl[ai].classList.remove("active");sl[ni].classList.add("active");
        w.querySelector(".tau-slider-indicator").textContent="Diapositiva "+(ni+1)+" de "+sl.length;
    };
    window.tauPrevSlide=function(id){
        var w=document.getElementById("tau-slider-"+id);if(!w)return;
        var sl=w.querySelectorAll(".tau-slide"),ai=0;
        sl.forEach(function(s,i){if(s.classList.contains("active"))ai=i;});
        var pi=(ai-1+sl.length)%sl.length;
        sl[ai].classList.remove("active");sl[pi].classList.add("active");
        w.querySelector(".tau-slider-indicator").textContent="Diapositiva "+(pi+1)+" de "+sl.length;
    };
}
// Edit-mode: inject floating AI-editor button for teachers
(function(){
    function tauInjectEditBtn(){
        if(!document.body.classList.contains("editing"))return;
        if(document.getElementById("tau-edit-fab"))return;
        var cmid=(new URLSearchParams(window.location.search)).get("id")||"";
        if(!cmid)return;
        var a=document.createElement("a");
        a.id="tau-edit-fab";
        a.href="/local/tau_course_creator_ai/improve.php?cmid="+cmid;
        a.title="Mejorar esta presentación con IA";
        a.style.cssText="position:fixed;bottom:90px;right:20px;background:linear-gradient(135deg,#c62b3a,#8d182a);color:#fff!important;padding:12px 20px;border-radius:14px;font-size:.85rem;font-weight:800;text-decoration:none;box-shadow:0 6px 24px rgba(198,43,58,.45);z-index:9999;display:flex;align-items:center;gap:8px;letter-spacing:.02em;transition:transform .15s,box-shadow .15s;";
        a.innerHTML="<span style=\"font-size:1.1rem;\">✏️</span> Editar con IA";
        a.onmouseover=function(){a.style.transform="translateY(-2px)";a.style.boxShadow="0 10px 32px rgba(198,43,58,.55)";};
        a.onmouseout=function(){a.style.transform="";a.style.boxShadow="0 6px 24px rgba(198,43,58,.45)";};
        document.body.appendChild(a);
    }
    if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",tauInjectEditBtn);}
    else{tauInjectEditBtn();}
})();
</script>';
    }

    // ── Utility ───────────────────────────────────────────────────────────────

    private function progress(string $msg): void {
        if ($this->on_progress) {
            ($this->on_progress)($msg);
        }
    }

    private function apply_introeditor(\stdClass $moduleinfo): void {
        $moduleinfo->introeditor = [
            'text' => $moduleinfo->intro ?? '',
            'format' => $moduleinfo->introformat ?? FORMAT_HTML,
            'itemid' => 0,
        ];
    }

    private function inject_evaluation_survey_to_blueprint(array &$sections): void {
        if (empty($sections)) {
            return;
        }
        $last_section_idx = -1;
        $last_activity_idx = -1;
        
        for ($i = count($sections) - 1; $i >= 0; $i--) {
            if (!empty($sections[$i]['activities'])) {
                $acts = $sections[$i]['activities'];
                for ($j = count($acts) - 1; $j >= 0; $j--) {
                    $type = $acts[$j]['type'] ?? '';
                    if (in_array($type, ['assign', 'quiz', 'forum', 'h5pactivity', 'page', 'resource'])) {
                        $last_section_idx = $i;
                        $last_activity_idx = $j;
                        break 2;
                    }
                }
                if ($last_section_idx === -1) {
                    $last_section_idx = $i;
                    $last_activity_idx = count($acts) - 1;
                    break;
                }
            }
        }

        if ($last_section_idx !== -1 && $last_activity_idx !== -1) {
            $feedback_activity = [
                'type' => 'feedback',
                'title' => 'Encuesta de Satisfacción del Curso',
                'description' => 'Ayúdanos a mejorar evaluando el curso y al docente. Esta encuesta es anónima y obligatoria para acceder a tu última actividad.',
                'category' => 'evaluacion',
                'requires_previous' => true
            ];
            $sections[$last_section_idx]['activities'][$last_activity_idx]['requires_feedback'] = true;
            array_splice($sections[$last_section_idx]['activities'], $last_activity_idx, 0, [$feedback_activity]);
        }
    }

    private function add_questions_to_feedback(int $feedback_id): void {
        global $DB;
        $questions = [
            ['typ' => 'label', 'name' => 'Diseño y Contenido', 'presentation' => ''],
            ['typ' => 'multichoicerated', 'name' => 'Los objetivos del curso fueron claros desde el inicio.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'Los materiales de estudio (textos, videos, etc.) facilitaron mi aprendizaje.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'La carga de trabajo y el tiempo asignado fueron adecuados para la duración del curso.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'label', 'name' => 'Desempeño Docente', 'presentation' => ''],
            ['typ' => 'multichoicerated', 'name' => 'El docente demostró un amplio dominio de la temática enseñada.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'El docente resolvió dudas de manera oportuna, clara y con buena disposición.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'La retroalimentación (calificación/comentarios) recibida fue útil para mejorar.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'label', 'name' => 'Metodología e Interacción', 'presentation' => ''],
            ['typ' => 'multichoicerated', 'name' => 'El modelo de enseñanza y metodologías activas utilizadas promovieron mi participación (no fue solo una clase magistral).', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'La interacción y espacios de debate enriquecieron mi proceso de aprendizaje.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'label', 'name' => 'Plataforma Tecnológica', 'presentation' => ''],
            ['typ' => 'multichoicerated', 'name' => 'La plataforma virtual (Campus) fue fácil de navegar, intuitiva y accesible.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'Los recursos tecnológicos (foros, entregas, interactividades) funcionaron correctamente.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'label', 'name' => 'Satisfacción General', 'presentation' => ''],
            ['typ' => 'multichoicerated', 'name' => 'Los resultados de aprendizaje o competencias propuestas se cumplieron.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'El sistema de evaluación fue justo y coherente con las temáticas enseñadas.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'multichoicerated', 'name' => 'Recomendaría este curso a otras personas.', 'presentation' => "r>>>>>1/Totalmente en desacuerdo\\n2/En desacuerdo\\n3/Neutral\\n4/De acuerdo\\n5/Totalmente de acuerdo"],
            ['typ' => 'label', 'name' => 'Comentarios Abiertos', 'presentation' => ''],
            ['typ' => 'textarea', 'name' => '¿Qué aspecto del curso consideras que fue el más valioso para tu formación?', 'presentation' => '30|5'],
            ['typ' => 'textarea', 'name' => '¿Qué oportunidades de mejora o sugerencias harías para el docente o el curso?', 'presentation' => '30|5'],
        ];

        $position = 1;
        foreach ($questions as $q) {
            $item = new \stdClass();
            $item->feedback = $feedback_id;
            $item->template = 0;
            $item->name = $q['name'];
            $item->label = '';
            $item->presentation = $q['presentation'];
            $item->typ = $q['typ'];
            $item->hasvalue = ($q['typ'] === 'label') ? 0 : 1;
            $item->position = $position++;
            $item->required = ($q['typ'] === 'label') ? 0 : 1;
            $DB->insert_record('feedback_item', $item);
        }
    }
}
