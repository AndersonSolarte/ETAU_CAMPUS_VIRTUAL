import type { NextFunction, Request, Response } from "express";
import { z } from "zod";

import type { AiCourseBuilderService } from "./ai-course-builder.service.js";
import type { GenerateCourseRequest } from "./types/course-builder.types.js";

// ─── Validation schemas ────────────────────────────────────────────────────────

const GenerateCourseSchema = z.object({
  prompt: z.string().min(10, "El prompt debe tener al menos 10 caracteres").max(1000),
  language: z.enum(["es", "en"]).default("es"),
  level: z.enum(["beginner", "intermediate", "advanced"]).default("beginner"),
  moodleCategoryId: z.number().int().positive().optional(),
  options: z
    .object({
      includeQuizzes: z.boolean().default(true),
      includeAssignments: z.boolean().default(true),
      includeForums: z.boolean().default(true),
      includeRubrics: z.boolean().default(true),
      includeCompetencies: z.boolean().default(true),
      includeOutcomes: z.boolean().default(true),
      weeklyHours: z.number().min(1).max(20).default(4)
    })
    .optional()
});

// ─── Controller ────────────────────────────────────────────────────────────────

export class AiCourseBuilderController {
  constructor(private readonly service: AiCourseBuilderService) {}

  // POST /api/ai-course-builder/generate
  generate = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = GenerateCourseSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }

      const job = await this.service.startGeneration(parsed.data as GenerateCourseRequest);
      res.status(202).json({ jobId: job.id, status: job.status, message: "Generación iniciada" });
    } catch (err) {
      next(err);
    }
  };

  // GET /api/ai-course-builder/jobs/:jobId
  getJob = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const result = await this.service.getJobStatus(String(req.params.jobId));
      if (!result) {
        res.status(404).json({ error: "Job not found" });
        return;
      }
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // GET /api/ai-course-builder/jobs
  listJobs = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const limit = Number(req.query.limit) || 20;
      const jobs = await this.service.listJobs(limit);
      res.json({ jobs, total: jobs.length });
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai-course-builder/jobs/:jobId/deploy
  deployToMoodle = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const request = req.body as GenerateCourseRequest;
      const result = await this.service.deployToMoodle(String(req.params.jobId), request);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // GET /api/ai-course-builder/templates
  getTemplates = async (_req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const { ACADEMIC_TEMPLATES } = await import("./templates/academic-templates.js");
      res.json({ templates: ACADEMIC_TEMPLATES });
    } catch (err) {
      next(err);
    }
  };

  // GET /api/ai-course-builder/health
  health = async (_req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const { OpenAiProvider } = await import("./providers/openai.provider.js");
      const provider = new OpenAiProvider();
      const available = await provider.isAvailable();
      res.json({
        status: available ? "ok" : "degraded",
        provider: provider.name,
        model: provider.model,
        timestamp: new Date().toISOString()
      });
    } catch (err) {
      next(err);
    }
  };
}
