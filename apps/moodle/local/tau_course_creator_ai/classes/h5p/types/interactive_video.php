<?php
namespace local_tau_course_creator_ai\h5p\types;

defined('MOODLE_INTERNAL') || die();

use local_tau_course_creator_ai\h5p\h5p_content_builder;

/**
 * Generates H5P.InteractiveVideo content (video with embedded question overlays).
 *
 * AI data schema:
 * {
 *   "title": "...",
 *   "videoUrl": "https://www.youtube.com/watch?v=...",
 *   "interactions": [
 *     {"secondsIn": 30, "question": "<p>...</p>", "answers": ["correct","A","B"], "correct": 0, "feedback": "..."}
 *   ],
 *   "summaryStatements": ["Correct stmt", "Wrong A", "Wrong B"],
 *   "correctSummaryIndex": 0
 * }
 */
class interactive_video {

    const MACHINE_NAME  = 'H5P.InteractiveVideo';
    const MAJOR_VERSION = 1;
    const MINOR_VERSION = 26;

    public static function build_h5p_json(string $title, string $language = 'es'): array {
        return [
            'title'       => $title,
            'language'    => $language,
            'mainLibrary' => self::MACHINE_NAME,
            'embedTypes'  => ['iframe'],
            'license'     => 'U',
            'authors'     => [],
            'changes'     => [],
            'preloadedDependencies' => h5p_content_builder::deps([
                'H5P.SingleChoiceSet', 'H5P.Summary', 'H5P.JoubelUI', 'FontAwesome',
                'H5P.Transition', 'H5P.Video', 'H5P.Question', 'H5P.InteractiveVideo',
            ]),
        ];
    }

    public static function build_content_json(array $data): array {
        $video_url    = $data['videoUrl'] ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $interactions = [];
        $last_seconds = 0;

        foreach (($data['interactions'] ?? []) as $ix) {
            $seconds     = (int) ($ix['secondsIn'] ?? 30);
            $last_seconds = max($last_seconds, $seconds);
            $interactions[] = self::build_interaction($ix, $seconds);
        }

        // Build summary task from summaryStatements
        $summary_stmts = $data['summaryStatements'] ?? [];
        $correct_idx   = (int) ($data['correctSummaryIndex'] ?? 0);
        $summary_task  = self::build_summary($summary_stmts, $correct_idx, $last_seconds + 5);

        return [
            'interactiveVideo' => [
                'video' => [
                    'startScreenOptions' => [
                        'title'          => $data['title'] ?? 'Video interactivo',
                        'hideStartTitle' => false,
                    ],
                    'textTracks' => ['videoTrack' => []],
                    'files'      => [
                        self::video_file($video_url),
                    ],
                ],
                'assets'  => [
                    'interactions' => $interactions,
                    'bookmarks'    => [],
                ],
                'summary' => $summary_task,
            ],
            'override' => [
                'autoplay'                => false,
                'loop'                    => false,
                'showBookmarksmenuOnLoad' => false,
                'showRewind10'            => true,
                'preventSkipping'         => false,
                'deactivateSound'         => false,
            ],
            'l10n' => [
                'interaction'            => 'Interacción',
                'play'                   => 'Reproducir',
                'pause'                  => 'Pausar',
                'mute'                   => 'Silenciar',
                'unmute'                 => 'Activar sonido',
                'quality'                => 'Calidad de video',
                'captions'               => 'Subtítulos',
                'close'                  => 'Cerrar',
                'fullscreen'             => 'Pantalla completa',
                'exitFullscreen'         => 'Salir de pantalla completa',
                'summary'                => 'Abrir resumen',
                'bookmarks'              => 'Marcadores',
                'defaultAdaptivitySeekLabel' => 'Continuar',
                'continueWithVideo'      => 'Continuar con el video',
                'playbackRate'           => 'Velocidad de reproducción',
                'rewind10'               => 'Retroceder 10 segundos',
                'navDisabled'            => 'La navegación está desactivada',
                'navForwardsDisabled'    => 'Avanzar está desactivado',
            ],
        ];
    }

    private static function video_file(string $url): array {
        $is_youtube = strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false;
        return [
            'path'      => $url,
            'mime'      => $is_youtube ? 'video/YouTube' : 'video/mp4',
            'copyright' => ['license' => 'U'],
        ];
    }

