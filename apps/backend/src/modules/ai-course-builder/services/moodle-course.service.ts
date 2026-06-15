import axios, { type AxiosInstance } from "axios";

import { env } from "../../../config/env.js";
import type { CourseBlueprint, GenerateCourseRequest } from "../types/course-builder.types.js";
import {
  MOODLE_WS_FUNCTIONS,
  type MoodleCreatedCourse,
  type MoodleCreateCourseParams,
  type MoodleWsResponse
} from "../types/moodle-ws.types.js";

// Handles all Moodle Web Services API calls.
// When a new Moodle function is needed, add it here — keep provider knowledge isolated.
export class MoodleCourseService {
  private readonly http: AxiosInstance;
  private readonly wstoken: string;

  constructor() {
    this.wstoken = env.MOODLE_WS_TOKEN;
    this.http = axios.create({
      baseURL: env.MOODLE_BASE_URL,
      timeout: 30_000,
      params: {
        wstoken: this.wstoken,
        moodlewsrestformat: "json"
      }
    });
  }

  // ─── Public API ─────────────────────────────────────────────────────────────

  async createCourseFromBlueprint(
    blueprint: CourseBlueprint,
    request: GenerateCourseRequest
  ): Promise<number> {
    const courseId = await this.createCourse(blueprint, request.moodleCategoryId ?? 1);
    await this.createSections(courseId, blueprint.totalWeeks, blueprint.modules.map(m => m.title));
    return courseId;
  }

  async verifyCourseExists(courseId: number): Promise<boolean> {
    try {
      const data = await this.call<{ id: number }[]>(MOODLE_WS_FUNCTIONS.GET_COURSES, {
        "options[ids][0]": courseId
      });
      return Array.isArray(data) && data.length > 0;
    } catch {
      return false;
    }
  }

  // ─── Private helpers ─────────────────────────────────────────────────────────

  private async createCourse(
    blueprint: CourseBlueprint,
    categoryId: number
  ): Promise<number> {
    const params: Record<string, unknown> = {
      "courses[0][fullname]": blueprint.title,
      "courses[0][shortname]": blueprint.shortname,
      "courses[0][categoryid]": categoryId,
      "courses[0][summary]": blueprint.summary,
      "courses[0][summaryformat]": 1,
      "courses[0][format]": "weeks",
      "courses[0][numsections]": blueprint.totalWeeks,
      "courses[0][lang]": blueprint.language ?? "es",
      "courses[0][enablecompletion]": 1,
      "courses[0][visible]": 1
    };

    const result = await this.call<MoodleCreatedCourse[]>(
      MOODLE_WS_FUNCTIONS.CREATE_COURSES,
      params
    );

    if (!result?.[0]?.id) {
      throw new Error("Moodle did not return a course ID after creation");
    }

    return result[0].id;
  }

  private async createSections(
    courseId: number,
    totalWeeks: number,
    sectionTitles: string[]
  ): Promise<void> {
    // Moodle weeks format creates sections automatically;
    // we update each section name via update_sections.
    const params: Record<string, unknown> = {};

    for (let i = 0; i < totalWeeks; i++) {
      params[`sections[${i}][type]`] = "num";
      params[`sections[${i}][value]`] = i + 1;
      params[`sections[${i}][courseid]`] = courseId;
      params[`sections[${i}][name]`] = sectionTitles[i] ?? `Semana ${i + 1}`;
      params[`sections[${i}][summaryformat]`] = 1;
    }

    await this.call(MOODLE_WS_FUNCTIONS.UPDATE_SECTIONS, params);
  }

  // Generic Moodle WS REST caller
  private async call<T = unknown>(
    wsfunction: string,
    params: Record<string, unknown> = {}
  ): Promise<T> {
    const response = await this.http.post<MoodleWsResponse<T>>(
      "/webservice/rest/server.php",
      null,
      { params: { wsfunction, ...params } }
    );

    const body = response.data as unknown;

    if (body && typeof body === "object" && "exception" in body) {
      const err = body as { exception: string; message: string; errorcode: string };
      throw new Error(`Moodle WS error [${err.errorcode}]: ${err.message}`);
    }

    return body as T;
  }
}
