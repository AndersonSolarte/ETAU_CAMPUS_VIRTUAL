<?php
namespace local_tau_course_creator_ai\h5p\types;

defined('MOODLE_INTERNAL') || die();

use local_tau_course_creator_ai\h5p\h5p_content_builder;

/**
 * Generates H5P.InteractiveBook content (chapter-based book with mixed content).
 */
class interactive_book {

    const MACHINE_NAME  = 'H5P.InteractiveBook';
    const MAJOR_VERSION = 1;
    const MINOR_VERSION = 7;

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
                'H5P.AdvancedText', 'H5P.Column', 'H5P.MultiChoice', 'H5P.Question',
                'H5P.JoubelUI', 'FontAwesome', 'H5P.InteractiveBook',
            ]),
        ];
    }

    public static function build_content_json(array $data): array {
        $chapters = [];
        foreach (($data['chapters'] ?? []) as $chapter) {
            $chapters[] = self::build_chapter($chapter);
        }

        if (empty($chapters)) {
            $chapters[] = self::build_chapter(['title' => 'Capítulo 1', 'textBlocks' => [['body' => '<p>Contenido</p>']]]);
        }

        $cover_title = $data['coverTitle'] ?? ($data['title'] ?? 'Libro');
        $cover_desc  = $data['coverDescription'] ?? '';

        return [
            'showCoverPage' => true,
            'bookCover' => [
                'title'    => $cover_title,
                'subtitle' => $data['coverSubtitle'] ?? '',
                'description' => [
                    'library'      => 'H5P.AdvancedText 1.1',
                    'params'       => ['text' => '<p>' . htmlspecialchars($cover_desc, ENT_QUOTES) . '</p>'],
                    'subContentId' => h5p_content_builder::uuid(),
                    'metadata'     => ['title' => 'Descripción de portada', 'license' => 'U'],
                ],
            ],
            'chapters'  => $chapters,
            'behaviour' => [
                'defaultTableOfContents' => true,
                'progressIndicators'     => true,
                'progressAuto'           => true,
                'displaySummary'         => true,
            ],
            'l10n' => [
                'read'              => 'Leído',
                'displayToc'        => 'Mostrar tabla de contenidos',
                'hideToc'           => 'Ocultar tabla de contenidos',
                'toggleNavigation'  => 'Alternar navegación',
                'closeNavigation'   => 'Cerrar navegación',
                'prev'              => 'Anterior',
                'next'              => 'Siguiente',
                'navigateToTop'     => 'Ir al inicio',
                'fullscreen'        => 'Pantalla completa',
                'exitFullscreen'    => 'Salir de pantalla completa',
                'summary'           => 'Resumen',
                'allInteractionsCompleted' => 'Completaste todas las actividades.',
                'scoreText'         => 'Obtuviste @score de @maxScore puntos.',
                'submitReport'      => 'Enviar informe',
                'restartLabel'      => 'Reiniciar',
                'finishLabel'       => 'Finalizar',
                'confirmationDialogHeader' => '¿Deseas enviar tu informe?',
                'confirmationDialogBody' => 'Asegúrate de responder todas las preguntas.',
                'confirmationDialogCancel' => 'Cancelar',
                'confirmationDialogConfirm' => 'Enviar',
            ],
        ];
    }

    private static function build_chapter(array $chapter): array {
        $column_content = [];

        // Add text blocks
        foreach (($chapter['textBlocks'] ?? []) as $block) {
            $html = '';
            if (!empty($block['heading'])) {
                $html .= '<h2>' . htmlspecialchars((string) $block['heading'], ENT_QUOTES) . '</h2>';
            }
            $html .= $block['body'] ?? '';

            $column_content[] = [
                'content' => [
                    'library'      => 'H5P.AdvancedText 1.1',
                    'params'       => ['text' => $html],
                    'subContentId' => h5p_content_builder::uuid(),
                    'metadata'     => ['title' => mb_substr(strip_tags($block['heading'] ?? 'Texto'), 0, 60), 'license' => 'U'],
                ],
                'useSeparator' => 'auto',
            ];
        }

        // Add chapter questions
        foreach (($chapter['questions'] ?? []) as $q) {
            $column_content[] = [
                'content' => self::multichoice_node($q),
                'useSeparator' => 'auto',
            ];
        }

        return [
            'params' => ['content' => $column_content],
            'library'      => 'H5P.Column 1.16',
            'subContentId' => h5p_content_builder::uuid(),
            'metadata'     => ['title' => $chapter['title'] ?? 'Capítulo', 'license' => 'U'],
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
            'metadata'     => ['title' => mb_substr(strip_tags($q['question'] ?? 'Pregunta'), 0, 60), 'license' => 'U'],
        ];
    }
}
