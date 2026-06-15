<?php
require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
require_capability('moodle/user:viewdetails', $context);

$userid = optional_param('userid', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_student_life_story/index.php', $userid ? ['userid' => $userid] : []));
$PAGE->set_title(get_string('pagetitle', 'local_tau_student_life_story'));
$PAGE->set_heading(get_string('pagetitle', 'local_tau_student_life_story'));
$PAGE->set_pagelayout('standard');

// Build student data if user selected.
$student_data = null;
if ($userid) {
    $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);

    $enrollments = enrol_get_all_users_courses($userid, true);

    $total_grades = $grade_sum = 0;
    $activities_completed = 0;

    foreach ($enrollments as $course) {
        $grades = grade_get_course_grade($userid, $course->id);
        if ($grades && $grades->grade !== null) {
            $grade_sum += (float)$grades->grade;
            $total_grades++;
        }
        $completions = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {course_modules_completion} WHERE userid = :uid AND completionstate > 0
               AND coursemoduleid IN (SELECT id FROM {course_modules} WHERE course = :cid)",
            ['uid' => $userid, 'cid' => $course->id]
        );
        $activities_completed += $completions;
    }

    $forum_posts = $DB->count_records('forum_posts', ['userid' => $userid]);
    $last_access = $DB->get_field('user_lastaccess', 'MAX(timeaccess)', ['userid' => $userid]);

    $student_data = [
        'userId'              => $userid,
        'fullName'            => fullname($user),
        'email'               => $user->email,
        'coursesEnrolled'     => count($enrollments),
        'activitiesCompleted' => $activities_completed,
        'avgGrade'            => $total_grades > 0 ? round($grade_sum / $total_grades, 1) : null,
        'forumPosts'          => $forum_posts,
        'lastAccess'          => $last_access,
    ];
}

echo $OUTPUT->header();
?>

<div class="tau-sls container-fluid py-4">
  <div class="row">
    <div class="col-md-4">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="fa fa-user-circle me-2"></i><?php echo get_string('select_student', 'local_tau_student_life_story'); ?></h6>
        </div>
        <div class="card-body">
          <form method="get">
            <div class="mb-3">
              <label class="form-label fw-bold"><?php echo get_string('select_student', 'local_tau_student_life_story'); ?></label>
              <select class="form-select" name="userid" id="tau-student-select">
                <option value="">-- Selecciona un estudiante --</option>
                <?php
                $students = get_users_by_capability($context, 'moodle/course:participate', 'u.id, u.firstname, u.lastname');
                foreach ($students as $s):
                ?>
                  <option value="<?php echo $s->id; ?>" <?php selected($userid, $s->id); ?>>
                    <?php echo s(fullname($s)); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ver perfil</button>
          </form>
        </div>
      </div>

      <?php if ($student_data): ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="text-primary fw-bold"><?php echo s($student_data['fullName']); ?></h6>
          <hr>
          <div class="row text-center g-2">
            <div class="col-6">
              <div class="border rounded p-2">
                <div class="fs-4 fw-bold text-primary"><?php echo $student_data['coursesEnrolled']; ?></div>
                <small class="text-muted"><?php echo get_string('courses_enrolled', 'local_tau_student_life_story'); ?></small>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded p-2">
                <div class="fs-4 fw-bold text-success"><?php echo $student_data['activitiesCompleted']; ?></div>
                <small class="text-muted"><?php echo get_string('activities_completed', 'local_tau_student_life_story'); ?></small>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded p-2">
                <div class="fs-4 fw-bold text-warning"><?php echo $student_data['avgGrade'] ?? '—'; ?></div>
                <small class="text-muted"><?php echo get_string('avg_grade', 'local_tau_student_life_story'); ?></small>
              </div>
            </div>
            <div class="col-6">
              <div class="border rounded p-2">
                <div class="fs-4 fw-bold text-info"><?php echo $student_data['forumPosts']; ?></div>
                <small class="text-muted"><?php echo get_string('participation', 'local_tau_student_life_story'); ?></small>
              </div>
            </div>
          </div>
          <div class="mt-3 text-muted small">
            <i class="fa fa-clock me-1"></i><?php echo get_string('last_access', 'local_tau_student_life_story'); ?>:
            <?php echo $student_data['lastAccess'] ? userdate($student_data['lastAccess']) : 'Nunca'; ?>
          </div>
          <button class="btn btn-primary w-100 mt-3" id="tau-generate-btn">
            <i class="fa fa-magic me-2"></i><?php echo get_string('generate_profile', 'local_tau_student_life_story'); ?>
          </button>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="col-md-8">
      <div id="tau-ai-profile" style="display:none;">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            <h6 class="mb-0"><i class="fa fa-brain me-2"></i>Análisis IA del perfil académico</h6>
          </div>
          <div class="card-body" id="tau-profile-content"></div>
        </div>
      </div>
      <div id="tau-loading" style="display:none;" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2 text-muted"><?php echo get_string('generating_profile', 'local_tau_student_life_story'); ?></p>
      </div>
    </div>
  </div>
