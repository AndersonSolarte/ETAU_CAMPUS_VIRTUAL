<?php
declare(strict_types=1);

define('CLI_SCRIPT', true);

require '/var/www/html/config.php';
require_once $CFG->dirroot . '/course/lib.php';
require_once $CFG->dirroot . '/lib/enrollib.php';
require_once $CFG->dirroot . '/user/lib.php';

$MARKER = '/var/www/moodledata/.tau_demo_seeded';

if (file_exists($MARKER)) {
    fwrite(STDOUT, "Demo data already seeded. Skipping.\n");
    exit(0);
}

fwrite(STDOUT, "Seeding E-TAU Campus Virtual demo data...\n");

// ── 1. Categorías académicas ───────────────────────────────────
$categories = [
    ['name' => 'Ingeniería y Tecnología',      'idnumber' => 'CAT-ING',  'description' => 'Programas de ingeniería de sistemas, electrónica e informática.'],
    ['name' => 'Ciencias Básicas',             'idnumber' => 'CAT-CIE',  'description' => 'Matemáticas, física, química y ciencias naturales.'],
    ['name' => 'Ciencias Administrativas',     'idnumber' => 'CAT-ADM',  'description' => 'Administración de empresas, contaduría y economía.'],
    ['name' => 'Humanidades y Comunicación',   'idnumber' => 'CAT-HUM',  'description' => 'Comunicación social, filosofía y ciencias humanas.'],
    ['name' => 'Salud',                        'idnumber' => 'CAT-SAL',  'description' => 'Enfermería, bacteriología y ciencias de la salud.'],
];

$categoryIds = [];
foreach ($categories as $catData) {
    $existing = $DB->get_record('course_categories', ['idnumber' => $catData['idnumber']]);
    if ($existing) {
        $categoryIds[$catData['idnumber']] = (int) $existing->id;
        fwrite(STDOUT, "  [exists] Category: {$catData['name']}\n");
        continue;
    }
    $cat = core_course_category::create([
        'name'        => $catData['name'],
        'idnumber'    => $catData['idnumber'],
        'description' => $catData['description'],
        'parent'      => 0,
        'visible'     => 1,
    ]);
    $categoryIds[$catData['idnumber']] = $cat->id;
    fwrite(STDOUT, "  [ok] Category: {$catData['name']} (id={$cat->id})\n");
}

// ── 2. Cursos demo ─────────────────────────────────────────────
$courses = [
    [
        'fullname'    => 'Python para Principiantes: Fundamentos de Programación',
        'shortname'   => 'PYTHON101',
        'category'    => 'CAT-ING',
        'summary'     => '<p>Aprende los fundamentos de Python desde cero. Cubre variables, estructuras de control, funciones, módulos y proyectos prácticos. Ideal para estudiantes sin experiencia previa en programación.</p>',
        'numsections' => 8,
        'format'      => 'weeks',
        'lang'        => 'es',
        'visible'     => 1,
    ],
    [
        'fullname'    => 'Ciencia de Datos con Python y Pandas',
        'shortname'   => 'DATASCI201',
        'category'    => 'CAT-ING',
        'summary'     => '<p>Domina el análisis de datos con Python, pandas, NumPy y matplotlib. Aprende a transformar, visualizar e interpretar datos reales para tomar decisiones informadas.</p>',
        'numsections' => 10,
        'format'      => 'weeks',
        'lang'        => 'es',
        'visible'     => 1,
    ],
    [
        'fullname'    => 'Gestión de Proyectos con Metodologías Ágiles',
        'shortname'   => 'AGILE301',
        'category'    => 'CAT-ADM',
        'summary'     => '<p>Aprende Scrum, Kanban y OKRs aplicados al entorno empresarial colombiano. Incluye casos de estudio reales, simulaciones y certificación interna de competencias ágiles.</p>',
        'numsections' => 6,
        'format'      => 'weeks',
        'lang'        => 'es',
        'visible'     => 1,
    ],
    [
        'fullname'    => 'Cálculo Diferencial e Integral',
        'shortname'   => 'CALC101',
        'category'    => 'CAT-CIE',
        'summary'     => '<p>Fundamentos de límites, derivadas e integrales con aplicaciones en ingeniería y ciencias. Incluye ejercicios interactivos, videos explicativos y evaluación continua.</p>',
        'numsections' => 12,
        'format'      => 'weeks',
        'lang'        => 'es',
        'visible'     => 1,
    ],
    [
        'fullname'    => 'Comunicación Efectiva y Oratoria',
        'shortname'   => 'COM201',
        'category'    => 'CAT-HUM',
        'summary'     => '<p>Desarrolla habilidades de comunicación oral y escrita para entornos académicos y profesionales. Técnicas de presentación, manejo de audiencias y comunicación digital.</p>',
        'numsections' => 6,
        'format'      => 'weeks',
        'lang'        => 'es',
        'visible'     => 1,
    ],
    [
        'fullname'    => 'Inteligencia Artificial: Aplicaciones Prácticas',
        'shortname'   => 'AI401',
        'category'    => 'CAT-ING',
        'summary'     => '<p>Explora las aplicaciones prácticas de la IA: machine learning, procesamiento de lenguaje natural y visión por computador. Proyectos reales con TensorFlow y scikit-learn.</p>',
        'numsections' => 10,
        'format'      => 'weeks',
        'lang'        => 'es',
        'visible'     => 1,
    ],
];

