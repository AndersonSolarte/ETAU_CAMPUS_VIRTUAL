import type { CourseBlueprint, CourseLevel } from "../types/course-builder.types.js";

// Skeleton templates that pre-fill structure for common academic scenarios.
// The AI provider uses these as a starting point when the prompt is short.

export interface AcademicTemplate {
  id: string;
  name: string;
  description: string;
  targetLevel: CourseLevel[];
  defaultWeeks: number;
  defaultWeeklyHours: number;
  examplePrompts: string[];
  partialBlueprint: Partial<CourseBlueprint>;
}

export const ACADEMIC_TEMPLATES: AcademicTemplate[] = [
  {
    id: "programming-intro",
    name: "Programación Introductoria",
    description: "Plantilla para cursos de introducción a la programación",
    targetLevel: ["beginner"],
    defaultWeeks: 8,
    defaultWeeklyHours: 4,
    examplePrompts: [
      "Crear curso de Python básico de 8 semanas",
      "Curso introductorio de JavaScript",
      "Fundamentos de programación en Java"
    ],
    partialBlueprint: {
      assessmentStrategy: {
        continuousAssessment: 50,
        finalProject: 40,
        participation: 10,
        description: "Evaluación continua mediante ejercicios prácticos y proyecto final integrador"
      }
    }
  },
  {
    id: "data-science",
    name: "Ciencia de Datos",
    description: "Plantilla para cursos de análisis y ciencia de datos",
    targetLevel: ["intermediate", "advanced"],
    defaultWeeks: 12,
    defaultWeeklyHours: 6,
    examplePrompts: [
      "Curso de Machine Learning con Python",
      "Análisis de datos con pandas y numpy",
      "Deep Learning aplicado"
    ],
    partialBlueprint: {
      assessmentStrategy: {
        continuousAssessment: 40,
        finalProject: 50,
        participation: 10,
        description: "Proyectos de análisis con datasets reales y defensa final"
      }
    }
  },
  {
    id: "business-management",
    name: "Administración y Gestión",
    description: "Plantilla para cursos de gestión empresarial",
    targetLevel: ["beginner", "intermediate"],
    defaultWeeks: 10,
    defaultWeeklyHours: 3,
    examplePrompts: [
      "Fundamentos de administración de empresas",
      "Gestión de proyectos con metodologías ágiles",
      "Marketing digital para empresas"
    ],
    partialBlueprint: {
      assessmentStrategy: {
        continuousAssessment: 60,
        finalProject: 30,
        participation: 10,
        description: "Casos de estudio, debates en foros y proyecto empresarial integrador"
      }
    }
  },
  {
    id: "general-academic",
    name: "Académico General",
    description: "Plantilla base para cualquier área del conocimiento",
    targetLevel: ["beginner", "intermediate", "advanced"],
    defaultWeeks: 8,
    defaultWeeklyHours: 4,
    examplePrompts: [],
    partialBlueprint: {
      assessmentStrategy: {
        continuousAssessment: 60,
        finalProject: 30,
        participation: 10,
        description: "Evaluación continua con actividades semanales y proyecto final"
      }
    }
  }
];

export function matchTemplate(prompt: string, level: CourseLevel): AcademicTemplate {
  const lower = prompt.toLowerCase();

  const match = ACADEMIC_TEMPLATES.find(t => {
    const inLevel = t.targetLevel.includes(level);
    const inPrompts = t.examplePrompts.some(p =>
      p.toLowerCase().split(" ").some(word => lower.includes(word))
    );
    return inLevel && inPrompts;
  });

  return match ?? ACADEMIC_TEMPLATES[ACADEMIC_TEMPLATES.length - 1];
}
