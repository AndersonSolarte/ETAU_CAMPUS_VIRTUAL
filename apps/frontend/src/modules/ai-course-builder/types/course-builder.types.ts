export type CourseLevel = "beginner" | "intermediate" | "advanced";
export type CourseLanguage = "es" | "en";
export type JobStatus = "pending" | "processing" | "completed" | "failed";
export type ActivityType = "quiz" | "assignment" | "forum" | "resource" | "page" | "url";

export interface GenerateCourseRequest {
  prompt: string;
  language?: CourseLanguage;
  level?: CourseLevel;
  moodleCategoryId?: number;
  options?: CourseGenerationOptions;
}

export interface CourseGenerationOptions {
  includeQuizzes?: boolean;
  includeAssignments?: boolean;
  includeForums?: boolean;
  includeRubrics?: boolean;
  includeCompetencies?: boolean;
  includeOutcomes?: boolean;
  weeklyHours?: number;
}

export interface CourseBlueprint {
  title: string;
  shortname: string;
  summary: string;
  language: string;
  level: CourseLevel;
  totalWeeks: number;
  weeklyHours: number;
  objectives: string[];
  competencies: Competency[];
  outcomes: LearningOutcome[];
  modules: CourseModule[];
  assessmentStrategy: AssessmentStrategy;
}

export interface Competency {
  id: string;
  name: string;
  description: string;
  bloomLevel: string;
}

export interface LearningOutcome {
  id: string;
  statement: string;
  competencyId: string;
  assessedBy: string[];
}

export interface CourseModule {
  weekNumber: number;
  title: string;
  description: string;
  topics: string[];
  activities: CourseActivity[];
  resources: CourseResource[];
  estimatedHours: number;
}

export interface CourseActivity {
  type: ActivityType;
  title: string;
  description: string;
  instructions?: string;
  weight?: number;
}

export interface CourseResource {
  type: "file" | "url" | "page";
  title: string;
  description: string;
}

export interface AssessmentStrategy {
  continuousAssessment: number;
  finalProject: number;
  participation: number;
  description: string;
}

export interface JobStatusResponse {
  jobId: string;
  status: JobStatus;
  progress?: number;
  blueprint?: CourseBlueprint;
  moodleCourseId?: number;
  moodleCourseUrl?: string;
  errorMessage?: string;
  createdAt: string;
  updatedAt: string;
}

export interface GenerateJobCreated {
  jobId: string;
  status: JobStatus;
  message: string;
}

export interface AcademicTemplate {
  id: string;
  name: string;
  description: string;
  targetLevel: CourseLevel[];
  defaultWeeks: number;
  examplePrompts: string[];
}
