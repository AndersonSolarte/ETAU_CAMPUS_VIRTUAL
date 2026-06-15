import { PrismaClient } from "@prisma/client";

import { OpenAiProvider } from "./providers/openai.provider.js";
import { CourseGeneratorService } from "./services/course-generator.service.js";
import { MoodleCourseService } from "./services/moodle-course.service.js";
import type {
  AiCourseJobRecord,
  CourseBlueprint,
  GenerateCourseRequest,
  JobStatus,
  JobStatusResponse
} from "./types/course-builder.types.js";

// Orchestrates the full AI course creation pipeline:
// generate blueprint → persist job → (optionally) push to Moodle
export class AiCourseBuilderService {
  private readonly generator: CourseGeneratorService;
  private readonly moodleService: MoodleCourseService;

  constructor(private readonly db: PrismaClient) {
    this.generator = new CourseGeneratorService(new OpenAiProvider());
    this.moodleService = new MoodleCourseService();
  }

  // POST /ai-course-builder/generate
  // Creates a job record and triggers async generation
  async startGeneration(request: GenerateCourseRequest): Promise<AiCourseJobRecord> {
    const job = await this.db.aiCourseJob.create({
      data: {
        status: "pending",
        prompt: request.prompt,
        requestPayload: request as object
      }
    });

    // Fire and forget — result is stored in DB; client polls via getJobStatus
    this.runGeneration(job.id, request).catch(err => {
      console.error(`[AiCourseBuilder] Job ${job.id} failed:`, err);
    });

    return this.mapJob(job);
  }

  // GET /ai-course-builder/jobs/:jobId
  async getJobStatus(jobId: string): Promise<JobStatusResponse | null> {
    const job = await this.db.aiCourseJob.findUnique({ where: { id: jobId } });
    if (!job) return null;

    const moodleCourseUrl = job.moodleCourseId
      ? `${process.env.MOODLE_BASE_URL}/course/view.php?id=${job.moodleCourseId}`
      : undefined;

    return {
      jobId: job.id,
      status: job.status as JobStatus,
      progress: this.calcProgress(job.status as JobStatus),
      blueprint: job.blueprint as unknown as CourseBlueprint | undefined,
      moodleCourseId: job.moodleCourseId ?? undefined,
      moodleCourseUrl,
      errorMessage: job.errorMessage ?? undefined,
      createdAt: job.createdAt,
      updatedAt: job.updatedAt
    };
  }

  // GET /ai-course-builder/jobs (list recent jobs)
  async listJobs(limit = 20): Promise<JobStatusResponse[]> {
    const jobs = await this.db.aiCourseJob.findMany({
      orderBy: { createdAt: "desc" },
      take: limit
    });
    return Promise.all(jobs.map((j: { id: string }) => this.getJobStatus(j.id))) as Promise<JobStatusResponse[]>;
  }

  // POST /ai-course-builder/jobs/:jobId/deploy
  // Pushes a completed blueprint to Moodle (separate step, opt-in)
  async deployToMoodle(jobId: string, request: GenerateCourseRequest): Promise<JobStatusResponse> {
    const job = await this.db.aiCourseJob.findUniqueOrThrow({ where: { id: jobId } });

    if (job.status !== "completed" || !job.blueprint) {
      throw new Error("Job must be in completed state with a blueprint before deploying");
    }

    const blueprint = job.blueprint as unknown as CourseBlueprint;
    const moodleCourseId = await this.moodleService.createCourseFromBlueprint(blueprint, request);

    await this.db.aiCourseJob.update({
      where: { id: jobId },
      data: { moodleCourseId }
    });

    return (await this.getJobStatus(jobId))!;
  }

  // ─── Private ─────────────────────────────────────────────────────────────────

  private async runGeneration(jobId: string, request: GenerateCourseRequest): Promise<void> {
    await this.db.aiCourseJob.update({
      where: { id: jobId },
      data: { status: "processing" }
    });

    try {
      const result = await this.generator.generate(request);

      await this.db.aiCourseJob.update({
        where: { id: jobId },
        data: {
          status: "completed",
          blueprint: result.blueprint as object,
          tokensUsed: result.tokensUsed,
          modelUsed: result.model,
          durationMs: result.durationMs
        }
      });
    } catch (err) {
      const message = err instanceof Error ? err.message : String(err);
      await this.db.aiCourseJob.update({
        where: { id: jobId },
        data: { status: "failed", errorMessage: message }
      });
    }
  }

  private mapJob(job: { id: string; status: string; prompt: string; createdAt: Date; updatedAt: Date }): AiCourseJobRecord {
    return {
      id: job.id,
      status: job.status as JobStatus,
      prompt: job.prompt,
      createdAt: job.createdAt,
      updatedAt: job.updatedAt
    };
  }

  private calcProgress(status: JobStatus): number {
    const map: Record<JobStatus, number> = {
      pending: 5,
      processing: 50,
      completed: 100,
      failed: 0
    };
    return map[status];
  }
}
