import { motion } from "framer-motion";
import {
  BookOpen,
  Bot,
  CheckSquare,
  Clock,
  MessageSquare,
  PlusCircle,
  Users,
} from "lucide-react";
import { Link } from "react-router-dom";

const courses = [
  {
    title: "Python para Principiantes",
    code: "PYTHON101",
    students: 38,
    progress: 62,
    pending: 4,
    color: "from-blue-700 to-blue-500",
  },
  {
    title: "Inteligencia Artificial Práctica",
    code: "AI401",
    students: 24,
    progress: 45,
    pending: 2,
    color: "from-violet-700 to-violet-500",
  },
];

const quickActions = [
  { label: "Nueva tarea",      icon: CheckSquare,  color: "border-blue-200 text-blue-700 hover:bg-blue-50" },
  { label: "Crear quiz",       icon: BookOpen,     color: "border-violet-200 text-violet-700 hover:bg-violet-50" },
  { label: "Nuevo foro",       icon: MessageSquare,color: "border-amber-200 text-amber-700 hover:bg-amber-50" },
  { label: "Generar curso IA", icon: Bot,          color: "border-emerald-200 text-emerald-700 hover:bg-emerald-50", to: "/dashboard/ai-course-builder" },
];

const pendingItems = [
  { type: "Tarea",  course: "PYTHON101",  item: "Tarea S3: Bucles y funciones",   due: "Vence hoy",      urgent: true },
  { type: "Quiz",   course: "AI401",      item: "Quiz semana 5 — ML básico",       due: "Vence mañana",   urgent: true },
  { type: "Foro",   course: "PYTHON101",  item: "Debate: POO vs funcional",        due: "3 días",         urgent: false },
  { type: "Nota",   course: "AI401",      item: "Calificar proyecto final (6)",     due: "5 días",         urgent: false },
];

const topStudents = [
  { name: "Valentina Cruz",  score: 98, avatar: "VC" },
  { name: "Santiago León",   score: 95, avatar: "SL" },
  { name: "Paula Jiménez",   score: 93, avatar: "PJ" },
  { name: "Felipe Castro",   score: 91, avatar: "FC" },
];

const fade = { hidden: { opacity: 0, y: 14 }, show: { opacity: 1, y: 0 } };

