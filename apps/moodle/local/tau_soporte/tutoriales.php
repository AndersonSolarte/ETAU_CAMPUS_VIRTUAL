<?php
require_once('../../config.php');

$PAGE->set_url('/local/tau_soporte/tutoriales.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Tutoriales — TAU Campus Virtual');
$PAGE->set_heading('Tutoriales de uso de la plataforma');
$PAGE->set_pagelayout('standard');

$tutorials = [
    [
        'title'    => 'Cómo ingresar al Campus Virtual',
        'desc'     => 'Aprende a acceder por primera vez y a navegar por la plataforma TAU.',
        'duration' => '2 min',
        'color'    => '#c62b3a',
        'icon'     => 'fa-sign-in-alt',
        'video'    => '',
    ],
    [
        'title'    => 'Cómo ver tus cursos',
        'desc'     => 'Encuentra y accede a todos tus cursos inscritos desde el área personal.',
        'duration' => '2 min',
        'color'    => '#2d6a9f',
        'icon'     => 'fa-book-open',
        'video'    => '',
    ],
    [
        'title'    => 'Cómo entregar una tarea',
        'desc'     => 'Paso a paso para subir archivos y enviar tus actividades a tiempo.',
        'duration' => '3 min',
        'color'    => '#1a7a4a',
        'icon'     => 'fa-upload',
        'video'    => '',
    ],
    [
        'title'    => 'Cómo participar en un foro',
        'desc'     => 'Comunícate con docentes y compañeros en los foros de discusión.',
        'duration' => '2 min',
        'color'    => '#e07b00',
        'icon'     => 'fa-comments',
        'video'    => '',
    ],
    [
        'title'    => 'Cómo realizar un cuestionario',
        'desc'     => 'Completa evaluaciones en línea correctamente y sin errores.',
        'duration' => '4 min',
        'color'    => '#6a35a0',
        'icon'     => 'fa-tasks',
        'video'    => '',
    ],
    [
        'title'    => 'Cómo revisar tus calificaciones',
        'desc'     => 'Consulta tu progreso académico y retroalimentación de tus docentes.',
        'duration' => '2 min',
        'color'    => '#1a7a7a',
        'icon'     => 'fa-chart-bar',
        'video'    => '',
    ],
];

// Handle video modal request
$videoid = optional_param('video', '', PARAM_ALPHANUMEXT);

echo $OUTPUT->header();
?>

