import type { GenerateCourseRequest } from "../types/course-builder.types.js";
import { matchTemplate } from "../templates/academic-templates.js";
import {
  buildRagContextBlock,
  buildSystemPrompt,
  buildUserPrompt
} from "../templates/prompt-templates.js";

// Builds the final prompts sent to the AI provider.
// Centralizing this here makes it easy to inject RAG context
// or swap prompt strategies without touching the provider layer.

export interface BuiltPrompts {
  system: string;
  user: string;
  templateId: string;
}

export class PromptBuilderService {
  build(request: GenerateCourseRequest, ragDocs: string[] = []): BuiltPrompts {
    const language = request.language ?? "es";
    const level = request.level ?? "beginner";

    const template = matchTemplate(request.prompt, level);

    const systemPrompt = buildSystemPrompt(language);
    const ragBlock = buildRagContextBlock(ragDocs);
    const userPrompt = buildUserPrompt(request.prompt, level, request.options) + ragBlock;

    return {
      system: systemPrompt,
      user: userPrompt,
      templateId: template.id
    };
  }
}
