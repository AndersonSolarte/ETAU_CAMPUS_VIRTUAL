<?php
namespace local_tau_course_creator_ai\h5p\types;

defined('MOODLE_INTERNAL') || die();

use local_tau_course_creator_ai\h5p\h5p_content_builder;

/**
 * Generates H5P.QuestionSet content (standalone quiz with multiple-choice questions).
 */
class question_set {

    const MACHINE_NAME  = 'H5P.QuestionSet';
    const MAJOR_VERSION = 1;
    const MINOR_VERSION = 20;

    public static function build_h5p_json(string $title, string $language = 'es'): array {
        return [
            'title'       => $title,
            'language'    => $language,
            'mainLibrary' => self::MACHINE_NAME,
            'embedTypes'  => ['div'],
            'license'     => 'U',
            'authors'     => [],
            'changes'     => [],
            'preloadedDependencies' => h5p_content_builder::deps([
                'H5P.MultiChoice', 'H5P.Question', 'H5P.JoubelUI', 'FontAwesome', 'H5P.QuestionSet',
            ]),
        ];
    }

    public static function build_content_json(array $data): array {
        $questions = [];
        foreach (($data['questions'] ?? []) as $q) {
            $questions[] = self::multichoice_node($q);
        }

        return [
            'introPage' => [
                'showIntroPage'   => true,
                'startButtonText' => 'Iniciar evaluación',
                'title'           => $data['title'] ?? 'Evaluación',
                'introduction'    => $data['introduction'] ?? '<p>Responde correctamente las siguientes preguntas.</p>',
            ],
            'progressType'   => 'dots',
            'passPercentage' => (int) ($data['passPercentage'] ?? 60),
            'questions'      => $questions,
            'behaviour'      => [
                'enableRetry'           => true,
                'enableSolutionsButton' => true,
                'enableCheckButton'     => true,
                'randomQuestions'       => false,
                'maxScore'              => 0,
                'showSolutionButton'    => true,
            ],
            'l10n' => [
                'score'        => 'Puntaje',
                'time'         => 'Tiempo',
                'question'     => 'Pregunta',
                'finished'     => 'Todas las preguntas respondidas',
                'next'         => 'Siguiente pregunta',
                'prev'         => 'Pregunta anterior',
                'showSolution' => 'Ver solución',
                'retry'        => 'Intentar de nuevo',
            ],
            'override' => ['checkButton' => true],
            'endGame' => [
                'showResultPage'     => true,
                'showSolutionButton' => true,
                'showRetryButton'    => true,
                'noResultTitle'      => '¡Finalizado!',
                'showScore'          => true,
                'resultLabel'        => 'Tu resultado:',
                'finishedVideoFileName' => '',
            ],
        ];
    }

    private static function multichoice_node(array $q): array {
        $correct_idx = (int) ($q['correct'] ?? 0);
        $answers     = [];
        foreach (($q['answers'] ?? []) as $i => $text) {
            $is_correct = ($i === $correct_idx);
            $answers[]  = [
                'correct'         => $is_correct,
                'tipsAndFeedback' => [
                    'tip'              => '',
                    'chosenFeedback'   => $is_correct ? ($q['feedback'] ?? 'Correcto.') : 'Respuesta incorrecta.',
                    'notChosenFeedback'=> '',
                ],
                'text' => '<div>' . htmlspecialchars((string) $text, ENT_QUOTES) . '</div>',
            ];
        }

        return [
            'library'      => 'H5P.MultiChoice 1.16',
            'params'       => [
                'question'  => $q['question'] ?? '<p>Pregunta</p>',
                'answers'   => $answers,
                'behaviour' => [
                    'enableRetry'               => true,
                    'enableSolutionsButton'      => true,
                    'enableCheckButton'          => true,
                    'type'                       => 'auto',
                    'singlePoint'                => false,
                    'randomAnswers'              => true,
                    'showSolutionsRequiresInput' => true,
                    'autoCheck'                  => false,
                    'passPercentage'             => 100,
                    'showScorePoints'            => true,
                ],
                'UI'    => self::multichoice_ui(),
                'media' => ['disableImageZooming' => false],
            ],
            'subContentId' => h5p_content_builder::uuid(),
            'metadata'     => [
                'title'   => mb_substr(strip_tags($q['question'] ?? 'Pregunta'), 0, 60),
                'license' => 'U',
            ],
        ];
    }

    private static function multichoice_ui(): array {
        return [
            'checkAnswerButton'  => 'Verificar',
            'submitAnswerButton' => 'Enviar',
            'showSolutionButton' => 'Ver solución',
            'tryAgainButton'     => 'Intentar de nuevo',
            'tipsLabel'          => 'Mostrar pista',
            'scoreBarLabel'      => 'Obtuviste :num de :total puntos',
            'tipAvailable'       => 'Pista disponible',
            'feedbackAvailable'  => 'Retroalimentación disponible',
            'readFeedback'       => 'Leer retroalimentación',
            'wrongAnswer'        => 'Respuesta incorrecta',
            'correctAnswer'      => 'Respuesta correcta',
            'shouldCheck'        => 'Debía marcarse',
            'shouldNotCheck'     => 'No debía marcarse',
            'noInput'            => 'Responde antes de ver la solución',
        ];
    }
}
