import { BookOpen, Loader2, Sparkles, Wand2 } from "lucide-react";
import { useEffect } from "react";

import { Button } from "@/components/ui/button";
import { useCourseBuilder } from "../hooks/useCourseBuilder";
import type { AcademicTemplate, CourseLanguage, CourseLevel } from "../types/course-builder.types";

export function CourseBuilderForm() {
  const {
    form,
    isGenerating,
    error,
    templates,
    setPrompt,
    setLanguage,
    setLevel,
    setOptions,
    applyTemplate,
    generate,
    loadTemplates
  } = useCourseBuilder();

  useEffect(() => {
    loadTemplates();
  }, [loadTemplates]);

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center gap-3">
        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
          <Sparkles className="h-5 w-5 text-white" />
        </div>
        <div>
          <h2 className="text-lg font-semibold text-gray-900">Crear Curso con IA</h2>
          <p className="text-sm text-gray-500">Describe el curso que quieres crear</p>
        </div>
      </div>

      {/* Main prompt input */}
      <div className="space-y-2">
        <label className="text-sm font-medium text-gray-700">
          Descripción del curso
        </label>
        <textarea
          value={form.prompt}
          onChange={e => setPrompt(e.target.value)}
          placeholder='Ejemplo: "Crear curso de Python básico de 8 semanas para estudiantes universitarios sin experiencia previa"'
          rows={4}
          className="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:opacity-50"
          disabled={isGenerating}
        />
        <p className="text-xs text-gray-400">{form.prompt.length}/1000 caracteres</p>
      </div>

      {/* Configuration row */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div className="space-y-1">
          <label className="text-sm font-medium text-gray-700">Idioma</label>
          <select
            value={form.language}
            onChange={e => setLanguage(e.target.value as CourseLanguage)}
            disabled={isGenerating}
            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:opacity-50"
          >
            <option value="es">Español</option>
            <option value="en">English</option>
          </select>
        </div>

        <div className="space-y-1">
          <label className="text-sm font-medium text-gray-700">Nivel</label>
          <select
            value={form.level}
            onChange={e => setLevel(e.target.value as CourseLevel)}
            disabled={isGenerating}
            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:opacity-50"
          >
            <option value="beginner">Principiante</option>
            <option value="intermediate">Intermedio</option>
            <option value="advanced">Avanzado</option>
          </select>
        </div>

        <div className="space-y-1">
          <label className="text-sm font-medium text-gray-700">Horas/semana</label>
          <input
            type="number"
            min={1}
            max={20}
            value={form.options.weeklyHours}
            onChange={e => setOptions({ weeklyHours: Number(e.target.value) })}
            disabled={isGenerating}
            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:opacity-50"
          />
        </div>
      </div>

      {/* Options toggles */}
      <div className="rounded-lg border border-gray-200 p-4">
        <p className="mb-3 text-sm font-medium text-gray-700">Incluir en el curso</p>
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
          {(
            [
              ["includeQuizzes", "Quizzes"],
              ["includeAssignments", "Tareas"],
              ["includeForums", "Foros"],
              ["includeRubrics", "Rúbricas"],
              ["includeCompetencies", "Competencias"],
              ["includeOutcomes", "Resultados"]
            ] as [keyof typeof form.options, string][]
          ).map(([key, label]) => (
            <label key={key} className="flex items-center gap-2 text-sm text-gray-600">
              <input
                type="checkbox"
                checked={form.options[key] as boolean}
                onChange={e => setOptions({ [key]: e.target.checked })}
                disabled={isGenerating}
                className="h-4 w-4 rounded border-gray-300 text-blue-600"
              />
              {label}
            </label>
          ))}
        </div>
      </div>

      {/* Templates */}
      {templates.length > 0 && (
        <div className="space-y-2">
          <p className="text-sm font-medium text-gray-700">Plantillas rápidas</p>
          <div className="flex flex-wrap gap-2">
            {templates.map((t: AcademicTemplate) => (
              <button
                key={t.id}
                onClick={() => applyTemplate(t)}
                disabled={isGenerating}
                className="flex items-center gap-1 rounded-full border border-gray-200 px-3 py-1 text-xs text-gray-600 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 disabled:opacity-50"
              >
                <BookOpen className="h-3 w-3" />
                {t.name}
              </button>
            ))}
          </div>
        </div>
      )}

      {/* Error */}
      {error && (
        <div className="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
          {error}
        </div>
      )}

      {/* Submit */}
      <Button
        onClick={generate}
        disabled={isGenerating || form.prompt.trim().length < 10}
        className="w-full"
      >
        {isGenerating ? (
          <>
            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
            Generando curso...
          </>
        ) : (
          <>
            <Wand2 className="mr-2 h-4 w-4" />
            Generar Curso con IA
          </>
        )}
      </Button>
    </div>
  );
}
