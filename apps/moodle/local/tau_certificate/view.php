<?php
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$course   = get_course($courseid);
$context  = context_course::instance($courseid);
require_capability('moodle/course:view', $context);

$completion = new completion_info($course);
$completed  = $completion->is_course_complete($USER->id);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_certificate/view.php', ['courseid' => $courseid]));
$PAGE->set_course($course);
$PAGE->set_title(get_string('certificate', 'local_tau_certificate') . ' — ' . format_string($course->fullname));

if (!$completed) {
    $PAGE->set_pagelayout('standard');
    $PAGE->set_heading(format_string($course->fullname));
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('notcompleted', 'local_tau_certificate'), 'warning');
    echo html_writer::link(
        new moodle_url('/course/view.php', ['id' => $courseid]),
        get_string('backtocourse', 'local_tau_certificate'),
        ['class' => 'btn btn-primary']
    );
    echo $OUTPUT->footer();
    exit;
}

$PAGE->set_pagelayout('popup');

$studentname = fullname($USER);
$coursename  = format_string($course->fullname);
$sitename    = format_string($SITE->fullname);
$issuedon    = strftime('%d de %B de %Y');

// Fallback for PHP 8.1+ (strftime deprecated).
if (function_exists('IntlDateFormatter')) {
    $fmt = new IntlDateFormatter('es_CO', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, "d 'de' MMMM 'de' y");
    $issuedon = $fmt->format(time()) ?: date('d/m/Y');
} else {
    $issuedon = date('d/m/Y');
}

echo $OUTPUT->header();
?>
<style>
@media print {
  .no-print { display:none !important; }
  body, .path-local-tau-certificate { background:#fff !important; }
  .cert-wrapper { box-shadow:none !important; border-color:#c62b3a !important; }
}
.cert-wrapper {
  max-width:860px; margin:24px auto; padding:64px 72px;
  border:3px solid #c62b3a; border-radius:8px;
  background:#fff; font-family:Georgia,serif;
  position:relative; box-shadow:0 6px 24px rgba(0,0,0,.12);
}
.cert-inner {
  position:absolute; top:12px; left:12px; right:12px; bottom:12px;
  border:1px solid #c62b3a; border-radius:4px; pointer-events:none;
}
.cert-logo { text-align:center; margin-bottom:32px; }
.cert-logo img { height:56px; }
.cert-logo-text { font-size:1.2rem; font-weight:bold; color:#c62b3a; letter-spacing:1px; }
.cert-title {
  font-size:2.4rem; color:#c62b3a; text-align:center;
  margin-bottom:6px; font-weight:normal;
}
.cert-subtitle {
  font-size:.85rem; text-align:center; color:#888;
  letter-spacing:3px; text-transform:uppercase; margin-bottom:48px;
}
.cert-body { text-align:center; font-size:1.05rem; color:#333; line-height:2; }
.cert-student {
  font-size:2.1rem; font-style:italic; color:#1a1a1a;
  border-bottom:2px solid #c62b3a;
  display:inline-block; padding:0 48px 8px; margin:12px 0 20px;
}
.cert-course { font-size:1.35rem; font-weight:bold; color:#c62b3a; margin:8px 0 20px; }
.cert-sigs {
  display:flex; justify-content:space-around;
  margin-top:64px; padding-top:16px;
}
.cert-sig { text-align:center; flex:1; padding:0 16px; }
.cert-sig-line {
  border-top:1px solid #555; padding-top:10px;
  font-size:.82rem; color:#555; line-height:1.5;
}
.cert-date { text-align:center; margin-top:28px; font-size:.9rem; color:#666; }
.cert-seal {
  width:80px; height:80px; border-radius:50%;
  background:#c62b3a; color:#fff;
  display:flex; align-items:center; justify-content:center;
  font-size:.65rem; font-weight:bold; text-align:center; line-height:1.3;
  margin:0 auto 8px; padding:8px; letter-spacing:.5px;
  text-transform:uppercase;
}
</style>

<div class="cert-wrapper">
  <div class="cert-inner"></div>

  <div class="cert-logo">
    <div class="cert-logo-text">UNIVERSIDAD CESMAG | E-TAU Campus Virtual</div>
  </div>

  <div class="cert-title">Certificado de Finalización</div>
  <div class="cert-subtitle">Educación en Línea</div>

  <div class="cert-body">
    <p>Se certifica que</p>
    <div class="cert-student"><?php echo s($studentname); ?></div>
    <p>ha completado satisfactoriamente el curso</p>
    <div class="cert-course"><?php echo s($coursename); ?></div>
    <p>ofrecido a través de la plataforma <strong><?php echo s($sitename); ?></strong></p>
  </div>

  <div class="cert-date">Expedido el <?php echo $issuedon; ?></div>

  <div class="cert-sigs">
    <div class="cert-sig">
      <div class="cert-seal">CESMAG<br>TAU</div>
      <div class="cert-sig-line">
        Director Académico<br>
        <strong>Universidad CESMAG</strong>
      </div>
    </div>
    <div class="cert-sig">
      <div class="cert-seal">Campus<br>Virtual</div>
      <div class="cert-sig-line">
        Coordinador Campus Virtual<br>
        <strong>TAU — CESMAG</strong>
      </div>
    </div>
  </div>
</div>

<div class="text-center my-4 no-print">
  <button onclick="window.print()"
          class="btn btn-lg fw-bold text-white me-2"
          style="background:#c62b3a; border:none;">
    <i class="fa fa-print me-2"></i>Imprimir / Guardar PDF
  </button>
  <a href="<?php echo (new moodle_url('/course/view.php', ['id' => $courseid]))->out(); ?>"
     class="btn btn-lg btn-outline-secondary">
    Volver al curso
  </a>
</div>

<?php echo $OUTPUT->footer(); ?>
