<?php
namespace local_tau_course_creator_ai\h5p\types;

defined('MOODLE_INTERNAL') || die();

use local_tau_course_creator_ai\h5p\h5p_content_builder;

/**
 * Generates H5P.CoursePresentation content (slide-based presentations).
 */
class course_presentation {

    const MACHINE_NAME  = 'H5P.CoursePresentation';
    const MAJOR_VERSION = 1;
    const MINOR_VERSION = 25;

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
                'H5P.AdvancedText', 'H5P.JoubelUI', 'FontAwesome', 'H5P.CoursePresentation',
            ]),
        ];
    }

    public static function build_content_json(array $data): array {
        $slides = [];
        foreach (($data['slides'] ?? []) as $slide) {
            $slides[] = self::build_slide($slide);
        }

        // Ensure at least one slide
        if (empty($slides)) {
            $slides[] = self::build_slide(['title' => $data['title'] ?? 'Introducción', 'content' => '<p>Contenido</p>']);
        }

        return [
            'presentation' => [
                'slides'                  => $slides,
                'globalBackgroundSelector'=> [],
                'keywordListEnabled'      => true,
                'keywordListAlwaysShow'   => false,
                'keywordListAutoHide'     => false,
                'keywordListOpacity'      => 90,
            ],
            'l10n' => [
                'slide'        => 'Diapositiva',
                'score'        => 'Puntuación',
                'titleSlide'   => 'Diapositiva de título',
                'progressbar'  => 'Barra de progreso',
                'prev'         => 'Anterior',
                'next'         => 'Siguiente',
                'currentSlide' => 'Diapositiva actual',
                'lastSlide'    => 'Última diapositiva',
                'solutionMask' => 'Máscara de solución',
                'retry'        => 'Reintentar',
                'showSolution' => 'Ver solución',
                'title'        => 'Título',
                'fullscreen'   => 'Pantalla completa',
                'exitFullscreen' => 'Salir de pantalla completa',
                'slideCount'   => 'Diapositiva @index de @total',
                'accessibilitySlideNavigationExplanation' => 'Usa las teclas de flecha izquierda y derecha para cambiar entre diapositivas.',
            ],
        ];
    }

    private static function build_slide(array $slide): array {
        $keywords = [];
        foreach (($slide['keywords'] ?? []) as $kw) {
            $keywords[] = ['main' => (string) $kw];
        }
        if (empty($keywords) && !empty($slide['title'])) {
            $keywords[] = ['main' => mb_substr(strip_tags((string) $slide['title']), 0, 40)];
        }

        $content_html = $slide['content'] ?? '';
        if (!empty($slide['title'])) {
            // Wrap with h2 if the content doesn't already have a heading
            if (!preg_match('/<h[1-6]/i', $content_html)) {
                $content_html = '<h2>' . htmlspecialchars((string) $slide['title'], ENT_QUOTES) . '</h2>' . $content_html;
            }
        }

        return [
            'elements' => [
                [
                    'x'      => 3,
                    'y'      => 3,
                    'width'  => 94,
                    'height' => 90,
                    'action' => [
                        'library'      => 'H5P.AdvancedText 1.1',
                        'params'       => ['text' => $content_html],
                        'subContentId' => h5p_content_builder::uuid(),
                        'metadata'     => [
                            'title'   => mb_substr(strip_tags($slide['title'] ?? 'Diapositiva'), 0, 60),
                            'license' => 'U',
                        ],
                    ],
                    'alwaysDisplayComments' => false,
                    'backgroundOpacity'     => 0,
                ],
            ],
            'slideBackgroundSelector' => [],
            'keywords'                => $keywords,
        ];
    }
}