<style>
.tau-tutorials-hero {
    background: linear-gradient(135deg, #c62b3a 0%, #7a1020 100%);
    color: #fff;
    padding: 56px 24px 40px;
    text-align: center;
    margin: -16px -16px 40px;
    border-radius: 0 0 24px 24px;
}
.tau-tutorials-hero h1 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 10px;
    color: #fff;
}
.tau-tutorials-hero p {
    font-size: 1.05rem;
    opacity: .88;
    margin: 0;
}
.tau-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 48px;
}
.tau-tut-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
    transition: transform .2s, box-shadow .2s;
    display: flex;
    flex-direction: column;
}
.tau-tut-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(0,0,0,.14);
}
.tau-tut-thumb {
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.tau-tut-thumb .thumb-icon {
    font-size: 3rem;
    color: rgba(255,255,255,.9);
}
.tau-tut-thumb .play-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0,0,0,.5);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.tau-tut-body {
    padding: 20px 20px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.tau-tut-body h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 8px;
    line-height: 1.35;
}
.tau-tut-body p {
    font-size: 0.875rem;
    color: #666;
    flex: 1;
    margin: 0 0 16px;
    line-height: 1.55;
}
.tau-tut-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    background: #c62b3a;
    color: #fff !important;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 0.88rem;
    font-weight: 600;
    text-decoration: none !important;
    cursor: pointer;
    transition: background .15s;
}
.tau-tut-btn:hover { background: #a32230; }

/* Dark mode */
[data-bs-theme="dark"] .tau-tut-card { background: #1e1e2e; }
[data-bs-theme="dark"] .tau-tut-body h3 { color: #eee; }
[data-bs-theme="dark"] .tau-tut-body p { color: #aaa; }

/* Modal */
.tau-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.75);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}
.tau-modal-backdrop.open { display: flex; }
.tau-modal-box {
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    width: min(720px, 95vw);
    position: relative;
}
.tau-modal-close {
    position: absolute;
    top: 8px; right: 8px;
    background: rgba(255,255,255,.2);
    border: none;
    color: #fff;
    width: 34px; height: 34px;
    border-radius: 50%;
    font-size: 1.1rem;
    cursor: pointer;
    z-index: 10;
    display: flex; align-items: center; justify-content: center;
}
.tau-modal-close:hover { background: rgba(255,255,255,.35); }
#tau-modal-iframe {
    width: 100%;
    aspect-ratio: 16/9;
    border: none;
    display: block;
}
.tau-no-video-msg {
    padding: 40px 24px;
    text-align: center;
    color: #fff;
    font-size: 0.95rem;
}
</style>

<div class="tau-tutorials-hero">
    <h1><i class="fa fa-play-circle" aria-hidden="true"></i> Capacitación Docente y Estudiantil</h1>
    <p>Recursos y tutoriales en video para sacar el máximo provecho de TAU Campus Virtual</p>
</div>

<div class="tau-card-grid">
<?php foreach ($tutorials as $idx => $tut): ?>
    <div class="tau-tut-card">
        <div class="tau-tut-thumb" style="background: linear-gradient(135deg, <?= htmlspecialchars($tut['color']) ?> 0%, <?= htmlspecialchars(substr($tut['color'], 0, 7)) ?>aa 100%);">
            <i class="fa <?= htmlspecialchars($tut['icon']) ?> thumb-icon" aria-hidden="true"></i>
            <span class="play-badge"><i class="fa fa-clock-o" aria-hidden="true"></i> <?= htmlspecialchars($tut['duration']) ?></span>
        </div>
        <div class="tau-tut-body">
            <h3><?= htmlspecialchars($tut['title']) ?></h3>
            <p><?= htmlspecialchars($tut['desc']) ?></p>
            <button class="tau-tut-btn" onclick="openTutorial(<?= $idx ?>)">
                <i class="fa fa-play-circle" aria-hidden="true"></i>
                Ver tutorial
            </button>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- Video modal -->
<div class="tau-modal-backdrop" id="tau-modal" role="dialog" aria-modal="true" aria-label="Tutorial en video">
    <div class="tau-modal-box">
        <button class="tau-modal-close" onclick="closeTutorial()" aria-label="Cerrar">&times;</button>
        <iframe id="tau-modal-iframe" allowfullscreen allow="autoplay; encrypted-media"></iframe>
        <div class="tau-no-video-msg" id="tau-no-video" style="display:none;">
            <i class="fa fa-film fa-2x" style="opacity:.5;margin-bottom:12px;display:block;" aria-hidden="true"></i>
            <strong>Próximamente</strong><br>
            Este tutorial estará disponible en breve. Contacta al equipo de soporte para más información.
        </div>
    </div>
</div>

<script>
var TUTORIALS = <?= json_encode(array_column($tutorials, 'video')) ?>;

function openTutorial(idx) {
    var videoId = TUTORIALS[idx];
    var modal = document.getElementById('tau-modal');
    var iframe = document.getElementById('tau-modal-iframe');
    var noVideo = document.getElementById('tau-no-video');
    modal.classList.add('open');
    if (videoId) {
        iframe.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
        iframe.style.display = 'block';
        noVideo.style.display = 'none';
    } else {
        iframe.src = '';
        iframe.style.display = 'none';
        noVideo.style.display = 'block';
    }
}

function closeTutorial() {
    document.getElementById('tau-modal').classList.remove('open');
    document.getElementById('tau-modal-iframe').src = '';
}

document.getElementById('tau-modal').addEventListener('click', function(e) {
    if (e.target === this) closeTutorial();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeTutorial();
});
</script>

<?php echo $OUTPUT->footer(); ?>
