<?php
namespace local_tau_course_creator_ai\h5p;

defined('MOODLE_INTERNAL') || die();

use local_tau_course_creator_ai\ai_service;
use local_tau_course_creator_ai\h5p\types\branching_scenario;
use local_tau_course_creator_ai\h5p\types\course_presentation;
use local_tau_course_creator_ai\h5p\types\interactive_book;
use local_tau_course_creator_ai\h5p\types\interactive_video;
use local_tau_course_creator_ai\h5p\types\question_set;

/**
 * Main orchestrator for AI-driven H5P content generation.
 *
 * Usage from course_builder:
 *   $gen    = new h5p_generator($on_progress_callback);
 *   $cb_id  = $gen->generate_from_spec($activity_array, $course_context);
 *   // $cb_id is contentbank_content.id — pass as $a['contentbank_id']
 *
 * Blueprint activity spec keys consumed:
 *   h5p_type     string  QuestionSet | CoursePresentation | InteractiveBook |
 *                        BranchingScenario | InteractiveVideo
 *   ai_generate  bool    Must be true to trigger generation
 *   ai_topic     string  Subject or topic description for the AI prompt
 *   ai_quantity  int     Number of questions / slides / chapters (default: 5)
 *   ai_language  string  'es' | 'en' (default: 'es')
 *   ai_video_url string  Required for InteractiveVideo type
 *   title        string  Fallback name when ai_topic is absent
 */
class h5p_generator {

    private ai_service    $ai;
    private h5p_publisher $publisher;
    /** @var callable|null */
    private $on_progress;

    public function __construct(?callable $on_progress = null, ?string $provider_override = null) {
        $this->ai          = new ai_service($provider_override);
        $this->publisher   = new h5p_publisher();
        $this->on_progress = $on_progress;
    }

    /**
     * Generate an H5P item from a blueprint activity spec and publish it
     * to the Moodle Content Bank.
     *
     * @param  array    $spec     Activity array from the blueprint JSON.
     * @param  \context $context  Moodle context (course context recommended).
     * @return int      contentbank_content.id, or 0 on failure.
     */
    public function generate_from_spec(array $spec, \context $context): int {
        $type     = $spec['h5p_type']    ?? 'QuestionSet';
        $topic    = $spec['ai_topic']    ?? ($spec['title'] ?? 'Contenido educativo');
        $quantity = (int) ($spec['ai_quantity'] ?? 5);
        $language = $spec['ai_language'] ?? 'es';
        $title    = $spec['title']        ?? $topic;
        $video    = $spec['ai_video_url'] ?? '';

        $this->progress("Generando H5P {$type}: {$title}");

        // 1. AI generates structured content data.
        $data = $this->ai->generate_h5p($type, $topic, $quantity, $language, $video);
        if (empty($data)) {
            throw new \RuntimeException("La IA no devolvió datos para H5P {$type}.");
        }
        $data['title'] = $data['title'] ?? $title;

        // 2. Build H5P JSON structures (metadata + content).
        [$h5p_meta, $content_json] = $this->resolve_type($type, $data, $language);

        // 3. Package as .h5p ZIP.
        $h5p_path = h5p_content_builder::build($h5p_meta, $content_json);

        try {
            // 4. Publish to Content Bank.
            $cb_id = $this->publisher->publish($h5p_path, $title, $context);
            $this->progress("H5P publicado en el Banco de Contenido (id={$cb_id})");
            return $cb_id;
        } finally {
            if (file_exists($h5p_path)) {
                @unlink($h5p_path);
            }
        }
    }

    /**
     * Build H5P metadata + content JSON for the given type.
     *
     * @return array [h5p_meta, content_json]
     */
    private function resolve_type(string $type, array $data, string $language): array {
        // Normalise: strip dots, spaces, dashes for matching.
        $key = strtolower(preg_replace('/[.\- _]/', '', $type));

        switch ($key) {
            case 'questionset':
            case 'h5pquestionset':
                return [
                    question_set::build_h5p_json($data['title'], $language),
                    question_set::build_content_json($data),
                ];

            case 'coursepresentation':
            case 'h5pcoursepresentation':
                return [
                    course_presentation::build_h5p_json($data['title'], $language),
                    course_presentation::build_content_json($data),
                ];

            case 'interactivebook':
            case 'h5pinteractivebook':
                return [
                    interactive_book::build_h5p_json($data['title'], $language),
                    interactive_book::build_content_json($data),
                ];

            case 'branchingscenario':
            case 'h5pbranchingscenario':
                return [
                    branching_scenario::build_h5p_json($data['title'], $language),
                    branching_scenario::build_content_json($data),
                ];

            case 'interactivevideo':
            case 'h5pinteractivevideo':
                return [
                    interactive_video::build_h5p_json($data['title'], $language),
                    interactive_video::build_content_json($data),
                ];

            default:
                throw new \InvalidArgumentException("H5P type desconocido: {$type}");
        }
    }

    private function progress(string $msg): void {
        if ($this->on_progress) {
            ($this->on_progress)($msg);
        }
    }
}
