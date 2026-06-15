import { useCallback } from "react";

import { courseBuilderApi } from "../services/course-builder.api";
import { useCourseBuilderStore } from "../store/course-builder.store";
import type { GenerateCourseRequest } from "../types/course-builder.types";
import { useGenerationStatus } from "./useGenerationStatus";

export function useCourseBuilder() {
  const store = useCourseBuilderStore();
  const { startPolling, stopPolling } = useGenerationStatus();

  const generate = useCallback(async () => {
    if (!store.form.prompt.trim()) return;

    store.setError(null);
    store.setGenerating(true);

    try {
      const request: GenerateCourseRequest = {
        prompt: store.form.prompt,
        language: store.form.language,
        level: store.form.level,
        moodleCategoryId: store.form.moodleCategoryId,
        options: store.form.options
      };

      const created = await courseBuilderApi.generate(request);
      store.setCurrentJobId(created.jobId);
      startPolling(created.jobId);
    } catch (err) {
      const msg = err instanceof Error ? err.message : "Error al iniciar la generación";
      store.setError(msg);
      store.setGenerating(false);
    }
  }, [store, startPolling]);

  const deployToMoodle = useCallback(async () => {
    if (!store.currentJobId || !store.currentJob) return;

    store.setDeploying(true);
    store.setError(null);

    try {
      const request: GenerateCourseRequest = {
        prompt: store.form.prompt,
        language: store.form.language,
        level: store.form.level,
        moodleCategoryId: store.form.moodleCategoryId
      };
      const result = await courseBuilderApi.deployToMoodle(store.currentJobId, request);
      store.setCurrentJob(result);
    } catch (err) {
      const msg = err instanceof Error ? err.message : "Error al desplegar en Moodle";
      store.setError(msg);
    } finally {
      store.setDeploying(false);
    }
  }, [store]);

  const loadTemplates = useCallback(async () => {
    try {
      const { templates } = await courseBuilderApi.getTemplates();
      store.setTemplates(templates);
    } catch {
      // templates are optional — fail silently
    }
  }, [store]);

  const cancel = useCallback(() => {
    stopPolling();
    store.setGenerating(false);
  }, [store, stopPolling]);

  return {
    form: store.form,
    currentJob: store.currentJob,
    isGenerating: store.isGenerating,
    isDeploying: store.isDeploying,
    error: store.error,
    templates: store.templates,
    blueprint: store.getBlueprint(),
    jobStatus: store.getJobStatus(),
    setPrompt: store.setPrompt,
    setLanguage: store.setLanguage,
    setLevel: store.setLevel,
    setOptions: store.setOptions,
    applyTemplate: store.applyTemplate,
    generate,
    deployToMoodle,
    loadTemplates,
    cancel,
    reset: store.reset
  };
}
