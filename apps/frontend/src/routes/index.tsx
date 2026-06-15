import { createBrowserRouter } from "react-router-dom";

import { DashboardLayout } from "@/layouts/dashboard-layout";
import { AdminDashboardPage } from "@/pages/admin-dashboard-page";
import { AiCourseBuilderPage } from "@/pages/ai-course-builder-page";
import { DashboardPage } from "@/pages/dashboard-page";
import { HomePage } from "@/pages/home-page";
import { LoginPage } from "@/pages/login-page";
import { StudentDashboardPage } from "@/pages/student-dashboard-page";
import { TeacherDashboardPage } from "@/pages/teacher-dashboard-page";

export const router = createBrowserRouter([
  {
    path: "/",
    element: <HomePage />
  },
  {
    path: "/login",
    element: <LoginPage />
  },
  {
    path: "/dashboard",
    element: <DashboardLayout />,
    children: [
      {
        index: true,
        element: <DashboardPage />
      },
      {
        path: "docente",
        element: <TeacherDashboardPage />
      },
      {
        path: "estudiante",
        element: <StudentDashboardPage />
      },
      {
        path: "ai-course-builder",
        element: <AiCourseBuilderPage />
      },
      {
        path: "admin",
        element: <AdminDashboardPage />
      }
    ]
  }
]);