export function TeacherDashboardPage() {
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
          <p className="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Panel Docente</p>
          <h1 className="mt-1 text-2xl font-bold text-slate-900">Bienvenido, Carlos</h1>
          <p className="mt-1 text-sm text-slate-500">Tienes {pendingItems.filter(i => i.urgent).length} tareas urgentes hoy</p>
        </div>
        <Link
          to="/dashboard/ai-course-builder"
          className="flex items-center gap-2 self-start rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:bg-blue-700 sm:self-auto"
        >
          <Bot className="h-4 w-4" />
          Crear curso con IA
        </Link>
      </motion.div>

      {/* Stats */}
      <motion.div variants={fade} className="grid grid-cols-3 gap-4">
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-blue-600">2</p>
          <p className="mt-0.5 text-xs text-slate-500">Cursos activos</p>
        </div>
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-violet-600">62</p>
          <p className="mt-0.5 text-xs text-slate-500">Estudiantes totales</p>
        </div>
        <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
          <p className="text-2xl font-bold text-amber-600">6</p>
          <p className="mt-0.5 text-xs text-slate-500">Pendientes</p>
        </div>
      </motion.div>

      {/* My courses */}
      <motion.div variants={fade}>
        <h2 className="mb-3 text-sm font-bold text-slate-700">Mis cursos</h2>
        <div className="grid gap-4 sm:grid-cols-2">
          {courses.map((c) => (
            <a
              key={c.code}
              href="http://localhost:8080"
              target="_blank"
              rel="noopener noreferrer"
              className="group rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden transition hover:shadow-md hover:-translate-y-1"
            >
              <div className={`h-24 bg-gradient-to-br ${c.color} p-4 flex items-end`}>
                <span className="rounded-lg bg-white/15 px-2 py-1 text-[11px] font-bold text-white backdrop-blur-sm">{c.code}</span>
              </div>
              <div className="p-4">
                <h3 className="font-semibold text-slate-800 group-hover:text-blue-600 transition">{c.title}</h3>
                <div className="mt-3 flex items-center justify-between text-xs text-slate-500">
                  <span className="flex items-center gap-1"><Users className="h-3.5 w-3.5" />{c.students} estudiantes</span>
                  <span className="flex items-center gap-1 text-amber-600 font-semibold"><Clock className="h-3.5 w-3.5" />{c.pending} pendientes</span>
                </div>
                <div className="mt-3">
                  <div className="flex justify-between text-[11px] text-slate-400 mb-1">
                    <span>Avance del curso</span>
                    <span className="font-semibold text-slate-600">{c.progress}%</span>
                  </div>
                  <div className="h-1.5 w-full rounded-full bg-slate-100">
                    <div className="h-1.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-400 transition-all" style={{ width: `${c.progress}%` }} />
                  </div>
                </div>
              </div>
            </a>
          ))}

          {/* Add course card */}
          <Link
            to="/dashboard/ai-course-builder"
            className="flex flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-slate-400 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
          >
            <PlusCircle className="h-8 w-8" />
            <p className="text-sm font-medium">Crear nuevo curso</p>
            <p className="text-xs text-slate-400">Con IA o manualmente</p>
          </Link>
        </div>
      </motion.div>

      {/* Bottom row */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {/* Quick actions */}
        <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 className="mb-4 text-sm font-bold text-slate-700">Acciones rápidas</h2>
          <div className="grid grid-cols-2 gap-3">
            {quickActions.map(({ label, icon: Icon, color, to }) =>
              to ? (
                <Link key={label} to={to} className={`flex flex-col items-center gap-2 rounded-xl border p-4 text-xs font-semibold transition ${color}`}>
                  <Icon className="h-5 w-5" />
                  {label}
                </Link>
              ) : (
                <button key={label} className={`flex flex-col items-center gap-2 rounded-xl border p-4 text-xs font-semibold transition ${color}`}>
                  <Icon className="h-5 w-5" />
                  {label}
                </button>
              )
            )}
          </div>
        </motion.div>

        {/* Top students */}
        <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 className="mb-4 text-sm font-bold text-slate-700">Mejores estudiantes</h2>
          <div className="space-y-3">
            {topStudents.map(({ name, score, avatar }, i) => (
              <div key={name} className="flex items-center gap-3">
                <span className="w-5 text-center text-xs font-bold text-slate-400">#{i + 1}</span>
                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">{avatar}</div>
                <span className="flex-1 text-sm text-slate-700">{name}</span>
                <span className="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">{score}%</span>
              </div>
            ))}
          </div>
        </motion.div>
      </div>

      {/* Pending items */}
      <motion.div variants={fade} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 className="mb-4 text-sm font-bold text-slate-700">Pendientes por revisar</h2>
        <div className="divide-y divide-slate-100">
          {pendingItems.map(({ type, course, item, due, urgent }) => (
            <div key={item} className="flex items-center justify-between py-3">
              <div className="flex items-center gap-3">
                <span className={`rounded-full px-2.5 py-0.5 text-[11px] font-semibold ${urgent ? "bg-red-50 text-red-600" : "bg-slate-100 text-slate-500"}`}>
                  {type}
                </span>
                <div>
                  <p className="text-sm font-medium text-slate-800">{item}</p>
                  <p className="text-xs text-slate-400">{course}</p>
                </div>
              </div>
              <span className={`text-xs font-semibold ${urgent ? "text-red-500" : "text-slate-400"}`}>{due}</span>
            </div>
          ))}
        </div>
      </motion.div>
    </motion.div>
  );
}
