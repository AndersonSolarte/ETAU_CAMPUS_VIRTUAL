<?php
defined('MOODLE_INTERNAL') || die();

class enrol_cesmag_plugin extends enrol_plugin {

    public function enrol_page_hook(stdClass $instance) {
        global $USER;

        if (isguestuser()) {
            return null;
        }

        $context = context_course::instance($instance->courseid);
        if (is_enrolled($context, $USER, '', true)) {
            return null;
        }

        $payurl = !empty($instance->customtext1)
            ? $instance->customtext1
            : (string)get_config('enrol_cesmag', 'payurl');

        $price    = !empty($instance->cost) ? trim($instance->cost) : '';
        $coursename = format_string(get_course($instance->courseid)->fullname);

        ob_start();
        ?>
        <div class="enrol-cesmag card border-0 shadow-sm my-3" style="max-width:500px;">
          <div class="card-header text-white fw-bold" style="background:#c62b3a;">
            <i class="fa fa-university me-2"></i>Curso con inscripción de pago — CESMAG
          </div>
          <div class="card-body">
            <?php if ($price): ?>
              <p class="fs-4 fw-bold mb-2" style="color:#c62b3a;">$ <?php echo s($price); ?></p>
            <?php endif; ?>
            <p class="text-muted small mb-3">
              Para acceder a <strong><?php echo s($coursename); ?></strong> realiza tu pago
              a través del portal oficial de CESMAG. Una vez confirmado, el administrador
              activará tu matrícula.
            </p>
            <a href="<?php echo s($payurl); ?>"
               target="_blank" rel="noopener noreferrer"
               class="btn btn-lg w-100 fw-bold text-white mb-3"
               style="background:#c62b3a; border:none;">
              <i class="fa fa-credit-card me-2"></i>Realizar pago — Portal CESMAG
            </a>
            <div class="alert alert-light border small mb-0">
              <i class="fa fa-info-circle me-1 text-primary"></i>
              Después de pagar, envía tu comprobante al administrador del campus.
              Tu acceso será activado dentro de las próximas <strong>24 horas hábiles</strong>.
            </div>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function can_add_instance($courseid) {
        $context = context_course::instance($courseid);
        return has_capability('moodle/course:enrolconfig', $context);
    }

    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('moodle/course:enrolconfig', $context);
    }

    public function get_instance_defaults() {
        return [
            'status'      => ENROL_INSTANCE_ENABLED,
            'customtext1' => (string)get_config('enrol_cesmag', 'payurl'),
            'cost'        => '',
        ];
    }

    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
        $mform->addElement('text', 'customtext1',
            get_string('payurl', 'enrol_cesmag'), ['size' => 60]);
        $mform->setType('customtext1', PARAM_URL);
        $mform->setDefault('customtext1', get_config('enrol_cesmag', 'payurl'));
        $mform->addHelpButton('customtext1', 'payurl', 'enrol_cesmag');

        $mform->addElement('text', 'cost',
            get_string('cost', 'enrol_cesmag'), ['size' => 20, 'placeholder' => 'Ej: 150.000']);
        $mform->setType('cost', PARAM_TEXT);
        $mform->addHelpButton('cost', 'cost', 'enrol_cesmag');
    }

    public function edit_instance_validation($data, $files, $instance, $context) {
        return [];
    }

    public function use_standard_editing_ui() {
        return true;
    }
}
