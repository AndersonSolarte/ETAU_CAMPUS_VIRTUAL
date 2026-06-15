import type { CourseGenerationOptions, CourseLanguage, CourseLevel } from "../types/course-builder.types.js";

// ─── System prompt ────────────────────────────────────────────────────────────

export function buildSystemPrompt(language: CourseLanguage = "es"): string {
  const lang = language === "es" ? "español" : "English";

  return `Eres un experto en diseño curricular e instrucción académica universitaria.
Tu tarea es generar blueprints completos de cursos universitarios estructurados en formato JSON estricto.

REGLAS:
1. Responde SIEMPRE en JSON válido, sin texto adicional fuera del JSON.
2. Usa el idioma: ${lang}.
3. Aplica la Taxonomía de Bloom para definir objetivos y competencias.
4. Sigue estándares de diseño instruccional (ADDIE, UbD).
5. El shortname del curso debe ser único, corto (máx. 10 caracteres), sin espacios.
6. Todas las actividades deben tener instrucciones claras y criterios de evaluación.
7. La estructura JSON debe seguir EXACTAMENTE el esquema proporcionado.
8. Los quizzes deben incluir preguntas de ejemplo representativas.
9. Las rúbricas deben tener criterios medibles y descriptores claros.

CONTEXTO INSTITUCIONAL:
- Institución: TAU Campus Virtual
- Modalidad: Virtual / E-learning
- LMS: Moodle
- Enfoque pedagógico: Constructivismo, Aprendizaje Activo`;
}

// ─── User prompt ──────────────────────────────────────────────────────────────

export function buildUserPrompt(
  prompt: string,
  level: CourseLevel = "beginner",
  options: CourseGenerationOptions = {}
): string {
  const opts = {
    includeQuizzes: true,
    includeAssignments: true,
    includeForums: true,
    includeRubrics: true,
    includeCompetencies: true,
    includeOutcomes: true,
    weeklyHours: 4,
    ...options
  };

  return `Crea un blueprint detallado de curso universitario basado en esta solicitud:

"${prompt}"

CONFIGURACIÓN:
- Nivel académico: ${level}
- Horas semanales estimadas: ${opts.weeklyHours}
- Incluir quizzes: ${opts.includeQuizzes}
- Incluir tareas/assignments: ${opts.includeAssignments}
- Incluir foros: ${opts.includeForums}
- Incluir rúbricas: ${opts.includeRubrics}
- Incluir competencias: ${opts.includeCompetencies}
- Incluir resultados de aprendizaje: ${opts.includeOutcomes}

ESQUEMA JSON REQUERIDO:
{
  "title": "string - Nombre completo del curso",
  "shortname": "string - Código único máx 10 chars sin espacios",
  "summary": "string - Descripción del curso (2-3 párrafos)",
  "language": "es",
  "level": "${level}",
  "totalWeeks": "number - duración en semanas",
  "weeklyHours": ${opts.weeklyHours},
  "objectives": ["string - objetivos generales del curso"],
  "competencies": [
    {
      "id": "C1",
      "name": "string",
      "description": "string",
      "bloomLevel": "remember|understand|apply|analyze|evaluate|create"
    }
  ],
  "outcomes": [
    {
      "id": "O1",
      "statement": "string - Al finalizar el curso el estudiante podrá...",
      "competencyId": "C1",
      "assessedBy": ["activity titles"]
    }
  ],
  "modules": [
    {
      "weekNumber": 1,
      "title": "string",
      "description": "string",
      "topics": ["string"],
      "estimatedHours": ${opts.weeklyHours},
      "activities": [
        {
          "type": "quiz|assignment|forum|resource|page",
          "title": "string",
          "description": "string",
          "instructions": "string",
          "weight": "number (0-100, solo si es evaluable)",
          "rubric": ${opts.includeRubrics ? `{
            "title": "string",
            "criteria": [
              {
                "name": "string",
                "description": "string",
                "maxScore": 10,
                "levels": [
                  { "label": "Excelente", "score": 10, "description": "string" },
                  { "label": "Bueno", "score": 7, "description": "string" },
                  { "label": "Regular", "score": 5, "description": "string" },
                  { "label": "Insuficiente", "score": 2, "description": "string" }
                ]
              }
            ]
          }` : "null"},
          "quiz": ${opts.includeQuizzes ? `{
            "questionCount": 10,
            "questionTypes": ["multiple_choice", "true_false"],
            "timeLimit": 30,
            "attempts": 2,
            "passingScore": 60,
            "sampleQuestions": [
              {
                "type": "multiple_choice",
                "question": "string",
                "options": ["A", "B", "C", "D"],
                "correctAnswer": "A"
              }
            ]
          }` : "null"},
          "forum": ${opts.includeForums ? `{
            "type": "general|single|question",
            "initialPost": "string - pregunta o tema inicial",
            "graded": true
          }` : "null"}
        }
      ],
      "resources": [
        {
          "type": "page|url",
          "title": "string",
          "description": "string",
          "content": "string (si type=page, contenido HTML básico)"
        }
      ]
    }
  ],
  "assessmentStrategy": {
    "continuousAssessment": 60,
    "finalProject": 30,
    "participation": 10,
    "description": "string - descripción de la estrategia evaluativa"
  }
}

Genera el blueprint completo para TODAS las semanas del curso.`;
}

// ─── RAG-ready prompt extension (future) ─────────────────────────────────────

export function buildRagContextBlock(contextDocs: string[]): string {
  if (!contextDocs.length) return "";
  return `\n\nCONTEXTO ADICIONAL DE BASE DE CONOCIMIENTO:\n${contextDocs.map((d, i) => `[Doc ${i + 1}]: ${d}`).join("\n\n")}`;
}