$courseIds = [];
foreach ($courses as $courseData) {
    $existing = $DB->get_record('course', ['shortname' => $courseData['shortname']]);
    if ($existing) {
        $courseIds[$courseData['shortname']] = (int) $existing->id;
        fwrite(STDOUT, "  [exists] Course: {$courseData['shortname']}\n");
        continue;
    }

    $catId = $categoryIds[$courseData['category']] ?? 1;
    unset($courseData['category']);

    $record              = (object) $courseData;
    $record->category    = $catId;
    $record->startdate   = mktime(0, 0, 0, 2, 1, 2025);
    $record->enddate     = mktime(0, 0, 0, 6, 30, 2025);
    $record->enablecompletion = 1;
    $record->showgrades  = 1;
    $record->showreports = 1;

    $created = create_course($record);
    $courseIds[$courseData['shortname']] = $created->id;
    fwrite(STDOUT, "  [ok] Course: {$courseData['shortname']} (id={$created->id})\n");
}

// ── 3. Usuarios docentes ───────────────────────────────────────
$teachers = [
    [
        'username'  => 'prof.rodriguez',
        'firstname' => 'Carlos',
        'lastname'  => 'Rodríguez Medina',
        'email'     => 'c.rodriguez@tau.edu.co',
        'password'  => 'Docente@2025!',
        'courses'   => ['PYTHON101', 'AI401'],
    ],
    [
        'username'  => 'prof.martinez',
        'firstname' => 'Laura',
        'lastname'  => 'Martínez López',
        'email'     => 'l.martinez@tau.edu.co',
        'password'  => 'Docente@2025!',
        'courses'   => ['DATASCI201'],
    ],
    [
        'username'  => 'prof.garcia',
        'firstname' => 'Andrés',
        'lastname'  => 'García Ospina',
        'email'     => 'a.garcia@tau.edu.co',
        'password'  => 'Docente@2025!',
        'courses'   => ['AGILE301', 'COM201', 'CALC101'],
    ],
];

$teacherRole = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);
$teacherIds  = [];

foreach ($teachers as $teacherData) {
    $courses  = $teacherData['courses'];
    unset($teacherData['courses']);
    $password = $teacherData['password'];
    unset($teacherData['password']);

    $existing = $DB->get_record('user', ['username' => $teacherData['username']]);
    if ($existing) {
        $teacherIds[] = (int) $existing->id;
        fwrite(STDOUT, "  [exists] Teacher: {$teacherData['username']}\n");
        $uid = (int) $existing->id;
    } else {
        $teacherData['auth']        = 'manual';
        $teacherData['confirmed']   = 1;
        $teacherData['mnethostid']  = $CFG->mnet_localhost_id;
        $teacherData['lang']        = 'es';
        $teacherData['country']     = 'CO';
        $teacherData['city']        = 'Pasto';
        $teacherData['description'] = 'Docente E-TAU Campus Virtual';

        $uid = user_create_user((object) $teacherData, false, false);
        $user = $DB->get_record('user', ['id' => $uid]);
        update_internal_user_password($user, $password);
        $teacherIds[] = $uid;
        fwrite(STDOUT, "  [ok] Teacher: {$teacherData['username']} (id={$uid})\n");
    }

    // Enroll in courses
    foreach ($courses as $shortname) {
        if (!isset($courseIds[$shortname])) {
            continue;
        }
        $courseId = $courseIds[$shortname];
        $context  = context_course::instance($courseId);
        if (!is_enrolled($context, $uid)) {
            $enrol = enrol_get_plugin('manual');
            $instance = $DB->get_record('enrol', ['enrol' => 'manual', 'courseid' => $courseId]);
            if ($instance) {
                $enrol->enrol_user($instance, $uid, $teacherRole->id);
                fwrite(STDOUT, "    [enroll] {$teacherData['username']} -> {$shortname} (teacher)\n");
            }
        }
    }
}

