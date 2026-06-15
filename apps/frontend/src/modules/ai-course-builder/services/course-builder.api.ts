import { http } from "@/services/http";

import type {
  AcademicTemplate,
  GenerateCourseRequest,
  GenerateJobCreated,
  JobStatusResponse
} from "../types/course-builder.types";

const BASE = "/ai-course-builder";

export const courseBuilderApi = {
  generate(request: GenerateCourseRequest): Promise<GenerateJobCreated> {
    return http.post<GenerateJobCreated>(`${BASE}/generate`, request).then(r => r.data);
  },

  getJob(jobId: string): Promise<JobStatusResponse> {
    return http.get<JobStatusResponse>(`${BASE}/jobs/${jobId}`).then(r => r.data);
  },

  listJobs(limit = 20): Promise<{ jobs: JobStatusResponse[]; total: number }> {
    return http.get<{ jobs: JobStatusResponse[]; total: number }>(`${BASE}/jobs?limit=${limit}`).then(r => r.data);
  },

  deployToMoodle(jobId: string, request: GenerateCourseRequest): Promise<JobStatusResponse> {
    return http.post<JobStatusResponse>(`${BASE}/jobs/${jobId}/deploy`, request).then(r => r.data);
  },

  getTemplates(): Promise<{ templates: AcademicTemplate[] }> {
    return http.get<{ templates: AcademicTemplate[] }>(`${BASE}/templates`).then(r => r.data);
  },

  health(): Promise<{ status: string; provider: string; model: string }> {
    return http.get<{ status: string; provider: string; model: string }>(`${BASE}/health`).then(r => r.data);
  }
};
