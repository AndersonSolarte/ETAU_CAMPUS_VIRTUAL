import { createBrowserRouter } from "react-router-dom";

import { DashboardLayout } from "@/layouts/dashboard-layout";
import { DashboardPage } from "@/pages/dashboard-page";
import { HomePage } from "@/pages/home-page";
import { LoginPage } from "@/pages/login-page";

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
      }
    ]
  }
]);

