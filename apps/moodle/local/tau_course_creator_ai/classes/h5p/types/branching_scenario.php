<?php
namespace local_tau_course_creator_ai\h5p\types;

defined('MOODLE_INTERNAL') || die();

use local_tau_course_creator_ai\h5p\h5p_content_builder;

/**
 * Generates H5P.BranchingScenario content (decision-tree learning paths).
 *
 * AI data schema:
 * {
 *   "title": "...",
 *   "startTitle": "...",
 *   "startSubtitle": "...",
 *   "startBody": "<p>...</p>",
 *   "nodes": [
 *     { "id": 0, "question": "<p>...</p>",
 *       "options": [
 *         {"text": "...", "consequence": "...", "nextNode": 1, "isPositive": true},
 *         {"text": "...", "consequence": "...", "nextNode": -1, "isPositive": false}
 *       ]
 *     }
 *   ],
 *   "goodEndingTitle": "...", "goodEndingBody": "<p>...</p>",
 *   "badEndingTitle": "...",  "badEndingBody":  "<p>...</p>"
 * }
 * nextNode: 0-based index into nodes[], or -1 for ending screen.
 */
class branching_scenario {

    const MACHINE_NAME  = 'H5P.BranchingScenario';
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
                'H5P.BranchingQuestion', 'H5P.AdvancedText', 'FontAwesome', 'H5P.BranchingScenario',
            ]),
        ];
    }

    public static function build_content_json(array $data): array {
        $content  = [];
        $nodes    = $data['nodes'] ?? [];

        foreach ($nodes as $node) {
            $content[] = self::build_branching_question($node);
        }

        // Default end screens
        $end_screens = [
            [
                'endScreenTitle'    => $data['goodEndingTitle'] ?? '¡Excelente trabajo!',
                'endScreenSubtitle' => $data['goodEndingBody']  ?? '<p>Has completado el escenario exitosamente.</p>',
                'contentId'         => -1,
                'endScreenScore'    => 0,
            ],
            [
                'endScreenTitle'    => $data['badEndingTitle'] ?? 'Reflexiona sobre esta decisión',
                'endScreenSubtitle' => $data['badEndingBody']  ?? '<p>Revisa el escenario y vuelve a intentarlo.</p>',
                'contentId'         => -2,
                'endScreenScore'    => 0,
            ],
        ];

        return [
            'title'       => $data['title'] ?? 'Escenario',
            'startScreen' => [
                'startScreenTitle'      => $data['startTitle']    ?? ($data['title'] ?? 'Escenario de aprendizaje'),
                'startScreenSubtitle'   => $data['startSubtitle'] ?? '',
                'startScreenButtonText' => 'Comenzar',
                'startScreenAltText'    => '',
            ],
            'endScreens' => $end_screens,
            'content'    => $content,
            'scoringOptionGroup' => ['scoringOption' => 'no-score'],
            'behaviour'  => [
                'enableBackwardsNavigation'    => false,
                'forceContentFinished'         => false,
                'randomizeBranchingQuestions'  => false,
            ],
            'l10n' => [
                'startScreenButtonText' => 'Comenzar',
                'endScreenButtonText'   => 'Reiniciar',
                'proceedButtonText'     => 'Continuar',
                'scoreText'             => 'Tu puntuación:',
                'fullscreen'            => 'Pantalla completa',
                'exitFullscreen'        => 'Salir de pantalla completa',
                'scoreString'           => 'Obtuviste @score de @maxScore puntos',
                'retry'                 => 'Reintentar',
                'ok'                    => 'Aceptar',
                'close'                 => 'Cerrar',
                'title'                 => 'Título',
                'replayButtonLabel'     => 'Ver escena de nuevo',
                'endScreensButtonText'  => 'Ir al final',
            ],
        ];
    }

    private static function build_branching_question(array $node): array {
        $alternatives = [];
        foreach (($node['options'] ?? []) as $opt) {
            $next = (int) ($opt['nextNode'] ?? -1);
            // Map: positive options go to nextNode, negative options go to bad ending (-1 maps to contentId -2)
            $next_content_id = $opt['isPositive'] ? $next : -1;

            $alternatives[] = [
                'text'          => htmlspecialchars((string) ($opt['text'] ?? 'Opción'), ENT_QUOTES),
                'nextContentId' => $next_content_id,
                'feedback'      => [
                    'title'    => $opt['isPositive'] ? '¡Bien!' : 'Piénsalo mejor',
                    'subtitle' => $opt['consequence'] ?? '',
                ],
            ];
        }

        return [
            'type' => [
                'library' => 'H5P.BranchingQuestion 1.0',
                'params'  => [
                    'branchingQuestion' => [
                        'question'     => $node['question'] ?? '<p>¿Qué harías?</p>',
                        'alternatives' => $alternatives,
                    ],
                ],
                'subContentId' => h5p_content_builder::uuid(),
                'metadata'     => [
                    'title'   => 'Punto de decisión ' . (int) ($node['id'] ?? 0),
                    'license' => 'U',
                ],
            ],
            'showContentTitle'    => true,
            'forceContentFinished'=> false,
            'contentTitle'        => 'Punto de decisión ' . (int) ($node['id'] ?? 0),
            'contentDescription'  => '',
            'nextContentId'       => -1,
        ];
    }
}
