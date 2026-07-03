import { motion } from "framer-motion";
import {
  ArrowRight,
  BookOpen,
  Bot,
  GraduationCap,
  LayoutDashboard,
  Users,
  Zap,
} from "lucide-react";
import { Link } from "react-router-dom";

const stats = [
  { label: "Estudiantes", value: "4.200+", icon: Users },
  { label: "Cursos activos", value: "180+", icon: BookOpen },
  { label: "Docentes", value: "95+", icon: GraduationCap },
];

const features = [
  {
    icon: LayoutDashboard,
    title: "Panel institucional",
    description:
      "Dashboards por rol con KPIs en tiempo real, gestión de cursos y seguimiento académico.",
    color: "text-blue-400",
    bg: "bg-blue-500/10",
  },
  {
    icon: Bot,
    title: "Generador de cursos con IA",
    description:
      "Crea estructuras curriculares completas en segundos usando GPT-4o y plantillas académicas.",
    color: "text-violet-400",
    bg: "bg-violet-500/10",
  },
  {
    icon: Zap,
    title: "Arquitectura enterprise",
    description:
      "React + Node.js desacoplados de Moodle. Escalable, actualizable y listo para producción.",
    color: "text-amber-400",
    bg: "bg-amber-500/10",
  },
];

const fade = { hidden: { opacity: 0, y: 20 }, show: { opacity: 1, y: 0 } };

