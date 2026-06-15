# AI Course Builder — Documentación Técnica

Módulo de generación automática de cursos usando IA para TAU Campus Virtual.

---

## Arquitectura

```
ai-course-builder/
├── ai-course-builder.module.ts       # Factory: instancia servicios y exporta Router
├── ai-course-builder.service.ts      # Orquestador principal (jobs, pipeline)
├── ai-course-builder.controller.ts   # Handlers Express + validación Zod
├── ai-course-builder.routes.ts       # Definición de endpoints
│
├── providers/
│   ├── ai-provider.interface.ts      # Contrato IAiProvider (intercambiable)
│   └── openai.provider.ts            # Implementación OpenAI GPT-4o
│
├── services/
│   ├── prompt-builder.service.ts     # Construye prompts dinámicos + RAG hook
│   ├── course-generator.service.ts   # Pipeline: request → prompts → AI → blueprint
│   └── moodle-course.service.ts      # Integración Moodle Web Services API
│
├── templates/
│   ├── prompt-templates.ts           # System/user prompts + RAG context block
│   └── academic-templates.ts         # Plantillas académicas reutilizables
│
└── types/
    ├── course-builder.types.ts       # DTOs y tipos del dominio
    └── moodle-ws.types.ts            # Tipos Moodle WS API
```

---

## Endpoints

| Método | Ruta                                        | Descripción                              |
|--------|---------------------------------------------|------------------------------------------|
| GET    | `/api/ai-course-builder/health`             | Estado del provider IA                   |
| GET    | `/api/ai-course-builder/templates`          | Lista plantillas académicas disponibles  |
| POST   | `/api/ai-course-builder/generate`           | Inicia generación de curso (async)       |
| GET    | `/api/ai-course-builder/jobs`               | Lista jobs recientes                     |
| GET    | `/api/ai-course-builder/jobs/:jobId`        | Estado y resultado de un job             |
| POST   | `/api/ai-course-builder/jobs/:jobId/deploy` | Despliega blueprint en Moodle            |

---

## Flujo de generación

```
Docente → POST /generate  →  Job creado (pending)
                           ↓
              AI genera blueprint (processing)
              OpenAI GPT-4o con JSON mode
                           ↓
              Blueprint guardado en DB (completed)
                           ↓
Frontend polling GET /jobs/:id cada 2.5s
                           ↓
              POST /jobs/:id/deploy → Moodle WS API
              core_course_create_courses
              core_course_update_sections
```

---

## Modelo de datos (Prisma)

```prisma
model AiCourseJob {
  id             String    # cuid
  status         String    # pending | processing | completed | failed
  prompt         String    # prompt original del docente
  requestPayload Json      # opciones de generación completas
  blueprint      Json      # CourseBlueprint generado por IA
  moodleCourseId Int       # ID del curso creado en Moodle
  tokensUsed     Int       # tokens consumidos
  modelUsed      String    # modelo OpenAI usado
  durationMs     Int       # tiempo de generación
  errorMessage   String
}

model AiCourseTemplate    # plantillas reutilizables creadas por admins
model AiUsageLog          # control de costos por tokens
```

---

## Variables de entorno requeridas

```env
# OpenAI
OPENAI_API_KEY=sk-proj-...
OPENAI_MODEL=gpt-4o          # default
OPENAI_MAX_TOKENS=4096       # default

# Moodle Web Services
MOODLE_BASE_URL=http://localhost:8080
MOODLE_WS_TOKEN=...          # Obtener en Moodle: Admin → Plugins → Web services
```

### Obtener MOODLE_WS_TOKEN
1. Moodle Admin → Plugins → Web services → Manage tokens
2. Crear token para usuario admin
3. Asignar función `core_course_create_courses` al servicio

---

## Agregar un nuevo provider IA

1. Crear `providers/anthropic.provider.ts` implementando `IAiProvider`:

```typescript
export class AnthropicProvider implements IAiProvider {
  readonly name = "anthropic";
  readonly model = "claude-opus-4-7";

  async generateCourseBlueprint(...): Promise<AiGenerationResult> {
    // implementación
  }

  async isAvailable(): Promise<boolean> { ... }
}
```

2. Reemplazar en `ai-course-builder.service.ts`:
```typescript
this.generator = new CourseGeneratorService(new AnthropicProvider());
```

---

## Integración RAG (preparada, no activa)

El `PromptBuilderService.build()` acepta un segundo parámetro `ragDocs: string[]`.
Cuando se integre una vector DB (pgvector / Pinecone):

```typescript
const ragDocs = await vectorDB.search(request.prompt, { topK: 5 });
const result = await this.generator.generate(request, ragDocs);
```

Los docs se inyectan automáticamente al prompt via `buildRagContextBlock()`.

---

## Plantillas académicas

Las plantillas en `templates/academic-templates.ts` pre-configuran:
- Número de semanas por defecto
- Estrategia de evaluación
- Ejemplos de prompts para matching automático

Para agregar una plantilla nueva, añadir un objeto al array `ACADEMIC_TEMPLATES`.

---

## Escalabilidad futura

| Necesidad                    | Punto de extensión                          |
|------------------------------|---------------------------------------------|
| Cola de jobs (Bull/BullMQ)   | Reemplazar `fire-and-forget` en `.service`  |
| Streaming de respuesta       | Usar `stream: true` en `openai.provider`    |
| Caché de blueprints          | Redis en `course-generator.service`         |
| Multi-tenant                 | Agregar `tenantId` en `AiCourseJob`         |
| Exportar a SCORM/IMS         | Nuevo service en `/services/export/`        |
| Generación de contenido HTML | Extender `CourseModule.resources`           |
