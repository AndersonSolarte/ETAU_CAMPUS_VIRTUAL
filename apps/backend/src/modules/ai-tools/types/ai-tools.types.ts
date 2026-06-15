// ─── Assign AI ────────────────────────────────────────────────────────────────

export interface AssignSubmission {
  submissionId: number;
  userId: number;
  studentName: string;
  text: string;
}

export interface GradeSuggestion {
  submissionId: number;
  userId: number;
  studentName: string;
  assignmentId: number;
  grade: number;
  maxGrade: number;
  feedback: string;
  confidence: number;
}

export interface GradeAssignmentRequest {
  assignmentId: number;
  rubric?: string;
  maxGrade?: number;
  submissions: AssignSubmission[];
}

export interface GradeAssignmentResponse {
  suggestions: GradeSuggestion[];
  tokensUsed: number;
}

// ─── Tutor AI ─────────────────────────────────────────────────────────────────

export interface ChatMessage {
  role: "user" | "assistant";
  content: string;
}

export interface TutorChatRequest {
  courseId: number;
  courseName: string;
  message: string;
  history?: ChatMessage[];
}

export interface TutorChatResponse {
  reply: string;
  tokensUsed: number;
}

// ─── Forum AI ─────────────────────────────────────────────────────────────────

export type ForumVerdict = "approved" | "flagged" | "rejected";

export interface ModeratePostRequest {
  postId: number;
  courseId: number;
  subject: string;
  text: string;
}

export interface ModeratePostResponse {
  verdict: ForumVerdict;
  reason: string;
  suggestedReply?: string;
  tokensUsed: number;
}

export interface GenerateDebateRequest {
  courseId: number;
  forumId: number;
  topic: string;
}

export interface GenerateDebateResponse {
  discussion: {
    title: string;
    body: string;
    suggestedReplies: string[];
  };
  tokensUsed: number;
}

// ─── Student Life Story ───────────────────────────────────────────────────────

export interface StudentDataInput {
  userId: number;
  fullName: string;
  coursesEnrolled: number;
  activitiesCompleted: number;
  avgGrade: number | null;
  forumPosts: number;
  lastAccess: number | null;
}

export interface StudentProfileResponse {
  summary: string;
  strengths: string[];
  areasImprovement: string[];
  dropoutRisk: "low" | "medium" | "high";
  engagementScore: number;
  recommendations: string[];
  tokensUsed: number;
}

// ─── Smart Rules AI ───────────────────────────────────────────────────────────

export interface StudentRuleInput {
  userId: number;
  courseId: number;
}

export interface EvaluateRulesRequest {
  ruleId: number;
  trigger: string;
  condition: string;
  students: StudentRuleInput[];
}

export interface EvaluateRulesResponse {
  triggered: { userId: number; reason: string }[];
  tokensUsed: number;
}

// ─── Ranking Activities AI ────────────────────────────────────────────────────

export interface ActivityDataInput {
  cmid: number;
  name: string;
  type: string;
  completions: number;
  totalAttempts: number;
}

export interface AnalyzeRankingRequest {
  courseId: number;
  activities: ActivityDataInput[];
}

export interface RankedActivity {
  cmid: number;
  name: string;
  type: string;
  effectivenessScore: number;
  completionRate: number;
  insight: string;
  recommendation: string;
}

export interface AnalyzeRankingResponse {
  ranking: RankedActivity[];
  tokensUsed: number;
}

// ─── Share Certificate AI ─────────────────────────────────────────────────────

export interface CertificateDescriptionRequest {
  certName: string;
  orgName: string;
  courseId?: number;
}

export interface CertificateDescriptionResponse {
  description: string;
  tokensUsed: number;
}

// ─── Recommended Courses ──────────────────────────────────────────────────────

export interface AvailableCourse {
  id: number;
  name: string;
  summary: string;
  category: number;
}

export interface RecommendCoursesRequest {
  userId: number;
  enrolledCourseIds: number[];
  availableCourses: AvailableCourse[];
}

export interface CourseRecommendation {
  courseId: number;
  courseName: string;
  reason: string;
  matchScore: number;
}

export interface RecommendCoursesResponse {
  recommendations: CourseRecommendation[];
  tokensUsed: number;
}