export function HomePage() {
  return (
    <main className="relative min-h-screen overflow-hidden bg-[#0b1f3a]">
      {/* Background */}
      <div
        className="absolute inset-0 bg-cover bg-center opacity-50"
        style={{
          backgroundImage:
            "url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22%3E%3Cdefs%3E%3CradialGradient id=%22rg1%22 cx=%2225%25%22 cy=%2235%25%22 r=%2255%25%22%3E%3Cstop offset=%220%25%22 stop-color=%22%231f4b99%22 stop-opacity=%220.6%22/%3E%3Cstop offset=%22100%25%22 stop-color=%22%230b1f3a%22 stop-opacity=%220%22/%3E%3C/radialGradient%3E%3Cpattern id=%22dots%22 x=%220%22 y=%220%22 width=%2240%22 height=%2240%22 patternUnits=%22userSpaceOnUse%22%3E%3Ccircle cx=%2220%22 cy=%2220%22 r=%221.2%22 fill=%22white%22 opacity=%220.07%22/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=%221920%22 height=%221080%22 fill=%22url(%23rg1)%22/%3E%3Crect width=%221920%22 height=%221080%22 fill=%22url(%23dots)%22/%3E%3C/svg%3E')",
        }}
      />
      <div className="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-[#1f4b99] via-[#d9a441] to-[#1f4b99]" />

      <div className="relative z-10 mx-auto max-w-7xl px-6 py-10 lg:px-12">

        {/* Navbar */}
        <motion.nav
          initial={{ opacity: 0, y: -12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
          className="flex items-center justify-between"
        >
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1f4b99]">
              <span className="text-base font-black text-white">T</span>
            </div>
            <div>
              <p className="text-[11px] font-bold uppercase tracking-[0.3em] text-[#d9a441]">TAU</p>
              <p className="text-[10px] text-white/50 tracking-wider">Campus Virtual</p>
            </div>
          </div>
          <div className="flex items-center gap-3">
            <a
              href="http://localhost:8080"
              target="_blank"
              rel="noopener noreferrer"
              className="hidden items-center gap-1.5 rounded-lg border border-white/15 px-3 py-1.5 text-xs font-medium text-white/70 transition hover:border-white/30 hover:text-white sm:flex"
            >
              <GraduationCap className="h-3.5 w-3.5" />
              Moodle LMS
            </a>
            <Link
              to="/login"
              className="flex items-center gap-2 rounded-xl bg-[#d9a441] px-4 py-2 text-sm font-bold text-[#0b1f3a] transition hover:bg-[#c4912f]"
            >
              Iniciar sesión
              <ArrowRight className="h-3.5 w-3.5" />
            </Link>
          </div>
        </motion.nav>

        {/* Hero */}
        <motion.div
          initial="hidden"
          animate="show"
          variants={{ show: { transition: { staggerChildren: 0.1 } } }}
          className="mt-20 text-center lg:mt-28"
        >
          <motion.p
            variants={fade}
            className="text-xs font-semibold uppercase tracking-[0.35em] text-[#d9a441]"
          >
            Plataforma LMS Enterprise · CESMAG · Pasto, Colombia
          </motion.p>
          <motion.h1
            variants={fade}
            className="mx-auto mt-5 max-w-4xl text-5xl font-extrabold leading-tight tracking-tight text-white md:text-6xl lg:text-[4rem]"
          >
            Aprende sin
            <span className="text-[#d9a441]"> límites</span>.<br />
            Enseña sin fronteras.
          </motion.h1>
          <motion.p
            variants={fade}
            className="mx-auto mt-6 max-w-2xl text-base leading-8 text-white/60"
          >
            E-TAU Campus Virtual integra Moodle 5 con paneles modernos basados en IA,
            experiencias diferenciadas por rol y arquitectura enterprise lista para escalar.
          </motion.p>

          <motion.div variants={fade} className="mt-10 flex flex-wrap items-center justify-center gap-4">
            <Link
              to="/dashboard"
              className="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-900/40 transition hover:-translate-y-0.5 hover:bg-blue-700"
            >
              <LayoutDashboard className="h-4 w-4" />
              Ir al panel
            </Link>
            <Link
              to="/login"
              className="flex items-center gap-2 rounded-xl border border-white/20 bg-white/8 px-6 py-3 text-sm font-semibold text-white backdrop-blur transition hover:border-white/35 hover:bg-white/12"
            >
              Iniciar sesión
              <ArrowRight className="h-4 w-4" />
            </Link>
          </motion.div>

          {/* Stats */}
          <motion.div
            variants={fade}
            className="mx-auto mt-14 grid max-w-2xl grid-cols-3 gap-4"
          >
            {stats.map(({ label, value, icon: Icon }) => (
              <div
                key={label}
                className="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur"
              >
                <Icon className="mx-auto mb-2 h-5 w-5 text-[#d9a441]" />
                <p className="text-2xl font-bold text-white">{value}</p>
                <p className="mt-0.5 text-xs text-white/50">{label}</p>
              </div>
            ))}
          </motion.div>
        </motion.div>

        {/* Features */}
        <motion.div
          initial="hidden"
          whileInView="show"
          viewport={{ once: true, margin: "-80px" }}
          variants={{ show: { transition: { staggerChildren: 0.1 } } }}
          className="mt-24 grid gap-5 sm:grid-cols-3"
        >
          {features.map(({ icon: Icon, title, description, color, bg }) => (
            <motion.div
              key={title}
              variants={fade}
              className="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur transition hover:border-white/20 hover:bg-white/8"
            >
              <div className={`mb-4 inline-flex rounded-xl p-3 ${bg}`}>
                <Icon className={`h-5 w-5 ${color}`} />
              </div>
              <h3 className="font-bold text-white">{title}</h3>
              <p className="mt-2 text-sm leading-6 text-white/55">{description}</p>
            </motion.div>
          ))}
        </motion.div>

        {/* CTA band */}
        <motion.div
          initial={{ opacity: 0, y: 24 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="mt-16 mb-10 flex flex-col items-center justify-between gap-6 rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur sm:flex-row"
        >
          <div>
            <p className="font-bold text-white">¿Eres docente o administrador?</p>
            <p className="mt-1 text-sm text-white/55">Accede al panel para gestionar cursos, estudiantes e IA.</p>
          </div>
          <Link
            to="/dashboard"
            className="flex shrink-0 items-center gap-2 rounded-xl bg-[#d9a441] px-5 py-2.5 text-sm font-bold text-[#0b1f3a] transition hover:bg-[#c4912f]"
          >
            Panel institucional
            <ArrowRight className="h-4 w-4" />
          </Link>
        </motion.div>

        <footer className="border-t border-white/8 py-6 text-center text-xs text-white/25">
          E-TAU Campus Virtual · CESMAG · Pasto, Colombia · {new Date().getFullYear()}
        </footer>
      </div>
    </main>
  );
}
