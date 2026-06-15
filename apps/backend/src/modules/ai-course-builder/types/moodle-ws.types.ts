// Moodle Web Services API type definitions
// Reference: https://docs.moodle.org/dev/Web_services_API

export interface MoodleWsResponse<T = unknown> {
  data?: T;
  exception?: string;
  errorcode?: string;
  message?: string;
}

// ─── core_course_create_courses ──────────────────────────────────────────────

export interface MoodleCreateCourseParams {
  fullname: string;
  shortname: string;
  categoryid: number;
  summary?: string;
  summaryformat?: 0 | 1 | 2 | 4; // 0=moodle, 1=html, 2=plain, 4=markdown
  format?: "weeks" | "topics" | "social" | "site";
  showgrades?: 0 | 1;
  newsitems?: number;
  startdate?: number; // unix timestamp
  enddate?: number;
  numsections?: number;
  maxbytes?: number;
  showreports?: 0 | 1;
  visible?: 0 | 1;
  lang?: string;
  enablecompletion?: 0 | 1;
}

export interface MoodleCreatedCourse {
  id: number;
  shortname: string;
}

// ─── core_course_update_courses ──────────────────────────────────────────────

export interface MoodleUpdateCourseParams extends Partial<MoodleCreateCourseParams> {
  id: number;
}

// ─── core_course_create_sections ─────────────────────────────────────────────

export interface MoodleCreateSectionParams {
  courseid: number;
  position?: number;
  number?: number;
}

export interface MoodleSection {
  id: number;
  course: number;
  section: number;
  name: string;
  summary: string;
  visible: number;
}

// ─── mod_forum_add_discussion ─────────────────────────────────────────────────

export interface MoodleForumAddDiscussionParams {
  forumid: number;
  subject: string;
  message: string;
  messageformat?: number;
}

// ─── core_course_get_courses ──────────────────────────────────────────────────

export interface MoodleCourse {
  id: number;
  shortname: string;
  fullname: string;
  displayname: string;
  categoryid: number;
  summary: string;
  visible: number;
  format: string;
  startdate: number;
  enddate: number;
}

// ─── mod_assign, mod_quiz, mod_forum creation ────────────────────────────────

export interface MoodleModuleCreateParams {
  courseid: number;
  sectionnum: number;
  modulename: string;
  name: string;
  intro?: string;
  introformat?: number;
  visible?: number;
}

// ─── Moodle WS function names ─────────────────────────────────────────────────

export const MOODLE_WS_FUNCTIONS = {
  CREATE_COURSES: "core_course_create_courses",
  UPDATE_COURSES: "core_course_update_courses",
  GET_COURSES: "core_course_get_courses",
  GET_CATEGORIES: "core_course_get_categories",
  CREATE_SECTIONS: "core_course_create_sections",
  UPDATE_SECTIONS: "core_course_update_sections",
  GET_CONTENTS: "core_course_get_contents",
  ADD_MODULE: "core_course_add_cm",
  CREATE_FORUM: "mod_forum_add_discussion",
  GET_SITE_INFO: "core_webservice_get_site_info"
} as const;

export type MoodleWsFunction = typeof MOODLE_WS_FUNCTIONS[keyof typeof MOODLE_WS_FUNCTIONS];
