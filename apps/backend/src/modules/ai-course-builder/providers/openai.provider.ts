import OpenAI from "openai";

import { env } from "../../../config/env.js";
import type { AiGenerationResult, CourseBlueprint, GenerateCourseRequest } from "../types/course-builder.types.js";
import type { IAiProvider } from "./ai-provider.interface.js";

export class OpenAiProvider implements IAiProvider {
  readonly name = "openai";
  readonly model: string;

  private readonly client: OpenAI;

  constructor() {
    this.client = new OpenAI({ apiKey: env.OPENAI_API_KEY });
    this.model = env.OPENAI_MODEL;
  }

  async generateCourseBlueprint(
    _request: GenerateCourseRequest,
    systemPrompt: string,
    userPrompt: string
  ): Promise<AiGenerationResult> {
    const start = Date.now();

    const response = await this.client.chat.completions.create({
      model: this.model,
      max_tokens: env.OPENAI_MAX_TOKENS,
      temperature: 0.7,
      response_format: { type: "json_object" },
      messages: [
        { role: "system", content: systemPrompt },
        { role: "user", content: userPrompt }
      ]
    });

    const raw = response.choices[0]?.message?.content;
    if (!raw) throw new Error("OpenAI returned an empty response");

    const blueprint = JSON.parse(raw) as CourseBlueprint;

    return {
      blueprint,
      tokensUsed: response.usage?.total_tokens ?? 0,
      model: response.model,
      durationMs: Date.now() - start
    };
  }

  async isAvailable(): Promise<boolean> {
    try {
      await this.client.models.retrieve(this.model);
      return true;
    } catch {
      return false;
    }
  }
}
