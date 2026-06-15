<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');

// ── 1. Borrar todos los cursos demo ──────────────────────────────────────────
$demo_ids = [2, 3, 4, 5, 6, 7, 8];
foreach ($demo_ids as $id) {
    $course = $DB->get_record('course', ['id' => $id]);
    if ($course) {
        delete_course($course, false);
        echo "Curso eliminado: [{$id}] {$course->fullname}\n";
    }
}

// ── 2. Obtener o crear categoría ─────────────────────────────────────────────
$cat = $DB->get_record('course_categories', ['idnumber' => 'TAU_TI']);
if (!$cat) {
    $catdata = new stdClass();
    $catdata->name      = 'Tecnología e Innovación';
    $catdata->idnumber  = 'TAU_TI';
    $catdata->parent    = 0;
    $catdata->visible   = 1;
    $cat = core_course_category::create($catdata);
    echo "Categoría creada: Tecnología e Innovación (id={$cat->id})\n";
} else {
    echo "Categoría existente: id={$cat->id}\n";
}

// ── 3. Crear el curso ────────────────────────────────────────────────────────
$course_data = new stdClass();
$course_data->fullname       = 'Ciberseguridad Profesional: Protección de Sistemas y Redes';
$course_data->shortname      = 'CIBERSEG-101';
$course_data->category       = $cat->id;
$course_data->visible        = 1;
$course_data->format         = 'topics';
$course_data->numsections    = 8;
$course_data->startdate      = mktime(0, 0, 0, 6, 2, 2026);
$course_data->enddate        = mktime(0, 0, 0, 8, 31, 2026);
$course_data->lang           = 'es';
$course_data->enablecompletion = 1;
$course_data->showgrades     = 1;
$course_data->showreports    = 0;
$course_data->idnumber       = 'TAU-CYBER-101';
$course_data->summary        = '<p>Domina los fundamentos y técnicas avanzadas de la ciberseguridad.
Este curso profesional te preparará para proteger infraestructuras digitales, identificar vulnerabilidades,
responder a incidentes y aplicar marcos normativos internacionales. Ideal para profesionales de TI,
administradores de sistemas y todos aquellos que deseen certificarse en seguridad informática.</p>
<ul>
<li>80 horas de contenido estructurado</li>
<li>Laboratorios prácticos con herramientas reales</li>
<li>Certificado de finalización TAU-CESMAG</li>
<li>Preparación para CompTIA Security+, CEH y CISSP</li>
</ul>';

$course = create_course($course_data);
echo "Curso creado: id={$course->id} — {$course->fullname}\n";

// ── 4. Crear secciones y actividades ────────────────────────────────────────
$sections = [
    1 => [
        'name'    => 'Módulo 1 — Fundamentos de Ciberseguridad',
        'summary' => 'Conceptos esenciales, terminología del sector y el panorama actual de amenazas digitales.',
        'pages'   => [
            'Introducción a la Ciberseguridad y el panorama de amenazas',
            'Principios CIA: Confidencialidad, Integridad y Disponibilidad',
            'Tipos de atacantes: hackers, APT y cibercrimen organizado',
            'Marcos normativos: ISO 27001, NIST, GDPR y normativa colombiana',
        ],
    ],
    2 => [
        'name'    => 'Módulo 2 — Redes y Protocolo de Seguridad',
        'summary' => 'Arquitectura de redes, protocolos seguros y diseño de perímetros de defensa.',
        'pages'   => [
            'Modelos OSI y TCP/IP desde la perspectiva de seguridad',
            'Firewall, IDS/IPS y arquitecturas de red segura',
            'VPN, TLS/SSL y comunicaciones cifradas',
            'Segmentación de redes y zonas DMZ',
        ],
    ],
    3 => [
        'name'    => 'Módulo 3 — Criptografía Aplicada',
        'summary' => 'Principios matemáticos de la criptografía y su aplicación práctica en sistemas modernos.',
        'pages'   => [
            'Criptografía simétrica: AES, DES y 3DES',
            'Criptografía asimétrica: RSA, ECC y clave pública',
            'Funciones hash, firmas digitales y certificados X.509',
            'PKI, autoridades de certificación y gestión de claves',
        ],
    ],
    4 => [
        'name'    => 'Módulo 4 — Hacking Ético y Pentesting',
        'summary' => 'Metodologías de pruebas de penetración, reconocimiento y explotación controlada.',
        'pages'   => [
            'Metodología de pentesting: fases PTES y OWASP',
            'Reconocimiento pasivo y activo: OSINT, Nmap y Shodan',
            'Explotación de vulnerabilidades con Metasploit Framework',
            'Informe de pentest: evidencias, riesgo y remediación',
        ],
    ],
    5 => [
        'name'    => 'Módulo 5 — Seguridad en Aplicaciones Web',
        'summary' => 'OWASP Top 10, análisis de código y técnicas de desarrollo seguro.',
        'pages'   => [
            'OWASP Top 10: las vulnerabilidades más críticas en aplicaciones web',
            'SQL Injection, XSS y CSRF: análisis y mitigación',
            'Autenticación segura, JWT y gestión de sesiones',
            'DevSecOps: integración de seguridad en el ciclo de desarrollo',
        ],
    ],
    6 => [
        'name'    => 'Módulo 6 — Seguridad en la Nube e Identidad',
        'summary' => 'Protección de entornos cloud, IAM y arquitecturas Zero Trust.',
        'pages'   => [
            'Modelos de responsabilidad compartida: AWS, Azure y GCP',
            'Gestión de identidad y acceso (IAM) y autenticación multifactor',
            'Arquitectura Zero Trust: principios y despliegue',
            'CASB, SASE y seguridad en contenedores y Kubernetes',
        ],
    ],
    7 => [
        'name'    => 'Módulo 7 — Respuesta a Incidentes y Forense Digital',
        'summary' => 'Detección, contención y análisis forense de incidentes de seguridad.',
        'pages'   => [
            'Ciclo de vida de un incidente: preparación, detección y contención',
            'SIEM, análisis de logs y correlación de eventos',
            'Forense digital: adquisición y análisis de evidencias',
            'Recuperación ante desastres y continuidad del negocio (BCP/DRP)',
        ],
    ],
    8 => [
        'name'    => 'Módulo 8 — Proyecto Final y Certificación',
        'summary' => 'Evaluación integral y obtención del certificado TAU-CESMAG.',
        'pages'   => [
            'Auditoría de seguridad integral: laboratorio final',
            'Elaboración del informe ejecutivo de seguridad',
        ],
    ],
];

