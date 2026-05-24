import type { ReactNode } from "react";
import { motion } from "framer-motion";
import { ArrowRight, Building2, CircleUserRound, ShieldCheck } from "lucide-react";
import { Link } from "react-router-dom";

import { Button } from "@/components/ui/button";
import { useHealthCheck } from "@/hooks/use-health-check";

const quickLinks = [
  "Acceso a aulas virtuales",
  "Servicios institucionales",
  "Calendario académico",
  "Mesa de ayuda"
];

export function HomePage() {
  const health = useHealthCheck();

  return (
    <main className="min-h-screen bg-[radial-gradient(circle_at_top,#dff4ff,transparent_30%),linear-gradient(135deg,#f7fbff_0%,#eef6f3_45%,#f7efe4_100%)] text-slate-900">
      <section className="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-10">
        <header className="flex flex-col gap-4 rounded-[2rem] border border-white/70 bg-white/70 px-5 py-4 shadow-sm backdrop-blur md:flex-row md:items-center md:justify-between">
          <div>
            <p className="text-xs font-semibold uppercase tracking-[0.35em] text-cyan-700">
              TAU Campus Virtual
            </p>
            <h1 className="mt-2 text-sm font-medium text-slate-700">
              Arquitectura enterprise sobre Moodle, React y Node.js
            </h1>
          </div>
          <div className="flex flex-wrap items-center gap-3">
            <span className="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">
              API {health.status === "ok" ? "operativa" : health.status}
            </span>
            <Button asChild variant="dark" size="sm">
              <a href="http://localhost:8080/login/index.php">Ingresar al LMS</a>
            </Button>
          </div>
        </header>

        <div className="grid flex-1 items-center gap-12 py-16 lg:grid-cols-[1.2fr_0.8fr]">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.65 }}
          >
            <p className="mb-4 text-sm font-semibold uppercase tracking-[0.35em] text-cyan-700">
              Plataforma institucional moderna
            </p>
            <h2 className="max-w-3xl text-5xl font-semibold tracking-tight text-slate-950 md:text-7xl">
              Un campus digital preparado para escalar sin rehacer Moodle.
            </h2>
            <p className="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
              La experiencia pública vive en React, la capa de integración corre
              en Node.js y Moodle permanece como motor LMS administrable,
              extensible y listo para futuras actualizaciones.
            </p>

            <div className="mt-10 flex flex-wrap gap-4">
              <Button asChild className="shadow-lg shadow-cyan-200">
                <Link to="/login">
                  Acceder ahora
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Link>
              </Button>
              <Button asChild variant="secondary">
                <a href="http://localhost:8080">Ver Moodle</a>
              </Button>
            </div>

            <div className="mt-12 grid gap-4 md:grid-cols-3">
              <FeatureCard
                icon={<Building2 className="h-5 w-5" />}
                title="Gestión institucional"
                description="Landing desacoplada para admisiones, comunicación y marca."
              />
              <FeatureCard
                icon={<CircleUserRound className="h-5 w-5" />}
                title="Experiencia moderna"
                description="Base lista para login moderno, SSO y dashboard personalizado."
              />
              <FeatureCard
                icon={<ShieldCheck className="h-5 w-5" />}
                title="Compatibilidad futura"
                description="Sin tocar core de Moodle y con capas separadas por responsabilidad."
              />
            </div>
          </motion.div>

          <motion.aside
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.75, delay: 0.1 }}
            className="rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-2xl shadow-slate-200 backdrop-blur"
          >
            <p className="text-sm font-semibold text-slate-500">Accesos rápidos</p>
            <h3 className="mt-3 text-2xl font-semibold text-slate-950">
              Base institucional lista para producción
            </h3>
            <p className="mt-4 text-sm leading-7 text-slate-600">
              El frontend ya conoce la API, el backend publica salud operativa y
              la plataforma queda preparada para integrar Moodle, analítica y
              servicios futuros.
            </p>
            <div className="mt-8 grid gap-3">
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
      </section>
    </main>
  );
}

type FeatureCardProps = {
  icon: ReactNode;
  title: string;
  description: string;
};

function FeatureCard({ icon, title, description }: FeatureCardProps) {
  return (
    <article className="rounded-[1.5rem] border border-white/70 bg-white/75 p-5 shadow-sm backdrop-blur">
      <div className="mb-4 inline-flex rounded-full bg-cyan-50 p-3 text-cyan-700">
        {icon}
      </div>
      <h3 className="text-base font-semibold text-slate-950">{title}</h3>
      <p className="mt-2 text-sm leading-6 text-slate-600">{description}</p>
    </article>
  );
}

