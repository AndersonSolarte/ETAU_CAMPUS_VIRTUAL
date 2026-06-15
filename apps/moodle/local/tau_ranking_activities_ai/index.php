<?php
require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

$course = get_course($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_ranking_activities_ai/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pagetitle', 'local_tau_ranking_activities_ai'));
$PAGE->set_heading($course->fullname . ' — ' . get_string('pagetitle', 'local_tau_ranking_activities_ai'));
$PAGE->set_pagelayout('standard');

// Collect activities data.
$modules = $DB->get_records_sql(
    "SELECT cm.id AS cmid, m.name AS modtype, cm.instance,
            COUNT(DISTINCT cmc.userid) AS completions,
            (SELECT COUNT(*) FROM {course_modules_completion} WHERE coursemoduleid = cm.id) AS total_attempts
       FROM {course_modules} cm
       JOIN {modules} m ON m.id = cm.module
  LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.completionstate > 0
      WHERE cm.course = :cid AND cm.deletioninprogress = 0 AND cm.completion > 0
   GROUP BY cm.id, m.name, cm.instance",
    ['cid' => $courseid]
);

// Get activity names.
$activities_data = [];
foreach ($modules as $mod) {
    $record = $DB->get_record($mod->modtype, ['id' => $mod->instance], 'id, name', IGNORE_MISSING);
    if ($record) {
        $activities_data[] = [
            'cmid'         => $mod->cmid,
            'name'         => $record->name,
            'type'         => $mod->modtype,
            'completions'  => (int)$mod->completions,
            'totalAttempts'=> (int)$mod->total_attempts,
        ];
    }
}

echo $OUTPUT->header();
?>

<div class="tau-ranking container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><?php echo get_string('pagetitle', 'local_tau_ranking_activities_ai'); ?></h5>
    <button class="btn btn-primary" id="tau-analyze-btn">
      <i class="fa fa-chart-bar me-2"></i><?php echo get_string('analyze_btn', 'local_tau_ranking_activities_ai'); ?>
    </button>
  </div>

  <div id="tau-analyzing" style="display:none;" class="text-center py-5">
    <div class="spinner-border text-primary mb-2"></div>
    <p class="text-muted"><?php echo get_string('analyzing', 'local_tau_ranking_activities_ai'); ?></p>
  </div>

  <div id="tau-ranking-results" style="display:none;">
    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="tau-ranking-table">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th><?php echo get_string('activity_name', 'local_tau_ranking_activities_ai'); ?></th>
            <th><?php echo get_string('activity_type', 'local_tau_ranking_activities_ai'); ?></th>
            <th><?php echo get_string('completion_rate', 'local_tau_ranking_activities_ai'); ?></th>
            <th><?php echo get_string('effectiveness_score', 'local_tau_ranking_activities_ai'); ?></th>
            <th><?php echo get_string('ai_insight', 'local_tau_ranking_activities_ai'); ?></th>
          </tr>
        </thead>
        <tbody id="tau-ranking-body"></tbody>
      </table>
    </div>
  </div>

  <!-- Raw data table (always visible) -->
  <div class="card shadow-sm mt-4">
    <div class="card-header"><h6 class="mb-0">Datos de actividades del curso</h6></div>
    <div class="card-body p-0">
      <table class="table table-sm mb-0">
        <thead class="table-light">
          <tr><th>Actividad</th><th>Tipo</th><th>Completados</th></tr>
        </thead>
        <tbody>
          <?php foreach ($activities_data as $a): ?>
            <tr>
              <td><?php echo s($a['name']); ?></td>
              <td><span class="badge bg-secondary"><?php echo s($a['type']); ?></span></td>
              <td><?php echo $a['completions']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.getElementById('tau-analyze-btn').addEventListener('click', async function() {
  document.getElementById('tau-analyzing').style.display = '';
  document.getElementById('tau-ranking-results').style.display = 'none';
  this.disabled = true;

  try {
    const res = await fetch('<?php echo $CFG->wwwroot; ?>/local/tau_ranking_activities_ai/ajax.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        sesskey: '<?php echo sesskey(); ?>',
        courseid: <?php echo $courseid; ?>,
        activities: <?php echo json_encode(array_values($activities_data)); ?>
      }),
    });
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    renderRanking(data.ranking || []);
  } catch(e) {
    alert('Error: ' + e.message);
  } finally {
    document.getElementById('tau-analyzing').style.display = 'none';
    this.disabled = false;
  }
});

function renderRanking(ranking) {
  const tbody = document.getElementById('tau-ranking-body');
  tbody.innerHTML = '';
  ranking.forEach((item, i) => {
    const score = item.effectivenessScore || 0;
    const barColor = score >= 70 ? 'success' : score >= 40 ? 'warning' : 'danger';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><span class="badge bg-${barColor} fs-6">${i + 1}</span></td>
      <td><strong>${escapeHtml(item.name)}</strong></td>
      <td><span class="badge bg-secondary">${escapeHtml(item.type)}</span></td>
      <td>${item.completionRate ?? '—'}%</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height:8px">
            <div class="progress-bar bg-${barColor}" style="width:${score}%"></div>
          </div>
          <span class="fw-bold">${score}</span>
        </div>
      </td>
      <td><small class="text-muted">${escapeHtml(item.insight || '')}</small></td>`;
    tbody.appendChild(tr);
  });
  document.getElementById('tau-ranking-results').style.display = '';
}

function escapeHtml(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>

<?php echo $OUTPUT->footer(); ?>
