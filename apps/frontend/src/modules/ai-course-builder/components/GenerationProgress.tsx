import { AlertCircle, CheckCircle2, Clock, Loader2 } from "lucide-react";

import type { JobStatus, JobStatusResponse } from "../types/course-builder.types";

interface Props {
  job: JobStatusResponse;
}

const STATUS_LABELS: Record<JobStatus, string> = {
  pending: "En cola...",
  processing: "Generando con IA...",
  completed: "Curso generado",
  failed: "Error en la generación"
};

const STATUS_COLORS: Record<JobStatus, string> = {
  pending: "text-yellow-600 bg-yellow-50 border-yellow-200",
  processing: "text-blue-600 bg-blue-50 border-blue-200",
  completed: "text-green-600 bg-green-50 border-green-200",
  failed: "text-red-600 bg-red-50 border-red-200"
};

function StatusIcon({ status }: { status: JobStatus }) {
  if (status === "processing") return <Loader2 className="h-5 w-5 animate-spin" />;
  if (status === "completed") return <CheckCircle2 className="h-5 w-5" />;
  if (status === "failed") return <AlertCircle className="h-5 w-5" />;
  return <Clock className="h-5 w-5" />;
}

export function GenerationProgress({ job }: Props) {
  const progress = job.progress ?? 0;

  return (
    <div className="space-y-4">
      {/* Status badge */}
      <div className={`flex items-center gap-3 rounded-lg border px-4 py-3 ${STATUS_COLORS[job.status]}`}>
        <StatusIcon status={job.status} />
        <div className="flex-1">
          <p className="font-medium">{STATUS_LABELS[job.status]}</p>
          {job.status === "processing" && (
            <p className="text-xs opacity-75">
              Esto puede tardar 30-60 segundos dependiendo de la complejidad del curso
            </p>
          )}
          {job.status === "failed" && job.errorMessage && (
            <p className="text-xs">{job.errorMessage}</p>
          )}
        </div>
        <span className="text-sm font-semibold">{progress}%</span>
      </div>

      {/* Progress bar */}
      <div className="h-2 w-full overflow-hidden rounded-full bg-gray-200">
        <div
          className="h-full rounded-full bg-blue-500 transition-all duration-500"
          style={{ width: `${progress}%` }}
        />
      </div>

      {/* Job metadata */}
      <div className="flex items-center justify-between text-xs text-gray-400">
        <span>Job ID: {job.jobId.slice(0, 8)}…</span>
        <span>Actualizado: {new Date(job.updatedAt).toLocaleTimeString()}</span>
      </div>
    </div>
  );
}
