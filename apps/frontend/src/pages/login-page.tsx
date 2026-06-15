import { motion } from "framer-motion";
import { ArrowRight, BookOpen, GraduationCap, LockKeyhole, Mail, Users } from "lucide-react";
import { useState } from "react";
import { Link } from "react-router-dom";

import { Button } from "@/components/ui/button";

const stats = [
  { icon: GraduationCap, label: "Estudiantes", value: "4.200+" },
  { icon: BookOpen, label: "Cursos activos", value: "180+" },
  { icon: Users, label: "Docentes", value: "95+" },
];

export function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  return (
    <main className="relative min-h-screen overflow-hidden bg-[#0b1f3a]">
      {/* Background SVG pattern */}
      <div
        className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60"
        style={{ backgroundImage: "url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1920 1080%22%3E%3Cdefs%3E%3CradialGradient id=%22rg1%22 cx=%2225%25%22 cy=%2235%25%22 r=%2255%25%22%3E%3Cstop offset=%220%25%22 stop-color=%22%231f4b99%22 stop-opacity=%220.5%22/%3E%3Cstop offset=%22100%25%22 stop-color=%22%230b1f3a%22 stop-opacity=%220%22/%3E%3C/radialGradient%3E%3Cpattern id=%22dots%22 x=%220%22 y=%220%22 width=%2240%22 height=%2240%22 patternUnits=%22userSpaceOnUse%22%3E%3Ccircle cx=%2220%22 cy=%2220%22 r=%221.2%22 fill=%22white%22 opacity=%220.08%22/%3E%3C/pattern%3E%3C/defs%3E%3Crect width=%221920%22 height=%221080%22 fill=%22url(%23rg1)%22/%3E%3Crect width=%221920%22 height=%221080%22 fill=%22url(%23dots)%22/%3E%3C/svg%3E')" }}
      />

      {/* Gold accent line top */}
      <div className="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-[#1f4b99] via-[#d9a441] to-[#1f4b99]" />

      <div className="relative z-10 mx-auto grid min-h-screen max-w-7xl grid-cols-1 items-center gap-12 px-6 py-10 lg:grid-cols-2 lg:px-12">

        {/* ── Left column: branding ── */}
        <motion.div
          initial={{ opacity: 0, x: -32 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.7, ease: [0.4, 0, 0.2, 1] }}
          className="flex flex-col gap-8"
        >
          {/* Logo */}
          <div className="flex items-center gap-3">
            <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-[#1f4b99]">
              <span className="text-lg font-black text-white">T</span>
            </div>
            <div>
              <p className="text-xs font-semibold uppercase tracking-[0.3em] text-[#d9a441]">TAU</p>
              <p className="text-xs font-medium text-white/60 tracking-wider">Campus Virtual</p>
            </div>
          </div>

          {/* Headline */}
          <div>
            <h1 className="text-4xl font-extrabold leading-tight tracking-tight text-white md:text-5xl lg:text-[3.25rem]">
              Aprende sin
              <br />
              <span className="text-[#d9a441]">límites.</span>
            </h1>
            <p className="mt-4 max-w-md text-base leading-7 text-white/65">
              Plataforma LMS enterprise de la institución. Accede a tus cursos,
              calificaciones y actividades académicas desde cualquier lugar.
            </p>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-3 gap-4">
            {stats.map(({ icon: Icon, label, value }) => (
              <div
                key={label}
                className="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm"
              >
                <Icon className="mb-2 h-5 w-5 text-[#d9a441]" />
                <p className="text-xl font-bold text-white">{value}</p>
                <p className="text-xs text-white/55">{label}</p>
              </div>
            ))}
          </div>

          {/* Direct Moodle link */}
          <p className="text-sm text-white/40">
            ¿Prefieres ingresar directamente al LMS?{" "}
            <a
              href="http://localhost:8080/login/index.php"
              className="text-[#d9a441] underline-offset-2 hover:underline"
            >
              Acceso Moodle
            </a>
          </p>
        </motion.div>

        {/* ── Right column: glass card form ── */}
        <motion.div
          initial={{ opacity: 0, y: 24 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.65, delay: 0.15, ease: [0.4, 0, 0.2, 1] }}
        >
          <div className="relative overflow-hidden rounded-3xl border border-white/15 bg-white/8 p-8 shadow-2xl backdrop-blur-2xl md:p-10"
               style={{ background: "rgba(255,255,255,0.07)" }}>
            {/* Top accent strip */}
            <div className="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-[#4a7fd4] to-[#d9a441]" />

            <div className="mb-8">
              <h2 className="text-2xl font-bold text-white">Iniciar sesión</h2>
              <p className="mt-1 text-sm text-white/55">Accede con tus credenciales institucionales</p>
            </div>

            <div className="space-y-5">
              {/* Email */}
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-white/80">
                  Correo institucional
                </label>
                <div className="flex items-center gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3 transition focus-within:border-[#d9a441] focus-within:bg-white/15 focus-within:shadow-[0_0_0_3px_rgba(217,164,65,0.2)]">
                  <Mail className="h-4 w-4 shrink-0 text-white/40" />
                  <input
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="nombre@tau.edu.co"
                    className="w-full bg-transparent text-sm text-white outline-none placeholder:text-white/35"
                  />
                </div>
              </div>

              {/* Password */}
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-white/80">
                  Contraseña
                </label>
                <div className="flex items-center gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3 transition focus-within:border-[#d9a441] focus-within:bg-white/15 focus-within:shadow-[0_0_0_3px_rgba(217,164,65,0.2)]">
                  <LockKeyhole className="h-4 w-4 shrink-0 text-white/40" />
                  <input
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="••••••••"
                    className="w-full bg-transparent text-sm text-white outline-none placeholder:text-white/35"
                  />
                </div>
              </div>

              {/* Forgot */}
              <div className="flex justify-end">
                <a href="#" className="text-xs text-[#f0c560] hover:text-[#d9a441] transition-colors">
                  ¿Olvidaste tu contraseña?
                </a>
              </div>

              {/* Submit */}
              <a
                href="http://localhost:8080/login/index.php"
                className="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#d9a441] to-[#b8872a] px-4 py-3.5 text-sm font-bold text-[#0b1f3a] shadow-lg transition hover:-translate-y-0.5 hover:shadow-[0_8px_20px_rgba(217,164,65,0.4)]"
              >
                Ingresar al Campus
                <ArrowRight className="h-4 w-4" />
              </a>

              {/* Divider */}
              <div className="flex items-center gap-3">
                <div className="h-px flex-1 bg-white/10" />
                <span className="text-xs text-white/35">o accede al panel</span>
                <div className="h-px flex-1 bg-white/10" />
              </div>

              {/* Dashboard link */}
              <Button asChild variant="secondary" className="w-full border-white/15 bg-white/8 text-white hover:bg-white/15 hover:text-white"
                style={{ background: "rgba(255,255,255,0.06)" }}>
                <Link to="/dashboard">
                  Panel de administración
                </Link>
              </Button>
            </div>

            <p className="mt-6 text-center text-xs text-white/30">
              TAU Campus Virtual · CESMAG · Pasto, Colombia
            </p>
          </div>
        </motion.div>
      </div>
    </main>
  );
}
