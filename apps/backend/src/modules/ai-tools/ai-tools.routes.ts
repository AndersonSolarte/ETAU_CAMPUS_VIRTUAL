import { Router } from "express";

import { AiToolsController } from "./ai-tools.controller.js";
import type { AiToolsService } from "./ai-tools.service.js";

export function createAiToolsRouter(service: AiToolsService): Router {
  const router = Router();
  const ctrl = new AiToolsController(service);

  router.post("/assign/grade",          ctrl.gradeAssignment);
  router.post("/tutor/chat",            ctrl.tutorChat);
  router.post("/forum/moderate",        ctrl.moderatePost);
  router.post("/forum/generate-debate", ctrl.generateDebate);
  router.post("/student/profile",       ctrl.studentProfile);
  router.post("/rules/evaluate",        ctrl.evaluateRules);
  router.post("/ranking/analyze",       ctrl.analyzeRanking);
  router.post("/certificate/description", ctrl.certificateDescription);
  router.post("/courses/recommend",     ctrl.recommendCourses);

  return router;
}
