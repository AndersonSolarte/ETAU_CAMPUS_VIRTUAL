<?php
require_once('../../config.php');

$PAGE->set_url('/local/tau_soporte/solicitud.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Realizar Solicitud — E-TAU Campus Virtual');
$PAGE->set_heading('Soporte Campus Virtual');
$PAGE->set_pagelayout('standard');

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $nombre    = trim(required_param('nombre',    PARAM_TEXT));
    $correo    = trim(required_param('correo',    PARAM_EMAIL));
    $rol       = trim(required_param('rol',       PARAM_ALPHA));
    $categoria = trim(required_param('categoria', PARAM_TEXT));
    $mensaje   = trim(required_param('mensaje',   PARAM_TEXT));

    if (strlen($nombre) < 3)   $errors[] = 'Por favor ingresa tu nombre completo.';
    if (!validate_email($correo)) $errors[] = 'El correo electrónico no es válido.';
    if (strlen($mensaje) < 20)  $errors[] = 'La descripción debe tener al menos 20 caracteres.';

    if (empty($errors)) {
        // Send email to support address
        $supportEmail = 'tau-ayuda@unicesmag.edu.co';
        $subject = "[TAU Soporte] {$categoria} — {$nombre}";
        $body    = "Nueva solicitud de soporte E-TAU Campus Virtual\n\n"
                 . "Nombre:    {$nombre}\n"
                 . "Correo:    {$correo}\n"
                 . "Rol:       {$rol}\n"
                 . "Categoría: {$categoria}\n\n"
                 . "Descripción:\n{$mensaje}\n\n"
                 . "---\nEnviado desde E-TAU Campus Virtual el " . userdate(time());

        $fakeUser       = new stdClass();
        $fakeUser->id   = -99;
        $fakeUser->email      = $supportEmail;
        $fakeUser->firstname  = 'Soporte';
        $fakeUser->lastname   = 'TAU';
        $fakeUser->maildisplay = 0;
        $fakeUser->mailformat  = 0;
        $fakeUser->lang        = 'es';
        $fakeUser->deleted     = 0;
        $fakeUser->suspended   = 0;
        $fakeUser->emailstop   = 0;
        $fakeUser->auth        = 'manual';
        $fakeUser->username    = 'soporte_tau';
        $fakeUser->mnethostid  = $CFG->mnetlocalhostid ?? 1;

        $sender       = new stdClass();
        $sender->id   = -98;
        $sender->email      = $correo;
        $sender->firstname  = $nombre;
        $sender->lastname   = '';
        $sender->maildisplay = 0;
        $sender->mailformat  = 0;
        $sender->lang        = 'es';
        $sender->deleted     = 0;
        $sender->suspended   = 0;
        $sender->emailstop   = 0;
        $sender->auth        = 'manual';
        $sender->username    = 'solicitante';
        $sender->mnethostid  = $CFG->mnetlocalhostid ?? 1;

        email_to_user($fakeUser, $sender, $subject, $body);
        $success = true;
    }
}

echo $OUTPUT->header();
?>

