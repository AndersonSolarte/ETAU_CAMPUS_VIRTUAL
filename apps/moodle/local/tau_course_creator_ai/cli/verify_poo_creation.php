<?php
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
set_debugging(DEBUG_DEVELOPER, true);
require_once($CFG->dirroot . '/local/tau_course_creator_ai/classes/course_builder.php');

global $DB, $USER;

$admin = get_admin();
if (!$admin) {
    fwrite(STDERR, 'Admin user not found.' . PHP_EOL);
    exit(1);
}
$USER = $admin;
\core\session\manager::set_user($admin);

$coursename = 'La Programación Orientada a Objetos (POO)';
$shortname = 'poo_test_' . date('His');

// Let's create a representative blueprint testing every single activity type!
$blueprint = [
    'courseName' => $coursename,
    'teacherName' => 'Ander',
    'courseDescription' => 'Curso de Programación Orientada a Objetos (POO). Aprende clases, objetos, herencia, polimorfismo, encapsulamiento y abstracción.',
    'sections' => [
        // Section 1: Información General
        [
            'title' => 'Información General',
            'summary' => 'Bienvenido al curso de Programación Orientada a Objetos. Aquí encontrarás el syllabus, el foro de consultas y las pautas generales.',
            'activities' => [
                [
                    'type' => 'page',
                    'title' => 'Presentación del Docente Ander',
                    'description' => 'Conoce al docente y su trayectoria profesional en desarrollo de software.'
                ],
                [
                    'type' => 'forum',
                    'title' => 'Foro de Inquietudes y Dudas',
                    'description' => 'Plantea aquí todas tus consultas técnicas o metodológicas durante el curso.'
                ]
            ]
        ],
        // Section 2: Módulo 1
        [
            'title' => 'Módulo 1: Introducción a la POO',
            'summary' => 'Descubre qué es el paradigma orientado a objetos y cómo se diferencia de la programación estructurada.',
            'activities' => []
        ],
        // Section 3: Semana 1 (Presentation + URL)
        [
            'title' => 'Módulo 1 — Semana 1: Clases y Objetos',
            'summary' => 'Comprende la definición de clase como plantilla y de objeto como instancia concreta.',
            'activities' => [
                [
                    'type' => 'resource',
                    'title' => 'Presentación Interactiva: Clases y Objetos',
                    'description' => 'Diapositivas animadas explicando el concepto de instancia, estados y comportamientos.'
                ],
                [
                    'type' => 'url',
                    'title' => 'Lectura: La historia del paradigma de objetos',
                    'description' => 'Historia y evolución de la POO desde Simula y Smalltalk hasta los lenguajes modernos.',
                    'externalurl' => 'https://example.com/historia-poo'
                ]
            ]
        ],
        // Section 4: Semana 2 (Quiz + Assign)
        [
            'title' => 'Módulo 1 — Semana 2: Atributos y Métodos',
            'summary' => 'Aprende a definir las variables miembro y las funciones que operan sobre los objetos.',
            'activities' => [
                [
                    'type' => 'quiz',
                    'title' => 'Evaluación Rápida: Atributos y Métodos',
                    'description' => 'Valida tus conocimientos teóricos y prácticos sobre definición de miembros de clase.',
                    'questions' => [
                        [
                            'question' => '¿Qué es un atributo en POO?',
                            'answers' => [
                                'Una función que ejecuta una acción.',
                                'Una variable que almacena el estado de un objeto.',
                                'Un objeto instanciado a partir de otra clase.',
                                'La firma de una clase.'
                            ],
                            'correct' => 1,
                            'feedback' => 'Correcto. Los atributos representan el estado o las variables miembro del objeto.'
                        ],
                        [
                            'question' => '¿Qué define a un método?',
                            'answers' => [
                                'El valor actual de las variables de instancia.',
                                'El comportamiento o las acciones que puede realizar un objeto.',
                                'El identificador único del objeto.',
                                'El proceso de liberar memoria.'
                            ],
                            'correct' => 1,
                            'feedback' => 'Correcto. Los métodos definen el comportamiento de los objetos de una clase.'
                        ],
                        [
                            'question' => '¿Qué es el constructor de una clase?',
                            'answers' => [
                                'Un método especial llamado al crear un objeto para inicializar sus atributos.',
                                'Una herramienta del compilador.',
                                'Una clase que hereda de otra.',
                                'El destructor de memoria.'
                            ],
                            'correct' => 0,
                            'feedback' => 'Correcto. El constructor inicializa los valores del objeto al instanciarlo.'
                        ],
                        [
                            'question' => 'Para crear un objeto en Java o C#, ¿qué palabra clave se utiliza habitualmente?',
                            'answers' => [
                                'class',
                                'create',
                                'new',
                                'instantiate'
                            ],
                            'correct' => 2,
                            'feedback' => 'Correcto. La palabra clave "new" asigna memoria y llama al constructor.'
                        ],
                        [
                            'question' => '¿Qué es una instancia?',
                            'answers' => [
                                'La definición abstracta de un concepto.',
                                'Un objeto concreto creado a partir de una clase.',
                                'Una biblioteca externa.',
                                'Un tipo de dato primitivo.'
                            ],
                            'correct' => 1,
                            'feedback' => 'Correcto. Un objeto es una instancia física e individual de una clase.'
                        ]
                    ]
                ],
                [
                    'type' => 'assign',
                    'title' => 'Entregable 1: Creando mi primera clase',
                    'description' => 'Diseña una clase Persona con atributos nombre, edad y un método saludar(). Sube tu archivo con el código fuente.'
                ]
            ]
        ],
        // Section 5: Módulo 2
        [
            'title' => 'Módulo 2: Pilares de la POO',
            'summary' => 'Explora a fondo los pilares que dan poder y flexibilidad al desarrollo de software orientado a objetos.',
            'activities' => []
        ],
        // Section 6: Semana 3 (Glossary)
        [
            'title' => 'Módulo 2 — Semana 3: Encapsulamiento y Abstracción',
            'summary' => 'Oculta la complejidad interna usando modificadores de acceso y exponiendo solo la interfaz pública necesaria.',
            'activities' => [
                [
                    'type' => 'glossary',
                    'title' => 'Glosario de Términos del Módulo 2',
                    'description' => 'Conceptos clave sobre los pilares del diseño orientado a objetos.',
                    'terms' => [
                        [
                            'concept' => 'Encapsulamiento',
                            'definition' => 'Ocultamiento del estado o de los detalles internos de un objeto, exponiendo solo lo necesario.'
                        ],
                        [
                            'concept' => 'Abstracción',
                            'definition' => 'Proceso de identificar y extraer los elementos esenciales de un objeto ignorando detalles secundarios.'
                        ],
                        [
                            'concept' => 'Modificador de acceso',
                            'definition' => 'Palabra clave (ej: public, private, protected) que define la visibilidad de atributos o métodos.'
                        ],
                        [
                            'concept' => 'Método Getter',
                            'definition' => 'Método público utilizado para obtener de forma segura el valor de un atributo privado.'
                        ],
                        [
                            'concept' => 'Método Setter',
                            'definition' => 'Método público utilizado para establecer o modificar de forma segura el valor de un atributo privado, aplicando validaciones.'
                        ]
                    ]
                ]
            ]
        ],
        // Section 7: Semana 4 (Feedback)
        [
            'title' => 'Módulo 2 — Semana 4: Herencia y Polimorfismo',
            'summary' => 'Reutiliza código permitiendo que una clase derive de otra, y redefine comportamientos para múltiples formas.',
            'activities' => [
                [
                    'type' => 'page',
                    'title' => 'Guía de Estudio: Herencia y Polimorfismo',
                    'description' => 'Conceptos de superclase, subclase, sobreescritura de métodos y enlazado dinámico.'
                ],
                [
                    'type' => 'feedback',
                    'title' => 'Pulso de Cierre: Encuesta del Curso',
                    'description' => 'Completa esta breve encuesta para contarnos tu experiencia de aprendizaje.'
                ]
            ]
        ]
    ]
];

