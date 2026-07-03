import {
  BookOpen,
  Bot,
  ChevronRight,
  GraduationCap,
  LayoutDashboard,
  LogOut,
  Menu,
  Settings,
  Users,
  X
} from "lucide-react";
import { Link, NavLink, Outlet, useNavigate } from "react-router-dom";

import { DEMO_SESSIONS, useAppStore, type UserRole } from "@/store/app.store";

const NAV_BY_ROLE = {
  admin: [
    { to: "/dashboard",                label: "Resumen ejecutivo",   icon: LayoutDashboard },
    { to: "/dashboard/docente",        label: "Vista Docente",       icon: GraduationCap },
    { to: "/dashboard/estudiante",     label: "Vista Estudiante",    icon: BookOpen },
    { to: "/dashboard/ai-course-builder", label: "Generador IA",    icon: Bot },
    { to: "/dashboard/usuarios",       label: "Usuarios",            icon: Users },
    { to: "/dashboard/configuracion",  label: "Configuración",       icon: Settings },
  ],
  teacher: [
    { to: "/dashboard/docente",        label: "Mis cursos",          icon: BookOpen },
    { to: "/dashboard/ai-course-builder", label: "Crear con IA",    icon: Bot },
    { to: "/dashboard/estudiantes",    label: "Mis estudiantes",     icon: Users },
    { to: "/dashboard/configuracion",  label: "Configuración",       icon: Settings },
  ],
  student: [
    { to: "/dashboard/estudiante",     label: "Mi aprendizaje",      icon: BookOpen },
    { to: "/dashboard/configuracion",  label: "Configuración",       icon: Settings },
  ],
};

const ROLE_LABELS: Record<UserRole, string> = {
  admin: "Administrador",
  teacher: "Docente",
  student: "Estudiante",
};

const ROLE_COLORS: Record<UserRole, string> = {
  admin: "bg-violet-500",
  teacher: "bg-blue-500",
  student: "bg-emerald-500",
};

export function DashboardLayout() {
  const { sidebarOpen, setSidebarOpen, session, switchRole } = useAppStore();
  const navigate = useNavigate();

  const navItems = session ? NAV_BY_ROLE[session.role] : NAV_BY_ROLE.student;

  return (
    <div className="flex min-h-screen bg-slate-50 font-[Inter,system-ui,sans-serif]">

      {/* ── Sidebar ─────────────────────────────────────────── */}
      <aside
        className={`fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-[#0b1f3a] text-white transition-transform duration-300 ease-out lg:translate-x-0 ${
          sidebarOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        {/* Logo */}
        <div className="flex h-16 shrink-0 items-center justify-between border-b border-white/8 px-5">
          <Link to="/" className="flex items-center gap-2.5">
            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-[#1f4b99]">
              <span className="text-sm font-black text-white">T</span>
            </div>
            <div>
              <p className="text-[11px] font-bold uppercase tracking-[0.25em] text-[#d9a441]">TAU</p>
              <p className="text-[9px] text-white/45 tracking-wider">Campus Virtual</p>
            </div>
          </Link>
          <button
            onClick={() => setSidebarOpen(false)}
            className="rounded-lg p-1.5 text-white/40 hover:bg-white/8 hover:text-white lg:hidden"
          >
            <X className="h-4 w-4" />
          </button>
        </div>

        {/* Role indicator */}
        {session && (
          <div className="px-4 pt-4 pb-2">
            <div className="rounded-xl border border-white/8 bg-white/5 p-3">
              <div className="flex items-center gap-3">
                <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#1f4b99] text-sm font-bold text-white ring-2 ring-[#d9a441]/60">
                  {session.initials}
                </div>
                <div className="min-w-0 flex-1">
                  <p className="truncate text-sm font-semibold text-white">{session.name}</p>
                  <span className={`mt-0.5 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold text-white ${ROLE_COLORS[session.role]}`}>
                    {ROLE_LABELS[session.role]}
                  </span>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto py-3 px-2">
          <p className="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/30">
            Menú principal
          </p>
          {navItems.map(({ to, label, icon: Icon }) => (
            <NavLink
              key={to}
              to={to}
              end={to === "/dashboard"}
              className={({ isActive }) =>
                `group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 ${
                  isActive
                    ? "bg-[#1f4b99] text-white shadow-[0_2px_12px_rgba(31,75,153,0.45)] border-l-[3px] border-[#d9a441] pl-[calc(0.75rem-3px)]"
                    : "text-white/65 hover:bg-white/8 hover:text-white"
                }`
              }
            >
              <Icon className="h-4 w-4 shrink-0" />
              <span className="flex-1">{label}</span>
              <ChevronRight className="h-3 w-3 opacity-0 transition group-hover:opacity-40" />
            </NavLink>
          ))}

          {/* Demo role switcher */}
          <div className="mt-6">
            <p className="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/30">
              Demo — cambiar rol
            </p>
            {(Object.keys(DEMO_SESSIONS) as UserRole[]).map((role) => (
              <button
                key={role}
                onClick={() => {
                  switchRole(role);
                  const firstRoute = NAV_BY_ROLE[role][0].to;
                  navigate(firstRoute);
                }}
                className={`flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm transition-all ${
                  session?.role === role
                    ? "bg-white/10 text-white"
                    : "text-white/40 hover:bg-white/5 hover:text-white/70"
                }`}
              >
                <div className={`h-2 w-2 rounded-full ${ROLE_COLORS[role]}`} />
                {ROLE_LABELS[role]}
              </button>
            ))}
          </div>
        </nav>

        {/* Bottom: Moodle link + logout */}
        <div className="shrink-0 border-t border-white/8 p-3 space-y-1">
          <a
            href="http://localhost:8080"
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/55 transition hover:bg-white/8 hover:text-white"
          >
            <GraduationCap className="h-4 w-4" />
            Ir a Moodle LMS
          </a>
          <button className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/40 transition hover:bg-red-900/30 hover:text-red-300">
            <LogOut className="h-4 w-4" />
            Cerrar sesión
          </button>
        </div>
      </aside>

      {/* Overlay (mobile) */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/50 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* ── Main ─────────────────────────────────────────────── */}
      <div className="flex flex-1 flex-col lg:pl-64">

        {/* Topbar */}
        <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-5 shadow-sm backdrop-blur">
          <div className="flex items-center gap-4">
            <button
              onClick={() => setSidebarOpen(!sidebarOpen)}
              className="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition lg:hidden"
            >
              <Menu className="h-5 w-5" />
            </button>
            <div>
              <p className="text-[10px] font-semibold uppercase tracking-[0.25em] text-blue-600">
                E-TAU Campus Virtual
              </p>
              <p className="text-xs text-slate-400">Panel institucional</p>
            </div>
          </div>

          <div className="flex items-center gap-3">
            <a
              href="http://localhost:8080"
              target="_blank"
              rel="noopener noreferrer"
              className="hidden items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:border-slate-300 hover:bg-white sm:flex"
            >
              <GraduationCap className="h-3.5 w-3.5" />
              Moodle LMS
            </a>
            {session && (
              <div className="flex h-8 w-8 items-center justify-center rounded-full bg-[#1f4b99] text-xs font-bold text-white ring-2 ring-[#d9a441]/50">
                {session.initials}
              </div>
            )}
          </div>
        </header>

        {/* Page content */}
        <main className="flex-1 overflow-auto p-5 md:p-7">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
