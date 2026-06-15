import OpenAI from "openai";

import { env } from "../../config/env.js";
import type {
  AnalyzeRankingRequest,
  AnalyzeRankingResponse,
  CertificateDescriptionRequest,
  CertificateDescriptionResponse,
  EvaluateRulesRequest,
  EvaluateRulesResponse,
  GenerateDebateRequest,
  GenerateDebateResponse,
  GradeAssignmentRequest,
  GradeAssignmentResponse,
  ModeratePostRequest,
  ModeratePostResponse,
  RecommendCoursesRequest,
  RecommendCoursesResponse,
  StudentDataInput,
  StudentProfileResponse,
  TutorChatRequest,
  TutorChatResponse
} from "./types/ai-tools.types.js";

export class AiToolsService {
  private readonly openai: OpenAI;
  private readonly model: string;

  constructor() {
    this.openai = new OpenAI({ apiKey: env.OPENAI_API_KEY });
    this.model = env.OPENAI_MODEL;
  }

  // ─── Assign AI ───────────────────────────────────────────────────────────────

  async gradeAssignment(req: GradeAssignmentRequest): Promise<GradeAssignmentResponse> {
    const maxGrade = req.maxGrade ?? 100;
    const rubricSection = req.rubric
      ? `\nRúbrica de evaluación:\n${req.rubric}`
      : "";

    const submissionsText = req.submissions
      .map(
        (s) =>
          `--- Entrega de ${s.studentName} (ID:${s.submissionId}) ---\n${s.text || "(sin texto)"}`
      )
      .join("\n\n");

    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.3,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un asistente de calificación educativa. Evalúa cada entrega de forma justa y constructiva.
Responde siempre en JSON con este formato exacto:
{
  "suggestions": [
    {
      "submissionId": number,
      "userId": number,
      "studentName": string,
      "assignmentId": number,
      "grade": number (0-${maxGrade}),
      "maxGrade": ${maxGrade},
      "feedback": string (retroalimentación en español, 2-3 oraciones constructivas),
      "confidence": number (0-1)
    }
  ]
}${rubricSection}`
        },
        {
          role: "user",
          content: `Califica las siguientes entregas para la tarea ID ${req.assignmentId}. Nota máxima: ${maxGrade}.\n\n${submissionsText}`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw) as { suggestions: GradeAssignmentResponse["suggestions"] };

    return {
      suggestions: parsed.suggestions ?? [],
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Tutor AI ─────────────────────────────────────────────────────────────────

  async tutorChat(req: TutorChatRequest): Promise<TutorChatResponse> {
    const history = (req.history ?? []).slice(-10);

    const messages: OpenAI.Chat.ChatCompletionMessageParam[] = [
      {
        role: "system",
        content: `Eres el Tutor AI del curso "${req.courseName}" en TAU Campus Virtual.
Eres un asistente educativo experto, amigable y conciso.
Responde siempre en el mismo idioma que el estudiante.
Limita tus respuestas a 3-4 oraciones a menos que se pida más detalle.
Contextualiza tus respuestas al contenido del curso cuando sea posible.`
      },
      ...history.map((m) => ({
        role: m.role as "user" | "assistant",
        content: m.content
      })),
      { role: "user", content: req.message }
    ];

    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.7,
      max_tokens: 512,
      messages
    });

    return {
      reply: response.choices[0]?.message?.content ?? "Lo siento, no pude generar una respuesta.",
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Forum AI — Moderation ────────────────────────────────────────────────────

  async moderateForumPost(req: ModeratePostRequest): Promise<ModeratePostResponse> {
    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.2,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un moderador de foros académicos. Analiza publicaciones y determina si son apropiadas.
Responde en JSON con este formato exacto:
{
  "verdict": "approved" | "flagged" | "rejected",
  "reason": string,
  "suggestedReply": string (opcional — respuesta pedagógica sugerida si aplica)
}
Criterios:
- approved: contenido constructivo, relevante y respetuoso
- flagged: contenido ambiguo, fuera de tema, o que requiere revisión humana
- rejected: contenido ofensivo, spam, o completamente inapropiado`
        },
        {
          role: "user",
          content: `Curso ID: ${req.courseId}\nAsunto: ${req.subject}\nContenido:\n${req.text}`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw);

