<?php
namespace local_tau_course_creator_ai;

defined('MOODLE_INTERNAL') || die();

class ai_service {

    private string $provider;
    private string $model;
    private string $api_key;
    private string $base_url;

    // Provider constants
    const PROVIDER_OPENAI = 'openai';

    public function __construct(?string $provider_override = null) {
        $this->provider = self::PROVIDER_OPENAI;
        $this->api_key  = get_config('local_tau_course_creator_ai', 'openai_api_key') ?: '';
        $this->model    = get_config('local_tau_course_creator_ai', 'openai_model') ?: 'gpt-4o';
        $this->base_url = 'https://api.openai.com/v1/chat/completions';
    }

    public function generate_text(string $prompt, string $system_instructions = ''): string {
        $messages = [];
        if ($system_instructions !== '') {
            $messages[] = ['role' => 'system', 'content' => $system_instructions];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];
        return $this->chat($messages);
    }

    public function plan(string $prompt, string $language = 'es', string $system_instructions = '', array $options = []): array {
        $lang_label = $language === 'es' ? 'Spanish' : 'English';
        $system     = $this->plan_system_prompt($lang_label, $options);
        if ($system_instructions) {
            $system .= "\n\nADDITIONAL INSTRUCTIONS:\n" . $system_instructions;
        }
        $raw = $this->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => $prompt],
        ]);
        return $this->parse_blueprint($raw);
    }

    public function stream_plan(string $prompt, string $language = 'es', string $system_instructions = '', array $options = [], ?callable $on_token = null): array {
        $lang_label = $language === 'es' ? 'Spanish' : 'English';
        $system     = $this->plan_system_prompt($lang_label, $options);
        if ($system_instructions) {
            $system .= "\n\nADDITIONAL INSTRUCTIONS:\n" . $system_instructions;
        }
        $raw = $this->stream_chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => $prompt],
        ], $on_token);
        return $this->parse_blueprint($raw);
    }

    public function stream_refine(array $blueprint, string $instruction, string $language = 'es', ?callable $on_token = null): array {
        $lang_label = $language === 'es' ? 'Spanish' : 'English';
        $raw = $this->stream_chat([
            ['role' => 'system',    'content' => $this->plan_system_prompt($lang_label)],
            ['role' => 'assistant', 'content' => json_encode($blueprint, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)],
            ['role' => 'user',      'content' => $instruction],
        ], $on_token);
        return $this->parse_blueprint($raw);
    }

    public function refine(array $blueprint, string $instruction, string $language = 'es'): array {
        $lang_label = $language === 'es' ? 'Spanish' : 'English';
        $raw = $this->chat([
            ['role' => 'system',    'content' => $this->plan_system_prompt($lang_label)],
            ['role' => 'assistant', 'content' => json_encode($blueprint, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)],
            ['role' => 'user',      'content' => $instruction],
        ]);
        return $this->parse_blueprint($raw);
    }

    // ── Provider-aware chat ───────────────────────────────────────────────────

    private function chat(array $messages): string {
        return $this->chat_openai_compat($messages, false);
    }

    private function stream_chat(array $messages, ?callable $on_token): string {
        return $this->stream_openai_compat($messages, $on_token);
    }

    // ── Claude (Anthropic) ────────────────────────────────────────────────────

    private function chat_openai_compat(array $messages, bool $stream = false): string {
        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.3,
            'max_tokens'  => 8000,
            'stream'      => $stream,
            'response_format' => ['type' => 'json_object'],
        ];

        $ch = curl_init($this->base_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => $this->openai_headers(),
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            throw new \Exception("Error de conexión con OpenAI: " . $curl_err);
        }
        $data = json_decode($response, true);
        if ($http_code >= 400) {
            $msg = $data['error']['message'] ?? "HTTP {$http_code}";
            throw new \Exception("Error OpenAI API: " . $msg);
        }
        return $data['choices'][0]['message']['content'] ?? '';
    }

    private function stream_openai_compat(array $messages, ?callable $on_token): string {
        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.3,
            'max_tokens'  => 4096,
            'stream'      => true,
            'response_format' => ['type' => 'json_object'],
        ];

        $accumulated = '';
        $line_buf    = '';

        $ch = curl_init($this->base_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => $this->openai_headers(),
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_WRITEFUNCTION  => function ($ch, $data) use (&$accumulated, &$line_buf, $on_token) {
                $line_buf .= $data;
                while (($pos = strpos($line_buf, "\n")) !== false) {
                    $line     = trim(substr($line_buf, 0, $pos));
                    $line_buf = substr($line_buf, $pos + 1);
                    if (strncmp($line, 'data: ', 6) !== 0) continue;
                    $payload = substr($line, 6);
                    if ($payload === '[DONE]') continue;
                    $chunk = json_decode($payload, true);
                    $token = $chunk['choices'][0]['delta']['content'] ?? '';
                    if ($token !== '' && $token !== null) {
                        $accumulated .= $token;
                        if ($on_token) ($on_token)($token);
                    }
                }
                return strlen($data);
            },
        ]);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            throw new \Exception("Error de conexión con OpenAI: " . $curl_err);
        }
        if ($http_code >= 400) {
            $err_msg = '';
            $decoded = json_decode($line_buf, true);
            if ($decoded) {
                $err_msg = $decoded['error']['message'] ?? ($decoded['message'] ?? '');
            }
            if (empty($err_msg)) {
                $err_msg = "HTTP " . $http_code;
            }
            throw new \Exception("Error de OpenAI ({$http_code}): " . $err_msg);
        }
        return $accumulated;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function extract_system(array $messages): array {
        $system   = '';
        $filtered = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $system = $msg['content'];
            } else {
                $filtered[] = $msg;
            }
        }
        return [$system, $filtered];
    }

    private function openai_headers(): array {
        $headers = ['Content-Type: application/json'];
        if ($this->api_key !== '') {
            $headers[] = 'Authorization: Bearer ' . $this->api_key;
        }
        return $headers;
    }

    private function parse_blueprint(string $raw): array {
        $clean = trim($raw);

        // Strip markdown code blocks
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $clean, $m)) {
            $clean = trim($m[1]);
        }

        // Find first {
        $start = strpos($clean, '{');
        if ($start !== false) {
            $clean = substr($clean, $start);
        }

        // Try direct parse first
        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['sections'])) {
            return $decoded;
        }

        // Attempt JSON repair: truncate at last balanced }
        $repaired = $this->repair_json($clean);
        if ($repaired !== null) {
            $decoded = json_decode($repaired, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['sections'])) {
                return $decoded;
            }
        }

        throw new \Exception(
            'La IA no generó un plan válido. Intenta con una descripción más corta o usa OpenAI/Claude para mejores resultados.'
        );
    }

    private function repair_json(string $json): ?string {
        // Remove trailing text after the last complete top-level }
        $depth  = 0;
        $in_str = false;
        $escape = false;
        $last_close = -1;

        for ($i = 0, $len = strlen($json); $i < $len; $i++) {
            $c = $json[$i];
            if ($escape) { $escape = false; continue; }
            if ($c === '\\' && $in_str) { $escape = true; continue; }
            if ($c === '"') { $in_str = !$in_str; continue; }
            if ($in_str) continue;
            if ($c === '{' || $c === '[') { $depth++; }
            elseif ($c === '}' || $c === ']') {
                $depth--;
                if ($depth === 0) { $last_close = $i; }
            }
        }

        if ($last_close > 0) {
            return substr($json, 0, $last_close + 1);
        }
        return null;
    }

    private function plan_system_prompt(string $lang, array $options = []): string {
        $allowed = ['page', 'resource', 'url', 'forum', 'assign', 'quiz', 'glossary', 'feedback'];
        $types_str = implode(', ', $allowed);

        return <<<PROMPT
You are an expert Moodle LMS course designer. Output ONLY valid JSON, no extra text or markdown.

Analyze the user's prompt carefully to determine:
1. The requested course name.
2. The number of modules/sections requested (if specified, e.g. "dos módulos", "5 módulos", "6 semanas", etc., create exactly that number of content modules. If not specified, dynamically determine a logical number of modules, usually between 3 and 5).
3. The specific types of activities and materials requested (e.g. "material PDF/documento/guía" maps to type "resource", "consulta bibliográfica / enlaces/páginas web" to type "url", "tareas / entregables/trabajos" to type "assign", "evaluaciones / cuestionarios / quices/exámenes" to type "quiz", "foro/discusión/debate" to type "forum", "página/lectura de introducción" to type "page"). If not specified, provide a balanced, high-quality pedagogy with a Page guide, a downloadable resource, and an assignment or quiz per module.

JSON structure:
{
  "courseName": "Course name in {$lang}",
  "courseDescription": "2-3 sentence overview in {$lang}",
  "sections": [
    {
      "title": "Section title",
      "summary": "One paragraph description of the module or topic",
      "activities": [
        {
          "type": "page", 
          "title": "Title", 
          "description": "1-2 sentence learning purpose or instructions"
        }
      ]
    }
  ]
}

ALLOWED ACTIVITY TYPES: {$types_str}

ACTIVITY RULES:
- type="quiz": Include a "questions" array with exactly 5 multiple-choice questions:
  {"question":"Question?","answers":["A","B","C","D"],"correct":0,"feedback":"Why A is correct."}
  ("correct" is the 0-based index of the correct answer).
- type="glossary": Include a "terms" array with 5 key terms:
  {"concept":"Term","definition":"Clear 1-sentence definition."}
- type="resource": Used for downloadable PDFs, guides, or files.
- type="url": Used for external reference links or bibliographic material.
- type="assign": Used for homework, assignments, or deliverables.
- type="forum": Used for discussion and reflection.
- type="page": Used for core content and overview guides.

STRUCTURE RULES:
- Section 0 (first section) MUST be named "Información del Curso" or "Introducción" and contain 1-2 introductory activities (e.g. a page for Course Welcome/Guidelines, and optionally a forum for Announcements).
- The subsequent sections are the content modules (Module 1, Module 2, etc.) structured logically based on the course topics.
- Keep all activity descriptions to 1-2 sentences maximum.
- All text, titles, descriptions, quiz questions, and glossary terms must be in {$lang}.
- Output ONLY the raw JSON object, starting with { and ending with }. Do not wrap in markdown or add explanations.
PROMPT;
    }

    // ── H5P content generation ────────────────────────────────────────────────

    /**
     * Generate structured data for a specific H5P content type using AI.
     *
     * @param  string $type      QuestionSet|CoursePresentation|InteractiveBook|BranchingScenario|InteractiveVideo
     * @param  string $topic     Topic or learning objective for the content
     * @param  int    $quantity  Number of items (questions, slides, chapters, nodes)
     * @param  string $language  'es' | 'en'
     * @param  string $video_url For InteractiveVideo type only
     * @return array  Structured data parsed from AI JSON response
     */
    public function generate_h5p(
        string $type,
        string $topic,
        int    $quantity  = 5,
        string $language  = 'es',
        string $video_url = ''
    ): array {
        $key = strtolower(preg_replace('/[.\- _]/', '', $type));

        $lang_label = $language === 'es' ? 'Spanish' : 'English';

        switch ($key) {
            case 'questionset':
            case 'h5pquestionset':
                $system = $this->h5p_question_set_prompt($lang_label, $quantity);
                break;

            case 'coursepresentation':
            case 'h5pcoursepresentation':
                $system = $this->h5p_course_presentation_prompt($lang_label, $quantity);
                break;

            case 'interactivebook':
            case 'h5pinteractivebook':
                $system = $this->h5p_interactive_book_prompt($lang_label, $quantity);
                break;

            case 'branchingscenario':
            case 'h5pbranchingscenario':
                $system = $this->h5p_branching_scenario_prompt($lang_label, $quantity);
                break;

            case 'interactivevideo':
            case 'h5pinteractivevideo':
                $system = $this->h5p_interactive_video_prompt($lang_label, $quantity, $video_url);
                break;

            default:
                throw new \InvalidArgumentException("Unknown H5P type for AI generation: {$type}");
        }

        $user_msg = "Topic: {$topic}";

        $raw = $this->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => $user_msg],
        ]);

        return $this->parse_json_generic($raw);
    }

    /**
     * Generate slide content for an interactive HTML presentation.
     *
     * @param  string $topic    Section title / topic to cover
     * @param  int    $slides   Number of content slides (excluding cover + closing)
     * @param  string $material Source material text; empty = use topic only
     * @param  string $language 'es' | 'en'
     * @return array  {title, slides:[{heading, kicker, icon, content, points[]}]}
     */
    public function generate_presentation_content(
        string $topic,
        int    $slides   = 7,
        string $material = '',
        string $language = 'es'
    ): array {
        $lang_label = $language === 'es' ? 'Spanish' : 'English';

        $material_block = '';
        if (!empty(trim($material))) {
            $short = mb_strlen($material) > 3500 ? mb_substr($material, 0, 3500) . '…' : $material;
            $material_block = "\n\nSOURCE MATERIAL (base all slide content on this):\n---\n{$short}\n---";
        }

        $system = <<<PROMPT
You are a subject-matter expert creating a real educational slide deck to TEACH a specific topic.
Your job is to explain, define, and illustrate the actual subject matter — as if you are the professor standing in front of a class.{$material_block}

CRITICAL: Every slide must contain REAL content about the topic — definitions, concepts, facts, formulas, examples, real-world use cases, methodologies.
NEVER write meta-content like "In this slide we will see...", "This presentation covers...", or anything describing the presentation format.
Write in {$lang_label}. Output ONLY a valid JSON object. No markdown, no code fences, no extra text.

JSON structure:
{
  "title": "Clear topic title in {$lang_label}",
  "slides": [
    {
      "heading": "Specific aspect of the topic (max 8 words)",
      "kicker": "Subject area label (1-3 words, e.g. Definición, Arquitectura, Casos de Uso, Tecnologías)",
      "icon": "📊",
      "content": "2-4 sentences that directly EXPLAIN this aspect. Include real facts, definitions or examples. No filler.",
      "points": ["Specific, concrete key point", "Real fact or concept", "Practical example or application"]
    }
  ]
}

RULES:
- Generate exactly {$slides} slides, each on a DISTINCT sub-topic or facet of the main topic.
- Slide 1: define what this topic is and why it matters (with concrete numbers/facts if available).
- Slides 2-{$slides}: cover technologies, architectures, tools, use cases, processes, or key concepts specific to this topic.
- Last slide: actionable takeaways and what to do next.
- icon: one relevant emoji that matches the slide sub-topic.
- points: 3-5 concrete bullet points per slide (not vague generalities).
- All text in {$lang_label}.
- Output ONLY the JSON object starting with { and ending with }.
PROMPT;

        $user_msg = "Topic: {$topic}";

        $raw = $this->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => $user_msg],
        ]);

        $result = $this->parse_json_generic($raw);

        if (empty($result['slides']) || !is_array($result['slides'])) {
            throw new \Exception('La IA no generó diapositivas válidas para la presentación.');
        }

        return $result;
    }

    private function parse_json_generic(string $raw): array {
        $clean = trim($raw);

        // Strip markdown code fences.
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/i', $clean, $m)) {
            $clean = trim($m[1]);
        }

        // Find the first '{'.
        $start = strpos($clean, '{');
        if ($start !== false) {
            $clean = substr($clean, $start);
        }

        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Try to repair truncated JSON.
        $repaired = $this->repair_json($clean);
        if ($repaired !== null) {
            $decoded = json_decode($repaired, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        throw new \Exception(
            'La IA no generó un JSON H5P válido. Usa Claude o OpenAI para contenido H5P complejo.'
        );
    }

    // ── H5P system prompts ───────────────────────────────────────────────────

    private function h5p_question_set_prompt(string $lang, int $qty): string {
        return <<<PROMPT
You are an expert educational content creator. Generate an H5P QuestionSet quiz.
Output ONLY a valid JSON object. No extra text, no markdown, no code fences.

Required JSON structure:
{
  "title": "Quiz title in {$lang}",
  "introduction": "<p>Brief instructions in {$lang}</p>",
  "passPercentage": 60,
  "questions": [
    {
      "question": "<p>Question text?</p>",
      "answers": ["Correct answer", "Wrong option B", "Wrong option C", "Wrong option D"],
      "correct": 0,
      "feedback": "One sentence explaining why the first answer is correct."
    }
  ]
}

RULES:
- Generate exactly {$qty} questions.
- answers[0] is ALWAYS the correct answer (correct must always be 0).
- All text must be in {$lang}.
- Questions must be specific and educational, not generic.
- Output ONLY the JSON object starting with {{ and ending with }}.
PROMPT;
    }

    private function h5p_course_presentation_prompt(string $lang, int $qty): string {
        return <<<PROMPT
You are an expert educational content creator. Generate an H5P CoursePresentation (slide deck).
Output ONLY a valid JSON object. No extra text, no markdown, no code fences.

Required JSON structure:
{
  "title": "Presentation title in {$lang}",
  "slides": [
    {
      "title": "Slide title",
      "content": "<h2>Slide Heading</h2><p>Main content in HTML. Use ul/li for key points.</p>",
      "keywords": ["keyword1", "keyword2"]
    }
  ]
}

RULES:
- Generate exactly {$qty} slides.
- First slide: title + course overview.
- Last slide: summary + conclusions.
- Each slide content must be meaningful HTML (h2, p, ul/li).
- keywords appear in the navigation sidebar.
- All text must be in {$lang}.
- Output ONLY the JSON object starting with {{ and ending with }}.
PROMPT;
    }

    private function h5p_interactive_book_prompt(string $lang, int $qty): string {
        return <<<PROMPT
You are an expert educational content creator. Generate an H5P InteractiveBook (digital book with chapters).
Output ONLY a valid JSON object. No extra text, no markdown, no code fences.

Required JSON structure:
{
  "title": "Book title in {$lang}",
  "coverTitle": "Title shown on cover",
  "coverSubtitle": "Subtitle",
  "coverDescription": "One sentence describing the book.",
  "chapters": [
    {
      "title": "Chapter 1: Title",
      "textBlocks": [
        {"heading": "Section heading", "body": "<p>Educational content in HTML with p, ul, li tags.</p>"}
      ],
      "questions": [
        {"question": "<p>Self-check question?</p>", "answers": ["Correct","Wrong A","Wrong B","Wrong C"], "correct": 0, "feedback": "Explanation."}
      ]
    }
  ]
}

RULES:
- Generate exactly {$qty} chapters.
- Each chapter: 1-2 textBlocks, exactly 1 question.
- answers[0] is always correct (correct is always 0).
- textBlocks.body must use HTML tags (p, ul, li, strong).
- All text in {$lang}.
- Output ONLY the JSON object starting with {{ and ending with }}.
PROMPT;
    }

    private function h5p_branching_scenario_prompt(string $lang, int $qty): string {
        return <<<PROMPT
You are an expert educational content creator. Generate an H5P BranchingScenario (decision-tree learning).
Output ONLY a valid JSON object. No extra text, no markdown, no code fences.

Required JSON structure:
{
  "title": "Scenario title in {$lang}",
  "startTitle": "Scenario name",
  "startSubtitle": "Short subtitle",
  "startBody": "<p>Scenario context and introduction in {$lang}.</p>",
  "nodes": [
    {
      "id": 0,
      "question": "<p>What would you do in this situation?</p>",
      "options": [
        {"text": "Good choice A", "consequence": "Explanation of why this is good.", "nextNode": 1, "isPositive": true},
        {"text": "Poor choice B", "consequence": "Explanation of the consequence.", "nextNode": -1, "isPositive": false}
      ]
    }
  ],
  "goodEndingTitle": "¡Excelente decisión!",
  "goodEndingBody": "<p>Congratulations message in {$lang}.</p>",
  "badEndingTitle": "Reflexiona sobre esto",
  "badEndingBody": "<p>Learning opportunity message in {$lang}.</p>"
}

RULES:
- Generate exactly {$qty} decision nodes.
- Node ids are 0-based integers.
- nextNode: integer index of next node, or -1 to end scenario.
- The LAST node's options must all have nextNode: -1.
- isPositive:true paths should lead forward; isPositive:false paths end with -1.
- All text in {$lang}.
- Output ONLY the JSON starting with {{ and ending with }}.
PROMPT;
    }

    private function h5p_interactive_video_prompt(string $lang, int $qty, string $video_url): string {
        $url_hint = $video_url ?: 'https://www.youtube.com/watch?v=REPLACE_WITH_VIDEO_ID';
        return <<<PROMPT
You are an expert educational content creator. Generate an H5P InteractiveVideo (video with quiz overlays).
Output ONLY a valid JSON object. No extra text, no markdown, no code fences.

Required JSON structure:
{
  "title": "Video title in {$lang}",
  "videoUrl": "{$url_hint}",
  "interactions": [
    {
      "secondsIn": 30,
      "question": "<p>Question about the video content at this point?</p>",
      "answers": ["Correct answer", "Wrong B", "Wrong C"],
      "correct": 0,
      "feedback": "Explanation of why the first answer is correct."
    }
  ],
  "summaryStatements": ["Correct key statement", "Incorrect statement A", "Incorrect statement B"],
  "correctSummaryIndex": 0
}

RULES:
- Generate exactly {$qty} interactions spaced throughout the video (different secondsIn values).
- answers[0] is always correct (correct is always 0).
- secondsIn must be realistic (e.g. 30, 90, 150, 210...).
- summaryStatements: first item is the correct one (correctSummaryIndex: 0).
- All text in {$lang}.
- Output ONLY the JSON starting with {{ and ending with }}.
PROMPT;
    }



    /**
     * Generate Moodle activities for a specific section using AI.
     *
     * @param string $prompt Description of what to generate.
     * @param string $language 'es' | 'en'
     * @return array Array of activities.
     */
    public function generate_activities_for_section(string $prompt, string $language = 'es'): array {
        $lang_label = $language === 'es' ? 'Spanish' : 'English';
        
        $system = "You are an expert Moodle LMS instructional designer. Output ONLY valid JSON representing a list of learning activities, no extra text, markdown fences or explanations.
JSON structure:
{
  \"activities\": [
    {
      \"type\": \"page\",
      \"title\": \"Title in {$lang_label}\",
      \"description\": \"Short description\",
      \"content\": \"Educational content in HTML. Use h4, p, ul, li tags. Keep it detailed and helpful.\"
    },
    {
      \"type\": \"quiz\",
      \"title\": \"Quiz Title in {$lang_label}\",
      \"description\": \"Quiz description\",
      \"questions\": [
        {
          \"question\": \"Question text?\",
          \"answers\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
          \"correct\": 0,
          \"feedback\": \"Feedback explaining why Option A is correct\"
        }
      ]
    },
    {
      \"type\": \"assign\",
      \"title\": \"Assignment Title in {$lang_label}\",
      \"description\": \"Assignment instruction in HTML\"
    }
  ]
}

RULES:
- Support page, quiz, and assign types.
- For type='quiz', generate exactly 3 relevant multiple-choice questions.
- Output ONLY the raw JSON object starting with { and ending with }.";

        $user_msg = "Topic / Instructions: {$prompt}";

        $raw = $this->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user',   'content' => $user_msg],
        ]);

        $result = $this->parse_json_generic($raw);
        if (empty($result['activities']) || !is_array($result['activities'])) {
            throw new \Exception('La IA no generó una lista de actividades válida.');
        }

        return $result['activities'];
    }
}
