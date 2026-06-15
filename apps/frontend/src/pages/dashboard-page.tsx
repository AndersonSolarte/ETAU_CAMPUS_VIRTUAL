import { useEffect } from "react";
import { useNavigate } from "react-router-dom";

import { useAppStore } from "@/store/app.store";
import { AdminDashboardPage } from "./admin-dashboard-page";

const ROLE_ROUTE = {
  admin: null,        // renders AdminDashboardPage inline (index route)
  teacher: "/dashboard/docente",
  student: "/dashboard/estudiante",
} as const;

export function DashboardPage() {
  const session = useAppStore((s) => s.session);
  const navigate = useNavigate();

  useEffect(() => {
    if (!session) return;
    const redirect = ROLE_ROUTE[session.role];
    if (redirect) navigate(redirect, { replace: true });
  }, [session, navigate]);

  // Admin stays on /dashboard index; others are redirected above
  if (!session || session.role !== "admin") return null;

  return <AdminDashboardPage />;
}
