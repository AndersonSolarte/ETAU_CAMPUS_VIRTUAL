// ─── Enums ───────────────────────────────────────────────────────────────────

export type CourseLevel = "beginner" | "intermediate" | "advanced";
export type CourseLanguage = "es" | "en";
export type JobStatus = "pending" | "processing" | "completed" | "failed";
export type ActivityType = "quiz" | "assignment" | "forum" | "resource" | "page" | "url";

// ─── Input DTOs ───────────────────────────────────────────────────────────────

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

// ─── Course Blueprint (output from AI) ───────────────────────────────────────

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
  bloomLevel: BloomLevel;
}

export interface LearningOutcome {
  id: string;
  statement: string;
  competencyId: string;
  assessedBy: string[];
}

export type BloomLevel =
  | "remember"
  | "understand"
  | "apply"
  | "analyze"
  | "evaluate"
  | "create";

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
  dueWeek?: number;
  weight?: number;
  rubric?: Rubric;
  quiz?: QuizConfig;
  forum?: ForumConfig;
}

export interface CourseResource {
  type: "file" | "url" | "page";
  title: string;
  description: string;
  url?: string;
  content?: string;
}

export interface Rubric {
  title: string;
  criteria: RubricCriterion[];
}

export interface RubricCriterion {
  name: string;
  description: string;
  maxScore: number;
  levels: RubricLevel[];
}

export interface RubricLevel {
  label: string;
  score: number;
  description: string;
}

export interface QuizConfig {
  questionCount: number;
  questionTypes: Array<"multiple_choice" | "true_false" | "short_answer">;
  timeLimit?: number;
  attempts?: number;
  passingScore?: number;
  sampleQuestions?: QuizQuestion[];
}

export interface QuizQuestion {
  type: "multiple_choice" | "true_false" | "short_answer";
  question: string;
  options?: string[];
  correctAnswer?: string;
}

export interface ForumConfig {
  type: "general" | "single" | "question";
  initialPost?: string;
  graded?: boolean;
}

export interface AssessmentStrategy {
  continuousAssessment: number;
  finalProject: number;
  participation: number;
  description: string;
}

// ─── Job / Async tracking ────────────────────────────────────────────────────

export interface AiCourseJobRecord {
  id: string;
  status: JobStatus;
  prompt: string;
  blueprint?: CourseBlueprint;
  moodleCourseId?: number;
  errorMessage?: string;
  createdAt: Date;
  updatedAt: Date;
}

export interface JobStatusResponse {
  jobId: string;
  status: JobStatus;
  progress?: number;
  blueprint?: CourseBlueprint;
  moodleCourseId?: number;
  moodleCourseUrl?: string;
  errorMessage?: string;
  createdAt: Date;
  updatedAt: Date;
}

// ─── AI Provider contract ────────────────────────────────────────────────────

export interface AiGenerationResult {
  blueprint: CourseBlueprint;
  tokensUsed: number;
  model: string;
  durationMs: number;
}
