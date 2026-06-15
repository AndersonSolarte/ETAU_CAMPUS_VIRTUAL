<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);
$context = context_course::instance($courseid);
require_capability('local/tau_assign_ai:use', $context);

$course = get_course($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_assign_ai/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pagetitle', 'local_tau_assign_ai'));
$PAGE->set_heading($course->fullname . ' — ' . get_string('pagetitle', 'local_tau_assign_ai'));
$PAGE->set_pagelayout('standard');

// Load assignments in this course.
$assignments = $DB->get_records_sql(
    "SELECT cm.id AS cmid, a.id, a.name
       FROM {assign} a
       JOIN {course_modules} cm ON cm.instance = a.id
       JOIN {modules} m ON m.id = cm.module AND m.name = 'assign'
      WHERE a.course = :courseid AND cm.deletioninprogress = 0",
    ['courseid' => $courseid]
);

echo $OUTPUT->header();
?>

<div class="tau-assign-ai container-fluid py-4">
  <div class="mb-4">
    <label class="form-label fw-bold"><?php echo get_string('select_assignment', 'local_tau_assign_ai'); ?></label>
    <select class="form-select" id="tau-assignment-select" style="max-width:420px;">
      <option value="">-- Selecciona una tarea --</option>
      <?php foreach ($assignments as $a): ?>
        <option value="<?php echo $a->cmid; ?>" data-assignid="<?php echo $a->id; ?>">
          <?php echo s($a->name); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div id="tau-assign-actions" style="display:none;">
    <button class="btn btn-primary mb-3" id="tau-grade-btn">
      <i class="fa fa-magic me-2"></i><?php echo get_string('grade_with_ai_btn', 'local_tau_assign_ai'); ?>
    </button>

    <div id="tau-assign-progress" style="display:none;" class="mb-3">
      <div class="d-flex align-items-center gap-2">
        <div class="spinner-border spinner-border-sm text-primary"></div>
        <span><?php echo get_string('grading', 'local_tau_assign_ai'); ?></span>
      </div>
      <div class="progress mt-2" style="max-width:400px;">
        <div class="progress-bar bg-primary" id="tau-grade-progress" style="width:0%"></div>
      </div>
    </div>

    <div id="tau-results-section" style="display:none;">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-success"><i class="fa fa-check-circle me-1"></i>Sugerencias de calificación</h6>
        <button class="btn btn-success btn-sm" id="tau-apply-all-btn">
          <i class="fa fa-check me-1"></i><?php echo get_string('apply_all', 'local_tau_assign_ai'); ?>
        </button>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-sm" id="tau-results-table">
          <thead class="table-light">
            <tr>
              <th><?php echo get_string('student', 'local_tau_assign_ai'); ?></th>
              <th><?php echo get_string('ai_grade', 'local_tau_assign_ai'); ?></th>
              <th><?php echo get_string('ai_feedback', 'local_tau_assign_ai'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody id="tau-results-body"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  const AJAX_URL  = '<?php echo $CFG->wwwroot; ?>/local/tau_assign_ai/ajax.php';
  const SESSKEY   = '<?php echo sesskey(); ?>';
  const COURSEID  = <?php echo $courseid; ?>;
  let gradingSuggestions = [];

  document.getElementById('tau-assignment-select').addEventListener('change', function() {
    document.getElementById('tau-assign-actions').style.display = this.value ? '' : 'none';
  });

  document.getElementById('tau-grade-btn').addEventListener('click', async function() {
    const select   = document.getElementById('tau-assignment-select');
    const cmid     = select.value;
    const assignid = select.selectedOptions[0]?.dataset.assignid;
    if (!cmid) return;

    showProgress(true);
    document.getElementById('tau-results-section').style.display = 'none';
    setProgressPct(20);

    try {
      const res = await postAjax({ action: 'grade', sesskey: SESSKEY, cmid, assignid, courseid: COURSEID });
      gradingSuggestions = res.suggestions || [];
      setProgressPct(100);
      setTimeout(() => { showProgress(false); renderResults(gradingSuggestions); }, 400);
    } catch (err) {
      showProgress(false);
      alert('Error: ' + err.message);
    }
  });

  document.getElementById('tau-apply-all-btn').addEventListener('click', async function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Aplicando...';
    try {
      await postAjax({ action: 'apply_all', sesskey: SESSKEY, suggestions: gradingSuggestions });
      this.innerHTML = '<i class="fa fa-check me-1"></i><?php echo get_string('grading_success', 'local_tau_assign_ai'); ?>'.replace('{$a}', gradingSuggestions.length);
      this.className = 'btn btn-outline-success btn-sm';
    } catch (err) {
      alert('Error: ' + err.message);
      this.disabled = false;
      this.innerHTML = '<?php echo get_string('apply_all', 'local_tau_assign_ai'); ?>';
    }
  });

  function renderResults(suggestions) {
    const tbody = document.getElementById('tau-results-body');
    tbody.innerHTML = '';
    suggestions.forEach((s, i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${escapeHtml(s.studentName)}</td>
        <td><span class="badge bg-primary fs-6">${s.grade} / ${s.maxGrade || 100}</span></td>
        <td><small>${escapeHtml(s.feedback)}</small></td>
        <td><button class="btn btn-outline-primary btn-sm" data-idx="${i}">Aplicar</button></td>`;
      tbody.appendChild(tr);
    });
    document.getElementById('tau-results-section').style.display = '';

    tbody.querySelectorAll('button[data-idx]').forEach(btn => {
      btn.addEventListener('click', async function() {
        const s = gradingSuggestions[this.dataset.idx];
        this.disabled = true;
        try {
          await postAjax({ action: 'apply_one', sesskey: SESSKEY, suggestion: s });
          this.textContent = '✓';
          this.className = 'btn btn-success btn-sm';
        } catch (e) { alert(e.message); this.disabled = false; }
      });
    });
  }

  async function postAjax(data) {
    const res = await fetch(AJAX_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    const json = await res.json();
    if (!res.ok || json.error) throw new Error(json.error || 'Error');
    return json;
  }

  function showProgress(show) {
    document.getElementById('tau-assign-progress').style.display = show ? '' : 'none';
  }
  function setProgressPct(pct) {
    document.getElementById('tau-grade-progress').style.width = pct + '%';
  }
  function escapeHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
})();
</script>

<?php echo $OUTPUT->footer(); ?>