// Crear páginas en cada sección
$modinfo_course = get_fast_modinfo($course);

foreach ($sections as $section_num => $section) {
    // Actualizar nombre y resumen de la sección
    $DB->set_field('course_sections', 'name',    $section['name'],    ['course' => $course->id, 'section' => $section_num]);
    $DB->set_field('course_sections', 'summary', $section['summary'], ['course' => $course->id, 'section' => $section_num]);
    $DB->set_field('course_sections', 'summaryformat', FORMAT_HTML,   ['course' => $course->id, 'section' => $section_num]);

    foreach ($section['pages'] as $page_title) {
        $page              = new stdClass();
        $page->course      = $course->id;
        $page->name        = $page_title;
        $page->intro       = '';
        $page->introformat = FORMAT_HTML;
        $page->content     = '<p>Contenido del tema: <strong>' . htmlspecialchars($page_title) . '</strong>.</p>
<p>Este módulo cubre en detalle los conceptos, herramientas y ejercicios prácticos necesarios para dominar este tema dentro del campo de la ciberseguridad profesional.</p>';
        $page->contentformat  = FORMAT_HTML;
        $page->display        = 5; // open in new window / same page
        $page->displayoptions = serialize(['printheading' => 1, 'printintro' => 0]);
        $page->timemodified   = time();

        $page->id = $DB->insert_record('page', $page);

        // Añadir el módulo a la sección
        $cm              = new stdClass();
        $cm->course      = $course->id;
        $cm->module      = $DB->get_field('modules', 'id', ['name' => 'page']);
        $cm->instance    = $page->id;
        $cm->section     = $DB->get_field('course_sections', 'id', ['course' => $course->id, 'section' => $section_num]);
        $cm->visible     = 1;
        $cm->added       = time();
        $cm->completion  = COMPLETION_TRACKING_MANUAL;
        $cm->id          = add_course_module($cm);
        course_add_cm_to_section($course, $cm->id, $section_num);
        echo "  + Página: {$page_title}\n";
    }

    echo "Módulo {$section_num} configurado: {$section['name']}\n";
}

// ── 5. Habilitar matrícula CESMAG en el curso ─────────────────────────────────
$enrol_plugin = enrol_get_plugin('cesmag');
$enrol_plugin->add_instance($course, [
    'status'      => ENROL_INSTANCE_ENABLED,
    'customtext1' => 'https://ruah.unicesmag.edu.co/generar-recibos-pagos',
    'cost'        => '250.000',
]);
echo "Matrícula CESMAG agregada al curso.\n";

// ── 6. Habilitar también matrícula manual para el admin ──────────────────────
$manual_plugin = enrol_get_plugin('manual');
if (!$DB->record_exists('enrol', ['courseid' => $course->id, 'enrol' => 'manual'])) {
    $manual_plugin->add_instance($course);
}

rebuild_course_cache($course->id, true);
echo "\n=== CURSO LISTO ===\n";
echo "URL: {$CFG->wwwroot}/course/view.php?id={$course->id}\n";
echo "Certificado: {$CFG->wwwroot}/local/tau_certificate/view.php?courseid={$course->id}\n";
