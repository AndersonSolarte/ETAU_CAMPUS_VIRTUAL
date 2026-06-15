<?php
require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

$course = get_course($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_forum_ai/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pagetitle', 'local_tau_forum_ai'));
$PAGE->set_heading($course->fullname . ' — ' . get_string('pagetitle', 'local_tau_forum_ai'));
$PAGE->set_pagelayout('standard');

// Load forums in this course.
$forums = $DB->get_records_sql(
    "SELECT f.id, f.name
       FROM {forum} f
      WHERE f.course = :cid",
    ['cid' => $courseid]
);

// Load recent moderation log.
$logs = $DB->get_records_sql(
    "SELECT l.*, fp.subject
       FROM {local_tau_forum_ai_log} l
       JOIN {forum_posts} fp ON fp.id = l.postid
      WHERE l.courseid = :cid
   ORDER BY l.timecreated DESC",
    ['cid' => $courseid],
    0, 20
);

echo $OUTPUT->header();
?>
<div class="tau-forum-ai container-fluid py-4">
  <div class="row">
    <div class="col-md-5">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="fa fa-comments me-2"></i>Generar debate con IA</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Foro destino</label>
            <select class="form-select" id="tau-forum-select">
              <?php foreach ($forums as $f): ?>
                <option value="<?php echo $f->id; ?>"><?php echo s($f->name); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold"><?php echo get_string('debate_topic', 'local_tau_forum_ai'); ?></label>
            <textarea class="form-control" id="tau-debate-topic" rows="4"
              placeholder="Ej: ¿Cuál es el impacto de la IA en la educación moderna?"></textarea>
          </div>
          <button class="btn btn-primary w-100" id="tau-debate-btn">
            <i class="fa fa-magic me-2"></i><?php echo get_string('generate_debate', 'local_tau_forum_ai'); ?>
          </button>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header"><h6 class="mb-0">Historial de moderación IA</h6></div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr><th>Publicación</th><th>Veredicto</th><th>Fecha</th></tr>
            </thead>
            <tbody>
              <?php foreach ($logs as $log): ?>
                <tr>
                  <td><?php echo s($log->subject); ?></td>
                  <td><?php
                    $badge = ['approved' => 'success', 'flagged' => 'warning', 'rejected' => 'danger'];
                    $cls = $badge[$log->verdict] ?? 'secondary';
                    echo "<span class='badge bg-$cls'>" . s($log->verdict) . "</span>";
                  ?></td>
                  <td><small class="text-muted"><?php echo userdate($log->timecreated); ?></small></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($logs)): ?>
                <tr><td colspan="3" class="text-muted text-center py-3">Sin registros aún.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('tau-debate-btn').addEventListener('click', async function() {
  const forumid = document.getElementById('tau-forum-select').value;
  const topic   = document.getElementById('tau-debate-topic').value.trim();
  if (!topic) return alert('Escribe un tema para el debate.');

  this.disabled = true;
  this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generando...';

  try {
    const res = await fetch('<?php echo $CFG->wwwroot; ?>/local/tau_forum_ai/ajax.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'generate_debate', sesskey: '<?php echo sesskey(); ?>',
        courseid: <?php echo $courseid; ?>, forumid, topic }),
    });
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    alert('<?php echo get_string('debate_generated', 'local_tau_forum_ai'); ?>');
    location.reload();
  } catch(e) {
    alert('Error: ' + e.message);
  } finally {
    this.disabled = false;
    this.innerHTML = '<i class="fa fa-magic me-2"></i><?php echo get_string('generate_debate', 'local_tau_forum_ai'); ?>';
  }
});
</script>

<?php echo $OUTPUT->footer(); ?>
