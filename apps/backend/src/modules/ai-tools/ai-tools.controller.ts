import type { NextFunction, Request, Response } from "express";
import { z } from "zod";

import type { AiToolsService } from "./ai-tools.service.js";

// ─── Schemas ──────────────────────────────────────────────────────────────────

const GradeAssignmentSchema = z.object({
  assignmentId: z.number().int().positive(),
  maxGrade: z.number().positive().default(100),
  rubric: z.string().optional(),
  submissions: z
    .array(
      z.object({
        submissionId: z.number().int(),
        userId: z.number().int(),
        studentName: z.string(),
        text: z.string()
      })
    )
    .min(1, "Debe haber al menos una entrega")
});

const TutorChatSchema = z.object({
  courseId: z.number().int().positive(),
  courseName: z.string().min(1),
  message: z.string().min(1).max(2000),
  history: z
    .array(z.object({ role: z.enum(["user", "assistant"]), content: z.string() }))
    .default([])
});

const ModeratePostSchema = z.object({
  postId: z.number().int(),
  courseId: z.number().int(),
  subject: z.string().default(""),
  text: z.string().min(1)
});

const GenerateDebateSchema = z.object({
  courseId: z.number().int(),
  forumId: z.number().int(),
  topic: z.string().min(5).max(500)
});

const StudentProfileSchema = z.object({
  studentData: z.object({
    userId: z.number().int(),
    fullName: z.string(),
    coursesEnrolled: z.number().int().default(0),
    activitiesCompleted: z.number().int().default(0),
    avgGrade: z.number().nullable().default(null),
    forumPosts: z.number().int().default(0),
    lastAccess: z.number().nullable().default(null)
  })
});

const EvaluateRulesSchema = z.object({
  ruleId: z.number().int(),
  trigger: z.string(),
  condition: z.string().default(""),
  students: z.array(z.object({ userId: z.number().int(), courseId: z.number().int() }))
});

const AnalyzeRankingSchema = z.object({
  courseId: z.number().int(),
  activities: z.array(
    z.object({
      cmid: z.number().int(),
      name: z.string(),
      type: z.string(),
      completions: z.number().int().default(0),
      totalAttempts: z.number().int().default(0)
    })
  )
});

const CertificateDescriptionSchema = z.object({
  certName: z.string().min(1),
  orgName: z.string().min(1),
  courseId: z.number().int().optional()
});

const RecommendCoursesSchema = z.object({
  userId: z.number().int(),
  enrolledCourseIds: z.array(z.number().int()).default([]),
  availableCourses: z.array(
    z.object({
      id: z.number().int(),
      name: z.string(),
      summary: z.string().default(""),
      category: z.number().int().default(0)
    })
  )
});

// ─── Controller ───────────────────────────────────────────────────────────────

export class AiToolsController {
  constructor(private readonly service: AiToolsService) {}

  // POST /api/ai/assign/grade
  gradeAssignment = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = GradeAssignmentSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.gradeAssignment(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/tutor/chat
  tutorChat = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = TutorChatSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.tutorChat(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/forum/moderate
  moderatePost = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = ModeratePostSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.moderateForumPost(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/forum/generate-debate
  generateDebate = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = GenerateDebateSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.generateForumDebate(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/student/profile
  studentProfile = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = StudentProfileSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.analyzeStudentProfile(parsed.data.studentData);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/rules/evaluate
  evaluateRules = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = EvaluateRulesSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.evaluateRules(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/ranking/analyze
  analyzeRanking = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = AnalyzeRankingSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.analyzeActivityRanking(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/certificate/description
  certificateDescription = async (
    req: Request,
    res: Response,
    next: NextFunction
  ): Promise<void> => {
    try {
      const parsed = CertificateDescriptionSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.generateCertificateDescription(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };

  // POST /api/ai/courses/recommend
  recommendCourses = async (req: Request, res: Response, next: NextFunction): Promise<void> => {
    try {
      const parsed = RecommendCoursesSchema.safeParse(req.body);
      if (!parsed.success) {
        res.status(400).json({ error: "Validation failed", details: parsed.error.flatten() });
        return;
      }
      const result = await this.service.recommendCourses(parsed.data);
      res.json(result);
    } catch (err) {
      next(err);
    }
  };
}
