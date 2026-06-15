import type { Router } from "express";

import { AiToolsService } from "./ai-tools.service.js";
import { createAiToolsRouter } from "./ai-tools.routes.js";

export function createAiToolsModule(): Router {
  const service = new AiToolsService();
  return createAiToolsRouter(service);
}

export { AiToolsService };
