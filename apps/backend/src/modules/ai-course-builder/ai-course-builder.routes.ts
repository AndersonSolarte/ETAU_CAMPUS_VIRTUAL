import { Router } from "express";

import { AiCourseBuilderController } from "./ai-course-builder.controller.js";
import type { AiCourseBuilderService } from "./ai-course-builder.service.js";

export function createAiCourseBuilderRouter(service: AiCourseBuilderService): Router {
  const router = Router();
  const ctrl = new AiCourseBuilderController(service);

  // Health & metadata
  router.get("/health", ctrl.health);
  router.get("/templates", ctrl.getTemplates);

  // Job management
  router.post("/generate", ctrl.generate);
  router.get("/jobs", ctrl.listJobs);
  router.get("/jobs/:jobId", ctrl.getJob);
  router.post("/jobs/:jobId/deploy", ctrl.deployToMoodle);

  return router;
}