    private static function build_interaction(array $ix, int $seconds): array {
        $correct_idx = (int) ($ix['correct'] ?? 0);
        $answers     = $ix['answers'] ?? ['Respuesta A', 'Respuesta B', 'Respuesta C'];

        // Build SingleChoiceSet choices — correct answer is always answers[$correct_idx]
        $choices = [];
        foreach ($answers as $i => $answer) {
            if ($i === $correct_idx) {
                // Put correct first (SingleChoiceSet treats answers[0] as correct)
                array_unshift($choices, $answer);
            } else {
                $choices[] = $answer;
            }
        }

        $sub_id = h5p_content_builder::uuid();

        return [
            'x'            => 10,
            'y'            => 10,
            'width'        => 80,
            'height'       => 35,
            'duration'     => ['from' => $seconds, 'to' => $seconds + 12],
            'libraryTitle' => 'Single Choice Set',
            'action'       => [
                'library'      => 'H5P.SingleChoiceSet 1.11',
                'params'       => [
                    'choices' => [
                        [
                            'subContentId' => h5p_content_builder::uuid(),
                            'question'     => $ix['question'] ?? '<p>Pregunta</p>',
                            'answers'      => $choices,
                        ],
                    ],
                    'overallFeedback' => [
                        ['from' => 0, 'to' => 100, 'feedback' => $ix['feedback'] ?? ''],
                    ],
                    'behaviour' => [
                        'autoContinue'         => false,
                        'timeoutCorrect'       => 2000,
                        'timeoutWrong'         => 3000,
                        'enableRetry'          => true,
                        'enableSolutionsButton'=> true,
                        'passPercentage'       => 100,
                    ],
                    'l10n' => [
                        'nextButtonLabel'         => 'Siguiente pregunta',
                        'showSolutionButtonLabel'  => 'Ver solución',
                        'retryButtonLabel'         => 'Reintentar',
                        'solutionViewTitle'        => 'Vista de solución',
                        'correctText'              => '¡Correcto!',
                        'incorrectText'            => 'Incorrecto.',
                        'shouldSelect'             => 'Debería seleccionarse',
                        'shouldNotSelect'          => 'No debería seleccionarse',
                        'muteButtonLabel'          => 'Silenciar efectos',
                        'closeButtonLabel'         => 'Cerrar',
                        'slideOfTotal'             => 'Diapositiva :num de :total',
                        'score'                    => 'Obtuviste :num de :total puntos.',
                        'combinedFeedback'         => '@score/@maxScore puntos',
                        'defaultText'              => 'Enviar respuesta',
                    ],
                ],
                'subContentId' => $sub_id,
                'metadata'     => ['title' => 'Pregunta interactiva', 'license' => 'U'],
            ],
            'pause'          => true,
            'displayType'    => 'button',
            'buttonOnMobile' => false,
            'adaptivity'     => [
                'correct' => ['allowOptionalOverride' => true, 'seekTo' => 0, 'allowUserInteraction' => false],
                'wrong'   => ['allowOptionalOverride' => true, 'seekTo' => 0, 'allowUserInteraction' => false],
            ],
            'label' => '',
        ];
    }

    private static function build_summary(array $statements, int $correct_idx, int $display_at): array {
        if (empty($statements)) {
            $statements  = ['Concepto clave del video', 'Afirmación incorrecta A', 'Afirmación incorrecta B'];
            $correct_idx = 0;
        }

        // H5P.Summary: summaries[0].summary[0] is the correct statement; others are distractors.
        $summary_arr = [];
        foreach ($statements as $i => $stmt) {
            if ($i === $correct_idx) {
                array_unshift($summary_arr, (string) $stmt);
            } else {
                $summary_arr[] = (string) $stmt;
            }
        }

        return [
            'task' => [
                'library'      => 'H5P.Summary 1.10',
                'params'       => [
                    'intro'     => 'Elige el enunciado correcto.',
                    'summaries' => [
                        [
                            'subContentId' => h5p_content_builder::uuid(),
                            'summary'      => $summary_arr,
                        ],
                    ],
                    'overallFeedback' => [
                        ['from' => 0, 'to' => 100, 'feedback' => ''],
                    ],
                    'solvedLabel'       => '¡Correcto!',
                    'scoreLabel'        => 'Respuestas incorrectas:',
                    'labelCorrect'      => 'Correcto',
                    'labelIncorrect'    => 'Incorrecto',
                    'labelCorrectAnswers' => 'Respuestas correctas',
                    'tipButtonLabel'    => 'Mostrar pista',
                    'scoreBarLabel'     => 'Tu puntaje:',
                    'progressText'      => 'Progreso :num de :total',
                ],
                'subContentId' => h5p_content_builder::uuid(),
                'metadata'     => ['title' => 'Resumen del video', 'license' => 'U'],
            ],
            'displayAt' => $display_at,
        ];
    }
}