</div>

<?php if ($student_data): ?>
<script>
document.getElementById('tau-generate-btn')?.addEventListener('click', async function() {
  document.getElementById('tau-loading').style.display = '';
  document.getElementById('tau-ai-profile').style.display = 'none';
  this.disabled = true;

  try {
    const res = await fetch('<?php echo $CFG->wwwroot; ?>/local/tau_student_life_story/ajax.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        sesskey: '<?php echo sesskey(); ?>',
        studentData: <?php echo json_encode($student_data); ?>
      }),
    });
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    renderProfile(data);
  } catch(e) {
    alert('Error: ' + e.message);
  } finally {
    document.getElementById('tau-loading').style.display = 'none';
    this.disabled = false;
  }
});

function renderProfile(data) {
  const riskColor = { low: 'success', medium: 'warning', high: 'danger' };
  const risk = data.dropoutRisk || 'low';
  const content = document.getElementById('tau-profile-content');
  content.innerHTML = `
    <div class="mb-3">
      <h6 class="text-muted text-uppercase small fw-bold"><?php echo get_string('profile_summary', 'local_tau_student_life_story'); ?></h6>
      <p>${data.summary || ''}</p>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <div class="border rounded p-3">
          <h6 class="text-success"><i class="fa fa-thumbs-up me-1"></i><?php echo get_string('strengths', 'local_tau_student_life_story'); ?></h6>
          <ul class="mb-0 small">${(data.strengths||[]).map(s=>`<li>${s}</li>`).join('')}</ul>
        </div>
      </div>
      <div class="col-md-4">
        <div class="border rounded p-3">
          <h6 class="text-warning"><i class="fa fa-exclamation-triangle me-1"></i><?php echo get_string('areas_improvement', 'local_tau_student_life_story'); ?></h6>
          <ul class="mb-0 small">${(data.areasImprovement||[]).map(s=>`<li>${s}</li>`).join('')}</ul>
        </div>
      </div>
      <div class="col-md-4">
        <div class="border rounded p-3 text-center">
          <h6 class="text-muted"><?php echo get_string('predicted_risk', 'local_tau_student_life_story'); ?></h6>
          <span class="badge bg-${riskColor[risk]} fs-6">${risk.toUpperCase()}</span>
          <div class="mt-2">
            <small class="text-muted"><?php echo get_string('engagement_score', 'local_tau_student_life_story'); ?>: </small>
            <strong>${data.engagementScore || 0}/100</strong>
          </div>
        </div>
      </div>
    </div>
    <div>
      <h6 class="text-muted text-uppercase small fw-bold">Recomendaciones</h6>
      <ul class="small">${(data.recommendations||[]).map(s=>`<li>${s}</li>`).join('')}</ul>
    </div>`;
  document.getElementById('tau-ai-profile').style.display = '';
}
</script>
<?php endif; ?>

<?php echo $OUTPUT->footer(); ?>
