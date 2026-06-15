import { create } from "zustand";

import type {
  AcademicTemplate,
  CourseBlueprint,
  CourseGenerationOptions,
  CourseLanguage,
  CourseLevel,
  JobStatus,
  JobStatusResponse
} from "../types/course-builder.types";

interface FormState {
  prompt: string;
  language: CourseLanguage;
  level: CourseLevel;
  moodleCategoryId?: number;
  options: Required<CourseGenerationOptions>;
}

interface CourseBuilderState {
  form: FormState;
  currentJobId: string | null;
  currentJob: JobStatusResponse | null;
  jobs: JobStatusResponse[];
  templates: AcademicTemplate[];
  isGenerating: boolean;
  isDeploying: boolean;
  error: string | null;

  // Actions
  setPrompt: (prompt: string) => void;
  setLanguage: (language: CourseLanguage) => void;
  setLevel: (level: CourseLevel) => void;
  setOptions: (options: Partial<CourseGenerationOptions>) => void;
  applyTemplate: (template: AcademicTemplate) => void;
  setCurrentJobId: (jobId: string | null) => void;
  setCurrentJob: (job: JobStatusResponse | null) => void;
  setJobs: (jobs: JobStatusResponse[]) => void;
  setTemplates: (templates: AcademicTemplate[]) => void;
  setGenerating: (v: boolean) => void;
  setDeploying: (v: boolean) => void;
  setError: (error: string | null) => void;
  reset: () => void;
  getBlueprint: () => CourseBlueprint | undefined;
  getJobStatus: () => JobStatus | null;
}

const defaultOptions: Required<CourseGenerationOptions> = {
  includeQuizzes: true,
  includeAssignments: true,
  includeForums: true,
  includeRubrics: true,
  includeCompetencies: true,
  includeOutcomes: true,
  weeklyHours: 4
};

const defaultForm: FormState = {
  prompt: "",
  language: "es",
  level: "beginner",
  options: defaultOptions
};

export const useCourseBuilderStore = create<CourseBuilderState>((set, get) => ({
  form: defaultForm,
  currentJobId: null,
  currentJob: null,
  jobs: [],
  templates: [],
  isGenerating: false,
  isDeploying: false,
  error: null,

  setPrompt: prompt => set(s => ({ form: { ...s.form, prompt } })),
  setLanguage: language => set(s => ({ form: { ...s.form, language } })),
  setLevel: level => set(s => ({ form: { ...s.form, level } })),
  setOptions: opts => set(s => ({ form: { ...s.form, options: { ...s.form.options, ...opts } } })),

  applyTemplate: template =>
    set(s => ({
      form: {
        ...s.form,
        level: template.targetLevel[0] ?? "beginner",
        prompt: s.form.prompt || template.examplePrompts[0] || ""
      }
    })),

  setCurrentJobId: jobId => set({ currentJobId: jobId }),
  setCurrentJob: job => set({ currentJob: job }),
  setJobs: jobs => set({ jobs }),
  setTemplates: templates => set({ templates }),
  setGenerating: isGenerating => set({ isGenerating }),
  setDeploying: isDeploying => set({ isDeploying }),
  setError: error => set({ error }),

  reset: () => set({ form: defaultForm, currentJobId: null, currentJob: null, error: null }),

  getBlueprint: () => get().currentJob?.blueprint,
  getJobStatus: () => get().currentJob?.status ?? null
}));
