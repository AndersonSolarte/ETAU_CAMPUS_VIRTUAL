import { motion } from "framer-motion";
import {
  BookOpen,
  Calendar,
  CheckCircle2,
  Clock,
  Flame,
  Star,
  Trophy,
  TrendingUp,
} from "lucide-react";

const courses = [
  {
    title: "Python para Principiantes",
    code: "PYTHON101",
    teacher: "Carlos Rodríguez",
    progress: 62,
    nextActivity: "Quiz: Semana 7 — Programación OO",
    due: "Mañana",
    color: "from-blue-700 to-blue-500",
    grade: "4.2",
  },
  {
    title: "Gestión con Metodologías Ágiles",
    code: "AGILE301",
    teacher: "Andrés García",
    progress: 80,
    nextActivity: "Foro: Retrospectiva sprint",
    due: "3 días",
    color: "from-violet-700 to-violet-500",
    grade: "4.5",
  },
  {
    title: "Comunicación Efectiva",
    code: "COM201",
    teacher: "Andrés García",
    progress: 35,
    nextActivity: "Entrega: Ensayo argumentativo",
    due: "5 días",
    color: "from-emerald-700 to-emerald-500",
    grade: "3.9",
  },
];

const upcoming = [
  { title: "Quiz: Semana 7 — OO",            course: "PYTHON101",  due: "Mañana 23:59",  type: "Quiz",     urgent: true },
  { title: "Participación foro Retrospectiva", course: "AGILE301",  due: "Miér. 23:59",   type: "Foro",     urgent: false },
  { title: "Ensayo argumentativo",            course: "COM201",     due: "Vie. 23:59",    type: "Tarea",    urgent: false },
];

const achievements = [
  { icon: Flame,       label: "7 días seguidos",  color: "text-orange-500",  bg: "bg-orange-50" },
  { icon: Star,        label: "Top 5 del curso",  color: "text-yellow-500",  bg: "bg-yellow-50" },
  { icon: Trophy,      label: "100% en Quiz S5",  color: "text-violet-500",  bg: "bg-violet-50" },
  { icon: CheckCircle2,label: "3 cursos activos", color: "text-emerald-500", bg: "bg-emerald-50" },
];

const fade = { hidden: { opacity: 0, y: 14 }, show: { opacity: 1, y: 0 } };

export function StudentDashboardPage() {
  const overallProgress = Math.round(courses.reduce((s, c) => s + c.progress, 0) / courses.length);

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
          <p className="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Mi aprendizaje</p>
          <h1 className="mt-1 text-2xl font-bold text-slate-900">Hola, Sofía 👋</h1>
          <p className="mt-1 text-sm text-slate-500">Sigues avanzando — {overallProgress}% de progreso general este semestre</p>
        </div>
        <a
          href="http://localhost:8080"
          target="_blank"
          rel="noopener noreferrer"
          className="flex items-center gap-2 self-start rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-blue-700 sm:self-auto"
        >
          <BookOpen className="h-4 w-4" />
          Ir a Moodle
        </a>
      </motion.div>

      {/* Progress overview */}
      <motion.div variants={fade} className="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-blue-600">{courses.length}</p>
          <p className="mt-0.5 text-xs text-slate-500">Cursos activos</p>
        </div>
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-amber-600">{upcoming.length}</p>
          <p className="mt-0.5 text-xs text-slate-500">Entregables</p>
        </div>
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-emerald-600">{overallProgress}%</p>
          <p className="mt-0.5 text-xs text-slate-500">Progreso global</p>
        </div>
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-violet-600">4.2</p>
          <p className="mt-0.5 text-xs text-slate-500">Promedio general</p>
        </div>
      </motion.div>

      {/* Achievements */}
      <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="mb-4 flex items-center gap-2 text-sm font-bold text-slate-700">
          <Trophy className="h-4 w-4 text-yellow-500" />
          Logros recientes
        </h2>
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
          {achievements.map(({ icon: Icon, label, color, bg }) => (
            <div key={label} className={`flex items-center gap-3 rounded-xl border border-transparent p-3 ${bg}`}>
              <Icon className={`h-5 w-5 shrink-0 ${color}`} />
              <span className="text-xs font-semibold text-slate-700">{label}</span>
            </div>
          ))}
        </div>
      </motion.div>

      {/* My courses */}
      <motion.div variants={fade}>
        <h2 className="mb-3 text-sm font-bold text-slate-700">Mis cursos</h2>
        <div className="space-y-4">
          {courses.map((c) => (
            <a
              key={c.code}
              href="http://localhost:8080"
              target="_blank"
              rel="noopener noreferrer"
              className="group flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md hover:-translate-y-0.5 sm:flex-row sm:items-center"
            >
              {/* Color strip */}
              <div className={`h-12 w-full shrink-0 rounded-xl bg-gradient-to-br sm:h-full sm:w-16 ${c.color} flex items-center justify-center`}>
                <BookOpen className="h-5 w-5 text-white/80" />
              </div>

              <div className="flex-1 min-w-0">
                <div className="flex items-start justify-between gap-2">
                  <div>
                    <p className="font-semibold text-slate-800 group-hover:text-blue-600 transition">{c.title}</p>
                    <p className="text-xs text-slate-400 mt-0.5">{c.teacher} · {c.code}</p>
                  </div>
                  <span className="shrink-0 rounded-lg bg-slate-100 px-2.5 py-1 text-sm font-bold text-slate-700">{c.grade}</span>
                </div>

                {/* Progress */}
                <div className="mt-3">
                  <div className="flex justify-between text-[11px] text-slate-400 mb-1.5">
                    <span className="flex items-center gap-1"><TrendingUp className="h-3 w-3" />Progreso</span>
                    <span className="font-semibold text-slate-600">{c.progress}%</span>
                  </div>
                  <div className="h-2 w-full rounded-full bg-slate-100">
                    <div className="h-2 rounded-full bg-gradient-to-r from-blue-500 to-blue-400 transition-all" style={{ width: `${c.progress}%` }} />
                  </div>
                </div>

                {/* Next activity */}
                <div className="mt-3 flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2">
                  <Clock className="h-3.5 w-3.5 shrink-0 text-amber-500" />
                  <span className="text-xs text-slate-600">{c.nextActivity}</span>
                  <span className="ml-auto text-xs font-semibold text-amber-600">{c.due}</span>
                </div>
              </div>
            </a>
          ))}
        </div>
      </motion.div>

      {/* Upcoming deadlines */}
      <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="mb-4 flex items-center gap-2 text-sm font-bold text-slate-700">
          <Calendar className="h-4 w-4 text-blue-500" />
          Próximas entregas
        </h2>
        <div className="space-y-3">
          {upcoming.map(({ title, course, due, type, urgent }) => (
            <div key={title} className={`flex items-center justify-between rounded-xl border p-3 ${urgent ? "border-red-100 bg-red-50" : "border-slate-100 bg-slate-50"}`}>
              <div className="flex items-center gap-3">
                <span className={`rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${urgent ? "bg-red-100 text-red-700" : "bg-white text-slate-500 border border-slate-200"}`}>{type}</span>
                <div>
                  <p className="text-sm font-medium text-slate-800">{title}</p>
                  <p className="text-xs text-slate-400">{course}</p>
                </div>
              </div>
              <span className={`text-xs font-bold ${urgent ? "text-red-600" : "text-slate-500"}`}>{due}</span>
            </div>
          ))}
        </div>
      </motion.div>
    </motion.div>
  );
}
