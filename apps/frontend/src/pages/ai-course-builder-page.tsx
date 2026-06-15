import {
  CourseBuilderForm,
  CoursePreview,
  GenerationProgress,
  useCourseBuilder
} from "@/modules/ai-course-builder";

export function AiCourseBuilderPage() {
  const { currentJob, blueprint, isGenerating } = useCourseBuilder();

  const showProgress = isGenerating || (currentJob && currentJob.status !== "completed");
  const showPreview = blueprint != null;

  return (
    <div className="mx-auto max-w-4xl space-y-8 px-4 py-8">
      {/* Page header */}
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Generador de Cursos con IA</h1>
        <p className="mt-1 text-gray-500">
          Describe el curso que necesitas y la IA creará automáticamente la estructura completa
          con módulos, actividades, evaluaciones y rúbricas.
        </p>
      </div>

      {/* Two-column layout on large screens */}
      <div className="grid grid-cols-1 gap-8 lg:grid-cols-2">
        {/* Left: Form */}
        <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <CourseBuilderForm />
        </div>

        {/* Right: Progress or Preview */}
        <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          {!showProgress && !showPreview && (
            <div className="flex h-full min-h-48 flex-col items-center justify-center gap-3 text-center text-gray-400">
              <div className="text-5xl">🎓</div>
              <p className="text-sm">
                El blueprint del curso aparecerá aquí una vez generado
              </p>
            </div>
          )}

          {showProgress && currentJob && !showPreview && (
            <GenerationProgress job={currentJob} />
          )}

          {showPreview && blueprint && (
            <CoursePreview
              blueprint={blueprint}
              moodleCourseUrl={currentJob?.moodleCourseUrl}
            />
          )}
        </div>
      </div>
    </div>
  );
}