fwrite(STDOUT, "🚀 Starting course builder validation..." . PHP_EOL);

$builder = new \local_tau_course_creator_ai\course_builder(function(string $message) {
    fwrite(STDOUT, "  🌿 " . $message . PHP_EOL);
});

try {
    $courseid = $builder->build($blueprint, 8);
    fwrite(STDOUT, PHP_EOL . "✅ SUCCESS! Course created successfully." . PHP_EOL);
    fwrite(STDOUT, "Course ID: " . $courseid . PHP_EOL);
    
    // Output stats from the DB
    $course = $DB->get_record('course', ['id' => $courseid]);
    fwrite(STDOUT, "Course Fullname: " . $course->fullname . PHP_EOL);
    fwrite(STDOUT, "Course Shortname: " . $course->shortname . PHP_EOL);
    
    $sections = $DB->get_records('course_sections', ['course' => $courseid], 'section ASC');
    fwrite(STDOUT, "Total section records created: " . count($sections) . PHP_EOL);
    foreach ($sections as $s) {
        if ($s->section > 0) {
            fwrite(STDOUT, "  - Section {$s->section}: " . ($s->name ?: '[No name]') . PHP_EOL);
        }
    }
    
    $cm_count = $DB->count_records('course_modules', ['course' => $courseid]);
    fwrite(STDOUT, "Total course modules created: " . $cm_count . PHP_EOL);
    
    foreach (['page', 'quiz', 'forum', 'assign', 'url', 'glossary', 'feedback'] as $type) {
        $count = $DB->count_records($type, ['course' => $courseid]);
        fwrite(STDOUT, "  - Instances of {$type}: " . $count . PHP_EOL);
    }
    
    // Check specific activity properties
    // 1. Assignment upload enabled
    $assigns = $DB->get_records('assign', ['course' => $courseid]);
    foreach ($assigns as $a) {
        $file_enabled = $DB->get_field('assign_plugin_config', 'value', ['assignment' => $a->id, 'plugin' => 'file', 'name' => 'enabled']);
        $text_enabled = $DB->get_field('assign_plugin_config', 'value', ['assignment' => $a->id, 'plugin' => 'onlinetext', 'name' => 'enabled']);
        fwrite(STDOUT, "  - Assignment '{$a->name}': file_upload={$file_enabled}, online_text={$text_enabled}" . PHP_EOL);
    }
    
    // 2. Quiz questions count
    $quizzes = $DB->get_records('quiz', ['course' => $courseid]);
    foreach ($quizzes as $q) {
        $slots_count = $DB->count_records('quiz_slots', ['quizid' => $q->id]);
        fwrite(STDOUT, "  - Quiz '{$q->name}': slots_count={$slots_count} questions" . PHP_EOL);
    }
    
    // 3. Glossary terms count
    $glossaries = $DB->get_records('glossary', ['course' => $courseid]);
    foreach ($glossaries as $g) {
        $terms_count = $DB->count_records('glossary_entries', ['glossaryid' => $g->id]);
        fwrite(STDOUT, "  - Glossary '{$g->name}': terms_count={$terms_count} terms" . PHP_EOL);
    }

} catch (\Throwable $e) {
    fwrite(STDERR, PHP_EOL . "❌ ERROR during course build: " . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
}
