<?php
require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();

$courseid = optional_param('courseid', 0, PARAM_INT);
$certname = optional_param('certname', '', PARAM_TEXT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_share_certificate_ai/share.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pagetitle', 'local_tau_share_certificate_ai'));
$PAGE->set_heading(get_string('pagetitle', 'local_tau_share_certificate_ai'));
$PAGE->set_pagelayout('standard');

$course     = $courseid ? get_course($courseid) : null;
$siteurl    = $CFG->wwwroot;
$org_name   = $SITE->fullname ?? 'E-TAU Campus Virtual';
$issue_date = date('Y-m-d');

echo $OUTPUT->header();
?>

<div class="tau-share-cert container" style="max-width:600px;margin:0 auto;padding-top:32px;">
  <div class="card shadow-sm">
    <div class="card-header" style="background:#0a66c2;color:#fff;">
      <div class="d-flex align-items-center gap-2">
        <i class="fa fa-linkedin-square fa-2x"></i>
        <h5 class="mb-0"><?php echo get_string('pagetitle', 'local_tau_share_certificate_ai'); ?></h5>
      </div>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label fw-bold"><?php echo get_string('certificate_title', 'local_tau_share_certificate_ai'); ?></label>
        <input type="text" class="form-control" id="tau-cert-name"
          value="<?php echo s($certname ?: ($course ? $course->fullname : '')); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold"><?php echo get_string('issuing_org', 'local_tau_share_certificate_ai'); ?></label>
        <input type="text" class="form-control" id="tau-cert-org" value="<?php echo s($org_name); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold"><?php echo get_string('issue_date', 'local_tau_share_certificate_ai'); ?></label>
        <input type="date" class="form-control" id="tau-cert-date" value="<?php echo $issue_date; ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold"><?php echo get_string('credential_url', 'local_tau_share_certificate_ai'); ?></label>
        <input type="url" class="form-control" id="tau-cert-url"
          value="<?php echo s($siteurl . '/local/tau_share_certificate_ai/share.php?courseid=' . $courseid); ?>">
      </div>

      <button class="btn btn-outline-primary mb-3 w-100" id="tau-gen-desc-btn">
        <i class="fa fa-magic me-2"></i><?php echo get_string('generate_description', 'local_tau_share_certificate_ai'); ?>
      </button>

      <div class="mb-3" id="tau-desc-section">
        <label class="form-label fw-bold"><?php echo get_string('ai_description', 'local_tau_share_certificate_ai'); ?></label>
        <textarea class="form-control" id="tau-cert-desc" rows="4"
          placeholder="La descripción se generará automáticamente con IA..."></textarea>
      </div>

      <!-- Preview -->
      <div class="border rounded p-3 mb-3" id="tau-preview" style="background:#f3f2ef;">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div style="width:48px;height:48px;border-radius:50%;background:#c62b3a;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;flex-shrink:0;">
            <?php echo mb_strtoupper(mb_substr($USER->firstname ?? 'U', 0, 1)); ?>
          </div>
          <div>
            <div class="fw-bold" style="font-size:.9rem;"><?php echo s(fullname($USER)); ?></div>
            <div class="text-muted" style="font-size:.8rem;"><?php echo s($USER->department ?? ''); ?></div>
          </div>
        </div>
        <p class="mb-1 small" id="tau-preview-text">Completa los campos para ver la vista previa...</p>
        <hr class="my-2">
        <div class="small text-muted">
          <i class="fa fa-certificate me-1"></i>
          <span id="tau-preview-cert">—</span> · <span id="tau-preview-org">—</span>
        </div>
      </div>

      <button class="btn w-100 text-white fw-bold" id="tau-share-btn" style="background:#0a66c2;">
        <i class="fa fa-share me-2"></i><?php echo get_string('share_linkedin', 'local_tau_share_certificate_ai'); ?>
      </button>
    </div>
  </div>
</div>

<script>
(function(){
  const AJAX_URL = '<?php echo $CFG->wwwroot; ?>/local/tau_share_certificate_ai/ajax.php';
  const SESSKEY  = '<?php echo sesskey(); ?>';

  // Live preview update.
  ['tau-cert-name','tau-cert-org','tau-cert-desc'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updatePreview);
  });

  function updatePreview() {
    document.getElementById('tau-preview-text').textContent =
      document.getElementById('tau-cert-desc').value || 'Completa los campos...';
    document.getElementById('tau-preview-cert').textContent =
      document.getElementById('tau-cert-name').value || '—';
    document.getElementById('tau-preview-org').textContent =
      document.getElementById('tau-cert-org').value || '—';
  }

  // Generate AI description.
  document.getElementById('tau-gen-desc-btn').addEventListener('click', async function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generando...';
    try {
      const res = await fetch(AJAX_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'generate_description', sesskey: SESSKEY,
          certName: document.getElementById('tau-cert-name').value,
          orgName: document.getElementById('tau-cert-org').value,
          courseid: <?php echo $courseid; ?>
        }),
      });
      const data = await res.json();
      if (data.error) throw new Error(data.error);
      document.getElementById('tau-cert-desc').value = data.description || '';
      updatePreview();
    } catch(e) { alert('Error: ' + e.message); }
    finally {
      this.disabled = false;
      this.innerHTML = '<i class="fa fa-magic me-2"></i><?php echo get_string('generate_description', 'local_tau_share_certificate_ai'); ?>';
    }
  });

  // Share to LinkedIn.
  document.getElementById('tau-share-btn').addEventListener('click', function() {
    const certName = encodeURIComponent(document.getElementById('tau-cert-name').value);
    const orgName  = encodeURIComponent(document.getElementById('tau-cert-org').value);
    const certUrl  = encodeURIComponent(document.getElementById('tau-cert-url').value);
    const issueDate = document.getElementById('tau-cert-date').value.replace(/-/g,'/');

    // LinkedIn share URL (deep link to "Add to Profile").
    const liUrl = `https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name=${certName}&organizationName=${orgName}&issueYear=${issueDate.split('/')[0]}&issueMonth=${parseInt(issueDate.split('/')[1],10)}&certUrl=${certUrl}`;
    window.open(liUrl, '_blank', 'width=600,height=500');
  });
})();
</script>

<?php echo $OUTPUT->footer(); ?>