// ── 4. Usuarios estudiantes ────────────────────────────────────
$studentNames = [
    ['sofia.mendez',   'Sofía',    'Méndez Torres',     'sofia.mendez@estudiante.tau.edu.co'],
    ['juan.perez',     'Juan',     'Pérez Arango',      'juan.perez@estudiante.tau.edu.co'],
    ['maria.gomez',    'María',    'Gómez Rivera',      'maria.gomez@estudiante.tau.edu.co'],
    ['carlos.diaz',    'Carlos',   'Díaz Hernández',    'carlos.diaz@estudiante.tau.edu.co'],
    ['andrea.lopez',   'Andrea',   'López Castillo',    'andrea.lopez@estudiante.tau.edu.co'],
    ['miguel.vargas',  'Miguel',   'Vargas Quintero',   'miguel.vargas@estudiante.tau.edu.co'],
    ['isabela.ruiz',   'Isabela',  'Ruiz Salazar',      'isabela.ruiz@estudiante.tau.edu.co'],
    ['david.mora',     'David',    'Mora Benavides',    'david.mora@estudiante.tau.edu.co'],
    ['valentina.cruz', 'Valentina','Cruz Patiño',       'valentina.cruz@estudiante.tau.edu.co'],
    ['santiago.leon',  'Santiago', 'León Castaño',      'santiago.leon@estudiante.tau.edu.co'],
    ['paula.jimenez',  'Paula',    'Jiménez Coral',     'paula.jimenez@estudiante.tau.edu.co'],
    ['felipe.castro',  'Felipe',   'Castro Melo',       'felipe.castro@estudiante.tau.edu.co'],
];

$studentRole  = $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);
$allCourseIds = array_values($courseIds);
$enrol        = enrol_get_plugin('manual');

foreach ($studentNames as [$username, $firstname, $lastname, $email]) {
    $existing = $DB->get_record('user', ['username' => $username]);

    if ($existing) {
        fwrite(STDOUT, "  [exists] Student: {$username}\n");
        $uid = (int) $existing->id;
    } else {
        $uid = user_create_user((object) [
            'username'   => $username,
            'firstname'  => $firstname,
            'lastname'   => $lastname,
            'email'      => $email,
            'auth'       => 'manual',
            'confirmed'  => 1,
            'mnethostid' => $CFG->mnet_localhost_id,
            'lang'       => 'es',
            'country'    => 'CO',
            'city'       => 'Pasto',
        ], false, false);
        $user = $DB->get_record('user', ['id' => $uid]);
        update_internal_user_password($user, 'Estudiante@2025!');
        fwrite(STDOUT, "  [ok] Student: {$username} (id={$uid})\n");
    }

    // Enroll in 2-3 random courses
    $randomCourses = array_slice(shuffle_array($allCourseIds), 0, rand(2, 3));
    foreach ($randomCourses as $courseId) {
        $context  = context_course::instance($courseId);
        if (!is_enrolled($context, $uid)) {
            $instance = $DB->get_record('enrol', ['enrol' => 'manual', 'courseid' => $courseId]);
            if ($instance) {
                $enrol->enrol_user($instance, $uid, $studentRole->id);
            }
        }
    }
}

fwrite(STDOUT, "  [ok] 12 students created and enrolled.\n");

// ── Done ───────────────────────────────────────────────────────
touch($MARKER);
fwrite(STDOUT, "Demo data seeded successfully.\n");
fwrite(STDOUT, "  Categories : " . count($categories)   . "\n");
fwrite(STDOUT, "  Courses    : " . count($courses)       . "\n");
fwrite(STDOUT, "  Teachers   : " . count($teachers)      . "\n");
fwrite(STDOUT, "  Students   : " . count($studentNames)  . "\n");

// Helper: shuffle array and return it
function shuffle_array(array $arr): array {
    shuffle($arr);
    return $arr;
}
