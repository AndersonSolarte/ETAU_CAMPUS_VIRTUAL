import type { AiGenerationResult, GenerateCourseRequest } from "../types/course-builder.types.js";

// Contract that every AI provider must satisfy.
// Swap OpenAI for Anthropic, Gemini, or a local model
// by creating a new class that implements this interface.
export interface IAiProvider {
  readonly name: string;
  readonly model: string;

  generateCourseBlueprint(request: GenerateCourseRequest, systemPrompt: string, userPrompt: string): Promise<AiGenerationResult>;

  isAvailable(): Promise<boolean>;
}

// Marker type for DI / future provider registry
export type AiProviderName = "openai" | "anthropic" | "gemini" | "ollama";
