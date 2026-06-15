import { PrismaClient } from "@prisma/client";
import type { Router } from "express";

import { AiCourseBuilderService } from "./ai-course-builder.service.js";
import { createAiCourseBuilderRouter } from "./ai-course-builder.routes.js";

// Module factory — instantiates services, wires dependencies, returns router.
// Import this in apps/backend/src/routes/index.ts.
export function createAiCourseBuilderModule(db: PrismaClient): Router {
  const service = new AiCourseBuilderService(db);
  return createAiCourseBuilderRouter(service);
}

export { AiCourseBuilderService };
