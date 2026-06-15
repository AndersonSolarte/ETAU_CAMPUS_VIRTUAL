import { motion } from "framer-motion";
import {
  Activity,
  BookOpen,
  Bot,
  GraduationCap,
  Server,
  TrendingUp,
  Users,
  Zap,
} from "lucide-react";
import { Link } from "react-router-dom";

const kpis = [
  { label: "Estudiantes activos",  value: "4,218",  delta: "+142",   up: true,  icon: Users,          color: "text-blue-600",    bg: "bg-blue-50" },
  { label: "Cursos publicados",    value: "186",     delta: "+6",     up: true,  icon: BookOpen,       color: "text-violet-600",  bg: "bg-violet-50" },
  { label: "Docentes",             value: "95",      delta: "+3",     up: true,  icon: GraduationCap,  color: "text-emerald-600", bg: "bg-emerald-50" },
  { label: "Matrículas activas",   value: "9.340",   delta: "+289",   up: true,  icon: TrendingUp,     color: "text-amber-600",   bg: "bg-amber-50" },
];

const quickActions = [
  { label: "Crear curso con IA",       to: "/dashboard/ai-course-builder", icon: Bot,           color: "bg-blue-600 hover:bg-blue-700" },
  { label: "Gestionar usuarios",       to: "/dashboard/usuarios",          icon: Users,         color: "bg-violet-600 hover:bg-violet-700" },
  { label: "Ver Moodle LMS",           to: "http://localhost:8080",        icon: GraduationCap, color: "bg-emerald-600 hover:bg-emerald-700", external: true },
  { label: "Estado del sistema",       to: "/dashboard/sistema",           icon: Server,        color: "bg-slate-600 hover:bg-slate-700" },
];

const recentActivity = [
  { text: "Sofía Méndez completó Python 101 — Semana 3",       time: "hace 5 min",  dot: "bg-emerald-400" },
  { text: "Carlos Rodríguez creó una nueva tarea en DATASCI201", time: "hace 18 min", dot: "bg-blue-400" },
  { text: "12 nuevas matrículas en Gestión con Ágiles",         time: "hace 34 min", dot: "bg-violet-400" },
  { text: "Sistema IA generó curso: Cálculo Vectorial",         time: "hace 1 h",    dot: "bg-amber-400" },
  { text: "Andrés García publicó foro de debate — COM201",      time: "hace 2 h",    dot: "bg-blue-400" },
  { text: "Miguel Vargas obtuvo 98% en Quiz Python 101",        time: "hace 3 h",    dot: "bg-emerald-400" },
];

const systemHealth = [
  { label: "Moodle",     status: "Operativo",  ok: true },
  { label: "PostgreSQL", status: "Operativo",  ok: true },
  { label: "Redis",      status: "Operativo",  ok: true },
  { label: "Backend",    status: "Operativo",  ok: true },
];

const fade = { hidden: { opacity: 0, y: 16 }, show: { opacity: 1, y: 0 } };

export function AdminDashboardPage() {
  return (
    <motion.div
      initial="hidden"
      animate="show"
      variants={{ show: { transition: { staggerChildren: 0.07 } } }}
      className="space-y-6"
    >
      {/* Header */}
      <motion.div variants={fade} className="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p className="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Panel Ejecutivo</p>
          <h1 className="mt-1 text-2xl font-bold text-slate-900">Centro de operación institucional</h1>
          <p className="mt-1 text-sm text-slate-500">TAU Campus Virtual · {new Date().toLocaleDateString("es-CO", { weekday: "long", year: "numeric", month: "long", day: "numeric" })}</p>
        </div>
        <Link
          to="/dashboard/ai-course-builder"
          className="flex items-center gap-2 self-start rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-blue-700 hover:shadow-blue-200 sm:self-auto"
        >
          <Bot className="h-4 w-4" />
          Crear curso con IA
        </Link>
      </motion.div>

      {/* KPIs */}
      <motion.div variants={fade} className="grid grid-cols-2 gap-4 xl:grid-cols-4">
        {kpis.map(({ label, value, delta, up, icon: Icon, color, bg }) => (
          <div key={label} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
            <div className={`mb-3 inline-flex rounded-xl p-2.5 ${bg}`}>
              <Icon className={`h-5 w-5 ${color}`} />
            </div>
            <p className="text-2xl font-bold text-slate-900">{value}</p>
            <p className="mt-0.5 text-sm text-slate-500">{label}</p>
            <span className={`mt-2 inline-flex items-center gap-1 text-xs font-semibold ${up ? "text-emerald-600" : "text-red-500"}`}>
              <TrendingUp className="h-3 w-3" />
              {delta} este mes
            </span>
          </div>
        ))}
      </motion.div>

      {/* Middle row */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {/* Quick actions */}
        <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 className="mb-4 flex items-center gap-2 text-sm font-bold text-slate-700">
            <Zap className="h-4 w-4 text-amber-500" />
            Accesos rápidos
          </h2>
          <div className="space-y-2">
            {quickActions.map(({ label, to, icon: Icon, color, external }) =>
              external ? (
                <a
                  key={label}
                  href={to}
                  target="_blank"
                  rel="noopener noreferrer"
                  className={`flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-white transition ${color}`}
                >
                  <Icon className="h-4 w-4" />
                  {label}
                </a>
              ) : (
                <Link
                  key={label}
                  to={to}
                  className={`flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-white transition ${color}`}
                >
                  <Icon className="h-4 w-4" />
                  {label}
                </Link>
              )
            )}
          </div>
        </motion.div>

        {/* Activity feed */}
        <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
          <h2 className="mb-4 flex items-center gap-2 text-sm font-bold text-slate-700">
            <Activity className="h-4 w-4 text-blue-500" />
            Actividad reciente
          </h2>
          <div className="space-y-3">
            {recentActivity.map(({ text, time, dot }) => (
              <div key={text} className="flex items-start gap-3">
                <span className={`mt-1.5 h-2 w-2 shrink-0 rounded-full ${dot}`} />
                <div className="flex-1">
                  <p className="text-sm text-slate-700">{text}</p>
                  <p className="text-xs text-slate-400">{time}</p>
                </div>
              </div>
            ))}
          </div>
        </motion.div>
      </div>

      {/* System health */}
      <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="mb-4 flex items-center gap-2 text-sm font-bold text-slate-700">
          <Server className="h-4 w-4 text-slate-500" />
          Estado del sistema
        </h2>
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
          {systemHealth.map(({ label, status, ok }) => (
            <div key={label} className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
              <span className="text-sm font-medium text-slate-700">{label}</span>
              <span className={`flex items-center gap-1.5 text-xs font-semibold ${ok ? "text-emerald-600" : "text-red-500"}`}>
                <span className={`h-1.5 w-1.5 rounded-full ${ok ? "bg-emerald-500 animate-pulse" : "bg-red-500"}`} />
                {status}
              </span>
            </div>
          ))}
        </div>
      </motion.div>
    </motion.div>
  );
}
