<?php
require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

$course = get_course($courseid);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_smart_rules_ai/index.php', ['courseid' => $courseid]));
$PAGE->set_title(get_string('pagetitle', 'local_tau_smart_rules_ai'));
$PAGE->set_heading($course->fullname . ' — ' . get_string('pagetitle', 'local_tau_smart_rules_ai'));
$PAGE->set_pagelayout('standard');

// Handle form submission.
if ($action = optional_param('formaction', '', PARAM_ALPHA)) {
    require_sesskey();
    if ($action === 'add') {
        $rule = new \stdClass();
        $rule->courseid         = $courseid;
        $rule->name             = required_param('name', PARAM_TEXT);
        $rule->trigger          = required_param('trigger', PARAM_ALPHANUMEXT);
        $rule->condition_value  = optional_param('condition_value', '', PARAM_TEXT);
        $rule->action           = required_param('action', PARAM_ALPHANUMEXT);
        $rule->action_data      = json_encode(['message' => optional_param('action_message', '', PARAM_TEXT)]);
        $rule->active           = 1;
        $rule->timecreated      = time();
        $rule->timemodified     = time();
        $DB->insert_record('local_tau_smart_rules', $rule);
        redirect($PAGE->url, 'Regla creada.', 3);
    } elseif ($action === 'toggle') {
        $ruleid = required_param('ruleid', PARAM_INT);
        $r = $DB->get_record('local_tau_smart_rules', ['id' => $ruleid, 'courseid' => $courseid], '*', MUST_EXIST);
        $DB->set_field('local_tau_smart_rules', 'active', $r->active ? 0 : 1, ['id' => $ruleid]);
        redirect($PAGE->url);
    } elseif ($action === 'delete') {
        $ruleid = required_param('ruleid', PARAM_INT);
        $DB->delete_records('local_tau_smart_rules', ['id' => $ruleid, 'courseid' => $courseid]);
        redirect($PAGE->url, 'Regla eliminada.', 3);
    }
}

$rules = $DB->get_records('local_tau_smart_rules', ['courseid' => $courseid], 'timecreated DESC');

$triggers = ['inactivity' => 'Inactividad', 'low_grade' => 'Nota baja', 'completion' => 'Actividad completada', 'enrollment' => 'Nueva matriculación'];
$actions  = ['send_message' => 'Enviar mensaje interno', 'send_email' => 'Enviar email', 'enroll_course' => 'Matricular en curso'];

echo $OUTPUT->header();
?>

<div class="tau-smart-rules container-fluid py-4">
  <div class="row">
    <div class="col-md-5">
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="fa fa-plus me-2"></i><?php echo get_string('add_rule', 'local_tau_smart_rules_ai'); ?></h6>
        </div>
        <div class="card-body">
          <form method="post">
            <?php echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]); ?>
            <input type="hidden" name="formaction" value="add">
            <div class="mb-3">
              <label class="form-label fw-bold"><?php echo get_string('rule_name', 'local_tau_smart_rules_ai'); ?></label>
              <input type="text" class="form-control" name="name" required placeholder="Ej: Alerta inactividad 7 días">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold"><?php echo get_string('trigger_event', 'local_tau_smart_rules_ai'); ?></label>
              <select class="form-select" name="trigger">
                <?php foreach ($triggers as $k => $v): ?>
                  <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold"><?php echo get_string('condition', 'local_tau_smart_rules_ai'); ?></label>
              <input type="text" class="form-control" name="condition_value" placeholder="Ej: 7 (días), 60 (nota mínima)">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold"><?php echo get_string('action_type', 'local_tau_smart_rules_ai'); ?></label>
              <select class="form-select" name="action">
                <?php foreach ($actions as $k => $v): ?>
                  <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Mensaje de acción</label>
              <textarea class="form-control" name="action_message" rows="3"
                placeholder="Texto del mensaje que recibirá el estudiante..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">
              <i class="fa fa-save me-2"></i>Crear regla
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Reglas activas</h6>
          <span class="badge bg-primary"><?php echo count($rules); ?> reglas</span>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr><th>Nombre</th><th>Disparador</th><th>Acción</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
              <?php foreach ($rules as $rule): ?>
                <tr>
                  <td><?php echo s($rule->name); ?></td>
                  <td><span class="badge bg-info"><?php echo $triggers[$rule->trigger] ?? $rule->trigger; ?></span></td>
                  <td><small><?php echo $actions[$rule->action] ?? $rule->action; ?></small></td>
                  <td>
                    <form method="post" class="d-inline">
                      <?php echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]); ?>
                      <input type="hidden" name="formaction" value="toggle">
                      <input type="hidden" name="ruleid" value="<?php echo $rule->id; ?>">
                      <button type="submit" class="btn btn-xs <?php echo $rule->active ? 'btn-success' : 'btn-secondary'; ?> btn-sm">
                        <?php echo $rule->active ? get_string('rule_active', 'local_tau_smart_rules_ai') : get_string('rule_inactive', 'local_tau_smart_rules_ai'); ?>
                      </button>
                    </form>
                  </td>
                  <td>
                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta regla?')">
                      <?php echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]); ?>
                      <input type="hidden" name="formaction" value="delete">
                      <input type="hidden" name="ruleid" value="<?php echo $rule->id; ?>">
                      <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($rules)): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">No hay reglas aún. Crea la primera.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php echo $OUTPUT->footer(); ?>
