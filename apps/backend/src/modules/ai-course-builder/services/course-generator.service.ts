import type { IAiProvider } from "../providers/ai-provider.interface.js";
import type {
  AiGenerationResult,
  GenerateCourseRequest
} from "../types/course-builder.types.js";
import { PromptBuilderService } from "./prompt-builder.service.js";

// Orchestrates the AI generation pipeline:
// request → prompts → provider → validated blueprint
export class CourseGeneratorService {
  private readonly promptBuilder: PromptBuilderService;

  constructor(private readonly aiProvider: IAiProvider) {
    this.promptBuilder = new PromptBuilderService();
  }

  async generate(
    request: GenerateCourseRequest,
    // RAG docs injected here when vector DB is ready
    ragDocs: string[] = []
  ): Promise<AiGenerationResult> {
    const prompts = this.promptBuilder.build(request, ragDocs);

    const result = await this.aiProvider.generateCourseBlueprint(
      request,
      prompts.system,
      prompts.user
    );

    this.validate(result);

    return result;
  }

  private validate(result: AiGenerationResult): void {
    const bp = result.blueprint;

    if (!bp.title || !bp.shortname) {
      throw new Error("AI blueprint is missing required fields: title, shortname");
    }
    if (!Array.isArray(bp.modules) || bp.modules.length === 0) {
      throw new Error("AI blueprint must contain at least one module");
    }
    if (!bp.totalWeeks || bp.totalWeeks < 1) {
      throw new Error("AI blueprint must specify totalWeeks >= 1");
    }
  }
}