    return {
      verdict: parsed.verdict ?? "approved",
      reason: parsed.reason ?? "",
      suggestedReply: parsed.suggestedReply,
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Forum AI — Generate Debate ────────────────────────────────────────────

  async generateForumDebate(req: GenerateDebateRequest): Promise<GenerateDebateResponse> {
    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.8,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un diseñador instruccional experto. Crea debates académicos estimulantes.
Responde en JSON con este formato exacto:
{
  "discussion": {
    "title": string (título del debate, máx 100 chars),
    "body": string (presentación del tema en HTML básico, 150-250 palabras, incluye preguntas guía),
    "suggestedReplies": string[] (3 perspectivas iniciales para dinamizar el debate)
  }
}`
        },
        {
          role: "user",
          content: `Genera un debate académico sobre: "${req.topic}"\nCurso ID: ${req.courseId}`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw);

    return {
      discussion: parsed.discussion ?? { title: req.topic, body: "", suggestedReplies: [] },
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Student Life Story ───────────────────────────────────────────────────────

  async analyzeStudentProfile(data: StudentDataInput): Promise<StudentProfileResponse> {
    const lastAccessStr = data.lastAccess
      ? new Date(data.lastAccess * 1000).toLocaleDateString("es-CO")
      : "nunca";

    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.4,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un analista de datos educativos. Analiza el perfil académico de un estudiante y genera insights accionables.
Responde en JSON con este formato exacto:
{
  "summary": string (resumen del perfil en 2-3 oraciones),
  "strengths": string[] (3-4 fortalezas identificadas),
  "areasImprovement": string[] (3-4 áreas de mejora),
  "dropoutRisk": "low" | "medium" | "high",
  "engagementScore": number (0-100),
  "recommendations": string[] (4-5 recomendaciones accionables para docentes)
}`
        },
        {
          role: "user",
          content: `Analiza el siguiente perfil estudiantil:
Nombre: ${data.fullName}
Cursos matriculados: ${data.coursesEnrolled}
Actividades completadas: ${data.activitiesCompleted}
Promedio general: ${data.avgGrade !== null ? `${data.avgGrade}/100` : "sin datos"}
Participación en foros: ${data.forumPosts} publicaciones
Último acceso: ${lastAccessStr}`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw);

    return {
      summary: parsed.summary ?? "",
      strengths: parsed.strengths ?? [],
      areasImprovement: parsed.areasImprovement ?? [],
      dropoutRisk: parsed.dropoutRisk ?? "low",
      engagementScore: parsed.engagementScore ?? 50,
      recommendations: parsed.recommendations ?? [],
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Smart Rules AI ───────────────────────────────────────────────────────────

  async evaluateRules(req: EvaluateRulesRequest): Promise<EvaluateRulesResponse> {
    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.1,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un motor de reglas educativas. Determina qué estudiantes cumplen la condición especificada.
Responde en JSON con este formato exacto:
{
  "triggered": [
    { "userId": number, "reason": string }
  ]
}
Solo incluye estudiantes que REALMENTE deben ser notificados según la regla.`
        },
        {
          role: "user",
          content: `Regla ID: ${req.ruleId}
Disparador: ${req.trigger}
Condición: ${req.condition}
Estudiantes en el curso (${req.students.length}): ${JSON.stringify(req.students.slice(0, 50))}

¿Qué estudiantes deberían activar esta regla? (Si no tienes datos suficientes para determinar, devuelve lista vacía.)`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw);

    return {
      triggered: parsed.triggered ?? [],
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Ranking Activities AI ────────────────────────────────────────────────────

  async analyzeActivityRanking(req: AnalyzeRankingRequest): Promise<AnalyzeRankingResponse> {
    const activitiesText = req.activities
      .map(
        (a) =>
          `- ${a.name} (${a.type}): ${a.completions} completados de ${a.totalAttempts} intentos`
      )
      .join("\n");

    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.3,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un analista de efectividad educativa. Evalúa actividades de un curso Moodle.
Responde en JSON con este formato exacto:
{
  "ranking": [
    {
      "cmid": number,
      "name": string,
      "type": string,
      "effectivenessScore": number (0-100),
      "completionRate": number (0-100, porcentaje),
      "insight": string (diagnóstico breve en 1 oración),
      "recommendation": string (acción concreta para mejorar o mantener)
    }
  ]
}
Ordena de mayor a menor efectividad.`
        },
        {
          role: "user",
          content: `Curso ID: ${req.courseId}\nActividades:\n${activitiesText}`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw);

    return {
      ranking: parsed.ranking ?? [],
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Share Certificate AI ──────────────────────────────────────────────────────

  async generateCertificateDescription(
    req: CertificateDescriptionRequest
  ): Promise<CertificateDescriptionResponse> {
    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.7,
      max_tokens: 300,
      messages: [
        {
          role: "system",
          content: `Eres un experto en branding profesional y LinkedIn. Genera descripciones atractivas para compartir certificados.
La descripción debe:
- Tener entre 150-250 caracteres
- Ser entusiasta pero profesional
- Destacar el valor del logro
- Estar en primera persona
- Incluir uno o dos emojis relevantes al inicio`
        },
        {
          role: "user",
          content: `Genera una descripción para compartir en LinkedIn:
Certificado: "${req.certName}"
Emitido por: "${req.orgName}"`
        }
      ]
    });

    return {
      description: response.choices[0]?.message?.content ?? "",
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }

  // ─── Recommended Courses ──────────────────────────────────────────────────────

  async recommendCourses(req: RecommendCoursesRequest): Promise<RecommendCoursesResponse> {
    if (!req.availableCourses.length) {
      return { recommendations: [], tokensUsed: 0 };
    }

    const availableText = req.availableCourses
      .slice(0, 20)
      .map((c) => `ID:${c.id} | ${c.name} — ${c.summary.slice(0, 80)}`)
      .join("\n");

    const response = await this.openai.chat.completions.create({
      model: this.model,
      temperature: 0.5,
      response_format: { type: "json_object" },
      messages: [
        {
          role: "system",
          content: `Eres un sistema de recomendación educativa. Sugiere cursos relevantes basados en el perfil del estudiante.
Responde en JSON con este formato exacto:
{
  "recommendations": [
    {
      "courseId": number,
      "courseName": string,
      "reason": string (por qué se recomienda, 1 oración),
      "matchScore": number (0-100)
    }
  ]
}
Devuelve máximo 4 recomendaciones ordenadas por matchScore descendente.`
        },
        {
          role: "user",
          content: `Estudiante ID: ${req.userId}
Cursos actuales (IDs): ${req.enrolledCourseIds.join(", ") || "ninguno"}

Cursos disponibles para recomendar:
${availableText}`
        }
      ]
    });

    const raw = response.choices[0]?.message?.content ?? "{}";
    const parsed = JSON.parse(raw);

    return {
      recommendations: parsed.recommendations ?? [],
      tokensUsed: response.usage?.total_tokens ?? 0
    };
  }
}
