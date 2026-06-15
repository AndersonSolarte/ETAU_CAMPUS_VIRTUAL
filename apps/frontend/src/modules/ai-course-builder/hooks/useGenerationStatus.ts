import { useCallback, useRef } from "react";

import { courseBuilderApi } from "../services/course-builder.api";
import { useCourseBuilderStore } from "../store/course-builder.store";

const POLL_INTERVAL_MS = 2500;
const MAX_POLLS = 120; // ~5 minutes

export function useGenerationStatus() {
  const store = useCourseBuilderStore();
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const pollCountRef = useRef(0);

  const stopPolling = useCallback(() => {
    if (timerRef.current) {
      clearInterval(timerRef.current);
      timerRef.current = null;
    }
    pollCountRef.current = 0;
  }, []);

  const startPolling = useCallback(
    (jobId: string) => {
      stopPolling();

      timerRef.current = setInterval(async () => {
        pollCountRef.current += 1;

        if (pollCountRef.current >= MAX_POLLS) {
          stopPolling();
          store.setError("La generación tardó demasiado. Por favor intenta de nuevo.");
          store.setGenerating(false);
          return;
        }

        try {
          const job = await courseBuilderApi.getJob(jobId);
          store.setCurrentJob(job);

          if (job.status === "completed" || job.status === "failed") {
            stopPolling();
            store.setGenerating(false);

            if (job.status === "failed") {
              store.setError(job.errorMessage ?? "La generación falló");
            }
          }
        } catch (err) {
          const msg = err instanceof Error ? err.message : "Error consultando estado del job";
          store.setError(msg);
          stopPolling();
          store.setGenerating(false);
        }
      }, POLL_INTERVAL_MS);
    },
    [store, stopPolling]
  );

  return { startPolling, stopPolling };
}
