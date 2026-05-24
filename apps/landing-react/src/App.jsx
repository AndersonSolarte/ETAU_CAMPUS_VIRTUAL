import { motion } from "framer-motion";

import { Button } from "@/components/ui/button";

const quickLinks = [
  "Aulas virtuales",
  "Admisiones",
  "Soporte TI",
  "Calendario académico"
];

export function App() {
  return (
    <main className="min-h-screen bg-[radial-gradient(circle_at_top,#e7f4ff,transparent_35%),linear-gradient(135deg,#f7fbff_0%,#eef6f3_45%,#f4efe6_100%)] text-slate-900">
      <section className="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-10">
        <header className="flex items-center justify-between rounded-full border border-white/60 bg-white/70 px-5 py-3 shadow-sm backdrop-blur">
          <div>
            <p className="text-xs font-semibold uppercase tracking-[0.35em] text-cyan-700">
              TAU Campus Virtual
            </p>
            <h1 className="text-sm font-medium text-slate-700">
              Plataforma institucional enterprise
            </h1>
          </div>
          <a
            href="http://localhost:8080/login/index.php"
            className="inline-flex"
          >
            <Button variant="dark" size="sm">Ingresar al LMS</Button>
          </a>
        </header>

        <div className="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.2fr_0.8fr]">
          <motion.div
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.7 }}
          >
            <p className="mb-4 text-sm font-semibold uppercase tracking-[0.35em] text-cyan-700">
              Experiencia institucional moderna
            </p>
            <h2 className="max-w-3xl text-5xl font-semibold tracking-tight text-slate-950 md:text-7xl">
              El campus digital que conecta aprendizaje, operación y crecimiento.
            </h2>
            <p className="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
              Moodle gestiona el LMS, React moderniza la primera impresión y
              Node.js prepara una capa enterprise para integraciones, analítica
              y evolución futura.
            </p>
            <div className="mt-10 flex flex-wrap gap-4">
              <Button asChild className="shadow-lg shadow-cyan-200">
                <a href="http://localhost:8080">Explorar plataforma</a>
              </Button>
              <Button asChild variant="secondary">
                <a href="#accesos">Ver accesos rápidos</a>
              </Button>
            </div>
          </motion.div>

          <motion.aside
            initial={{ opacity: 0, y: 24 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.1 }}
            className="rounded-[2rem] border border-white/60 bg-white/80 p-8 shadow-2xl shadow-slate-200 backdrop-blur"
          >
            <p className="text-sm font-semibold text-slate-500">
              Acceso unificado
            </p>
            <h3 className="mt-3 text-2xl font-semibold text-slate-950">
              Ingreso elegante y escalable
            </h3>
            <p className="mt-4 text-sm leading-7 text-slate-600">
              Base preparada para login moderno, SSO futuro y experiencias
              institucionales conectadas al motor principal de Moodle.
            </p>
            <div id="accesos" className="mt-8 grid gap-3">
              {quickLinks.map((item) => (
                <div
                  key={item}
                  className="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-medium text-slate-700"
                >
                  {item}
                </div>
              ))}
            </div>
          </motion.aside>
        </div>

        <footer className="border-t border-slate-200/80 py-6 text-sm text-slate-500">
          TAU Campus Virtual · Arquitectura modular · Moodle + React + Node.js
        </footer>
      </section>
    </main>
  );
}