<style>
.tau-support-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #c62b3a 100%);
    color: #fff;
    padding: 48px 24px 36px;
    text-align: center;
    margin: -16px -16px 40px;
    border-radius: 0 0 24px 24px;
}
.tau-support-hero h1 {
    font-size: 1.9rem;
    font-weight: 800;
    margin-bottom: 8px;
    color: #fff;
}
.tau-support-hero p {
    font-size: 1rem;
    opacity: .88;
    margin: 0;
}
.tau-form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,.09);
    padding: 36px 40px;
    max-width: 700px;
    margin: 0 auto 48px;
}
[data-bs-theme="dark"] .tau-form-card {
    background: #1e1e2e;
    color: #eee;
}
.tau-form-card h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #c62b3a;
    margin-bottom: 24px;
}
.tau-form-group {
    margin-bottom: 20px;
}
.tau-form-group label {
    display: block;
    font-weight: 600;
    font-size: 0.9rem;
    color: #444;
    margin-bottom: 6px;
}
[data-bs-theme="dark"] .tau-form-group label { color: #ccc; }
.tau-form-group input,
.tau-form-group select,
.tau-form-group textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: inherit;
    transition: border-color .2s;
    box-sizing: border-box;
    background: #fafafa;
    color: #222;
}
[data-bs-theme="dark"] .tau-form-group input,
[data-bs-theme="dark"] .tau-form-group select,
[data-bs-theme="dark"] .tau-form-group textarea {
    background: #2a2a3e;
    border-color: #444;
    color: #eee;
}
.tau-form-group input:focus,
.tau-form-group select:focus,
.tau-form-group textarea:focus {
    outline: none;
    border-color: #c62b3a;
    box-shadow: 0 0 0 3px rgba(198,43,58,.12);
}
.tau-form-group textarea { resize: vertical; min-height: 120px; }
.tau-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 560px) { .tau-form-row { grid-template-columns: 1fr; } }
.tau-submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #c62b3a;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px 32px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s, transform .1s;
    width: 100%;
    justify-content: center;
    margin-top: 8px;
}
.tau-submit-btn:hover { background: #a32230; transform: translateY(-1px); }
.tau-alert {
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 0.9rem;
}
.tau-alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}
.tau-alert-error {
    background: #fde8ea;
    color: #9e1e2a;
    border: 1px solid #f5c6cb;
}
.tau-faq-section {
    max-width: 700px;
    margin: 0 auto 48px;
}
.tau-faq-section h2 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 16px;
}
[data-bs-theme="dark"] .tau-faq-section h2 { color: #eee; }
.tau-faq-item {
    background: #fff;
    border-radius: 10px;
    border: 1.5px solid #eee;
    margin-bottom: 10px;
    overflow: hidden;
}
[data-bs-theme="dark"] .tau-faq-item { background: #1e1e2e; border-color: #333; }
.tau-faq-q {
    padding: 14px 18px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #333;
}
[data-bs-theme="dark"] .tau-faq-q { color: #eee; }
.tau-faq-q:hover { color: #c62b3a; }
.tau-faq-a {
    padding: 0 18px;
    max-height: 0;
    overflow: hidden;
    transition: max-height .3s, padding .3s;
    font-size: 0.875rem;
    color: #666;
    line-height: 1.6;
}
[data-bs-theme="dark"] .tau-faq-a { color: #aaa; }
.tau-faq-item.open .tau-faq-a { max-height: 200px; padding: 12px 18px; }
.tau-faq-item.open .tau-faq-q { color: #c62b3a; }
</style>

<div class="tau-support-hero">
    <h1><i class="fa fa-headphones" aria-hidden="true"></i> Soporte Campus Virtual</h1>
    <p>¿Tienes algún problema o necesitas ayuda? Completa el formulario y te respondemos pronto.</p>
</div>

<?php if ($success): ?>
<div class="tau-form-card">
    <div class="tau-alert tau-alert-success">
        <strong><i class="fa fa-check-circle" aria-hidden="true"></i> ¡Solicitud enviada!</strong><br>
        Tu solicitud ha sido recibida. El equipo de soporte TAU te contactará en menos de 24 horas hábiles al correo indicado.
    </div>
    <p style="text-align:center;margin:0;">
        <a href="/local/tau_soporte/solicitud.php" class="tau-submit-btn" style="text-decoration:none;max-width:260px;display:inline-flex;">
            <i class="fa fa-plus" aria-hidden="true"></i> Nueva solicitud
        </a>
    </p>
</div>
<?php else: ?>
<div class="tau-form-card">
    <h2><i class="fa fa-envelope" aria-hidden="true"></i> Formulario de Solicitud</h2>

    <?php if (!empty($errors)): ?>
    <div class="tau-alert tau-alert-error">
        <strong>Por favor corrige los siguientes errores:</strong>
        <ul style="margin:8px 0 0;padding-left:20px;">
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" action="" novalidate>
        <input type="hidden" name="sesskey" value="<?= sesskey() ?>">

        <div class="tau-form-row">
            <div class="tau-form-group">
                <label for="f-nombre">Nombre completo *</label>
                <input type="text" id="f-nombre" name="nombre" required
                    value="<?= htmlspecialchars($_POST['nombre'] ?? (isloggedin() ? fullname($USER) : '')) ?>"
                    placeholder="Ej: María García">
            </div>
            <div class="tau-form-group">
                <label for="f-correo">Correo electrónico *</label>
                <input type="email" id="f-correo" name="correo" required
                    value="<?= htmlspecialchars($_POST['correo'] ?? ($USER->email ?? '')) ?>"
                    placeholder="tucorreo@unicesmag.edu.co">
            </div>
        </div>

        <div class="tau-form-row">
            <div class="tau-form-group">
                <label for="f-rol">Eres *</label>
                <select id="f-rol" name="rol" required>
                    <option value="">— Seleccionar —</option>
                    <option value="Estudiante"      <?= ($_POST['rol'] ?? '') === 'Estudiante'      ? 'selected' : '' ?>>Estudiante</option>
                    <option value="Docente"         <?= ($_POST['rol'] ?? '') === 'Docente'         ? 'selected' : '' ?>>Docente</option>
                    <option value="Administrativo"  <?= ($_POST['rol'] ?? '') === 'Administrativo'  ? 'selected' : '' ?>>Administrativo</option>
                    <option value="Externo"         <?= ($_POST['rol'] ?? '') === 'Externo'         ? 'selected' : '' ?>>Externo</option>
                </select>
            </div>
            <div class="tau-form-group">
                <label for="f-categoria">Tipo de solicitud *</label>
                <select id="f-categoria" name="categoria" required>
                    <option value="">— Seleccionar —</option>
                    <option value="Acceso y contraseña">Acceso y contraseña</option>
                    <option value="Inscripción a cursos">Inscripción a cursos</option>
                    <option value="Problema con tarea o actividad">Problema con tarea o actividad</option>
                    <option value="Calificaciones">Calificaciones</option>
                    <option value="Problemas técnicos">Problemas técnicos</option>
                    <option value="Certificado o constancia">Certificado o constancia</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
        </div>

        <div class="tau-form-group">
            <label for="f-mensaje">Descripción del problema o solicitud *</label>
            <textarea id="f-mensaje" name="mensaje" required
                placeholder="Describe con detalle lo que necesitas o el problema que estás experimentando..."><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="tau-submit-btn">
            <i class="fa fa-paper-plane" aria-hidden="true"></i>
            Enviar solicitud
        </button>
    </form>
</div>
<?php endif; ?>

<div class="tau-faq-section">
    <h2><i class="fa fa-question-circle" aria-hidden="true"></i> Preguntas frecuentes</h2>
    <?php
    $faqs = [
        ['¿Olvidé mi contraseña, qué hago?',
         'TAU utiliza inicio de sesión con Google institucional. Si no puedes ingresar, verifica que estés usando tu correo @unicesmag.edu.co. Si el problema persiste, contáctanos.'],
        ['¿Cómo me inscribo a un curso?',
         'Los cursos son asignados por tu programa académico. Si no ves un curso al que estás inscrito, envía una solicitud con el nombre del curso y tu código estudiantil.'],
        ['¿En cuánto tiempo me responden?',
         'El equipo de soporte atiende solicitudes en días hábiles. El tiempo de respuesta promedio es de 4 a 8 horas hábiles.'],
        ['¿Puedo acceder desde el celular?',
         'Sí. E-TAU Campus Virtual está optimizado para dispositivos móviles. También puedes descargar la aplicación oficial de Moodle y configurar nuestra URL.'],
    ];
    foreach ($faqs as $i => $faq):
    ?>
    <div class="tau-faq-item" id="faq-<?= $i ?>">
        <div class="tau-faq-q" onclick="toggleFaq(<?= $i ?>)">
            <span><?= htmlspecialchars($faq[0]) ?></span>
            <i class="fa fa-chevron-down" aria-hidden="true"></i>
        </div>
        <div class="tau-faq-a"><?= htmlspecialchars($faq[1]) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<script>
function toggleFaq(idx) {
    var el = document.getElementById('faq-' + idx);
    el.classList.toggle('open');
}
</script>

<?php echo $OUTPUT->footer(); ?>
