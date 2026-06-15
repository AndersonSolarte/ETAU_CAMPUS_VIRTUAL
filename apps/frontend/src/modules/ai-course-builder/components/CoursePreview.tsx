import { BookOpen, ChevronDown, ChevronRight, Clock, ExternalLink, Layers, Target, Trophy } from "lucide-react";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { useCourseBuilder } from "../hooks/useCourseBuilder";
import type { CourseBlueprint, CourseModule } from "../types/course-builder.types";

interface Props {
  blueprint: CourseBlueprint;
  moodleCourseUrl?: string;
}

function ModuleCard({ module }: { module: CourseModule }) {
  const [open, setOpen] = useState(false);

  const quizCount = module.activities.filter(a => a.type === "quiz").length;
  const assignCount = module.activities.filter(a => a.type === "assignment").length;
  const forumCount = module.activities.filter(a => a.type === "forum").length;

  return (
    <div className="rounded-lg border border-gray-200 overflow-hidden">
      <button
        onClick={() => setOpen(o => !o)}
        className="flex w-full items-center justify-between px-4 py-3 text-left hover:bg-gray-50"
      >
        <div className="flex items-center gap-3">
          <span className="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
            {module.weekNumber}
          </span>
          <div>
            <p className="text-sm font-medium text-gray-900">{module.title}</p>
            <div className="flex gap-2 text-xs text-gray-400">
              {quizCount > 0 && <span>{quizCount} quiz</span>}
              {assignCount > 0 && <span>{assignCount} tarea</span>}
              {forumCount > 0 && <span>{forumCount} foro</span>}
              <span className="flex items-center gap-1">
                <Clock className="h-3 w-3" />
                {module.estimatedHours}h
              </span>
            </div>
          </div>
        </div>
        {open ? <ChevronDown className="h-4 w-4 text-gray-400" /> : <ChevronRight className="h-4 w-4 text-gray-400" />}
      </button>

      {open && (
        <div className="border-t border-gray-100 px-4 py-3 space-y-3">
          <p className="text-sm text-gray-600">{module.description}</p>

          {module.topics.length > 0 && (
            <div>
              <p className="text-xs font-semibold text-gray-500 uppercase mb-1">Temas</p>
              <ul className="space-y-1">
                {module.topics.map((topic, i) => (
                  <li key={i} className="text-sm text-gray-700 flex items-start gap-2">
                    <span className="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-400" />
                    {topic}
                  </li>
                ))}
              </ul>
            </div>
          )}

          {module.activities.length > 0 && (
            <div>
              <p className="text-xs font-semibold text-gray-500 uppercase mb-1">Actividades</p>
              <div className="space-y-1">
                {module.activities.map((act, i) => (
                  <div key={i} className="flex items-center gap-2 text-sm text-gray-700">
                    <span className="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-500 uppercase">
                      {act.type}
                    </span>
                    {act.title}
                    {act.weight ? <span className="ml-auto text-xs text-gray-400">{act.weight}%</span> : null}
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

export function CoursePreview({ blueprint, moodleCourseUrl }: Props) {
  const { deployToMoodle, isDeploying } = useCourseBuilder();

  return (
    <div className="space-y-6">
      {/* Course header */}
      <div className="rounded-xl border border-green-200 bg-green-50 p-5">
        <div className="flex items-start justify-between gap-4">
          <div className="flex-1">
            <h3 className="text-xl font-bold text-gray-900">{blueprint.title}</h3>
            <p className="mt-1 text-sm font-mono text-gray-500">{blueprint.shortname}</p>
            <p className="mt-2 text-sm text-gray-700">{blueprint.summary}</p>
          </div>
          <div className="flex shrink-0 flex-col items-end gap-1 text-sm">
            <span className="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 shadow-sm">
              {blueprint.totalWeeks} semanas
            </span>
            <span className="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 shadow-sm">
              {blueprint.weeklyHours}h/semana
            </span>
            <span className="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
              {blueprint.level}
            </span>
          </div>
        </div>
      </div>

      {/* Deploy to Moodle */}
      {!moodleCourseUrl ? (
        <Button
          onClick={deployToMoodle}
          disabled={isDeploying}
          variant="default"
          className="w-full bg-orange-600 hover:bg-orange-700"
        >
          <Layers className="mr-2 h-4 w-4" />
          {isDeploying ? "Creando en Moodle..." : "Crear curso en Moodle"}
        </Button>
      ) : (
        <a
          href={moodleCourseUrl}
          target="_blank"
          rel="noopener noreferrer"
          className="flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700"
        >
          <ExternalLink className="h-4 w-4" />
          Ver curso en Moodle
        </a>
      )}

      {/* Stats */}
      <div className="grid grid-cols-3 gap-3">
        <div className="rounded-lg border border-gray-200 p-3 text-center">
          <Target className="mx-auto mb-1 h-5 w-5 text-blue-500" />
          <p className="text-lg font-bold text-gray-900">{blueprint.competencies?.length ?? 0}</p>
          <p className="text-xs text-gray-500">Competencias</p>
        </div>
        <div className="rounded-lg border border-gray-200 p-3 text-center">
          <BookOpen className="mx-auto mb-1 h-5 w-5 text-purple-500" />
          <p className="text-lg font-bold text-gray-900">
            {blueprint.modules.reduce((s, m) => s + m.activities.length, 0)}
          </p>
          <p className="text-xs text-gray-500">Actividades</p>
        </div>
        <div className="rounded-lg border border-gray-200 p-3 text-center">
          <Trophy className="mx-auto mb-1 h-5 w-5 text-yellow-500" />
          <p className="text-lg font-bold text-gray-900">{blueprint.outcomes?.length ?? 0}</p>
          <p className="text-xs text-gray-500">Resultados</p>
        </div>
      </div>

      {/* Objectives */}
      {blueprint.objectives?.length > 0 && (
        <div>
          <h4 className="mb-2 text-sm font-semibold text-gray-700">Objetivos del curso</h4>
          <ul className="space-y-1.5">
            {blueprint.objectives.map((obj, i) => (
              <li key={i} className="flex items-start gap-2 text-sm text-gray-700">
                <span className="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500" />
                {obj}
              </li>
            ))}
          </ul>
        </div>
      )}

      {/* Assessment Strategy */}
      <div className="rounded-lg border border-gray-200 p-4">
        <h4 className="mb-3 text-sm font-semibold text-gray-700">Estrategia de evaluación</h4>
        <div className="space-y-2">
          {[
            ["Evaluación continua", blueprint.assessmentStrategy.continuousAssessment, "bg-blue-500"],
            ["Proyecto final", blueprint.assessmentStrategy.finalProject, "bg-purple-500"],
            ["Participación", blueprint.assessmentStrategy.participation, "bg-green-500"]
          ].map(([label, value, color]) => (
            <div key={label as string} className="flex items-center gap-3">
              <span className="w-32 text-xs text-gray-600">{label as string}</span>
              <div className="flex-1 h-2 rounded-full bg-gray-100">
                <div
                  className={`h-2 rounded-full ${color}`}
                  style={{ width: `${value}%` }}
                />
              </div>
              <span className="w-8 text-right text-xs font-medium text-gray-700">{value}%</span>
            </div>
          ))}
        </div>
        <p className="mt-3 text-xs text-gray-500">{blueprint.assessmentStrategy.description}</p>
      </div>

      {/* Weekly modules */}
      <div>
        <h4 className="mb-3 text-sm font-semibold text-gray-700">
          Estructura del curso — {blueprint.modules.length} semanas
        </h4>
        <div className="space-y-2">
          {blueprint.modules.map(module => (
            <ModuleCard key={module.weekNumber} module={module} />
          ))}
        </div>
      </div>
    </div>
  );
}
