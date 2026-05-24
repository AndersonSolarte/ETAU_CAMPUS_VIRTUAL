import { LockKeyhole, Mail } from "lucide-react";

import { Button } from "@/components/ui/button";

export function LoginPage() {
  return (
    <main className="min-h-screen bg-[linear-gradient(180deg,#0f172a_0%,#0b1120_100%)] px-6 py-10 text-white">
      <div className="mx-auto grid min-h-[calc(100vh-5rem)] max-w-6xl items-center gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <section>
          <p className="text-xs uppercase tracking-[0.35em] text-cyan-300">
            TAU Identity Layer
          </p>
          <h1 className="mt-4 max-w-2xl text-5xl font-semibold tracking-tight md:text-6xl">
            Acceso elegante para un LMS enterprise.
          </h1>
          <p className="mt-6 max-w-xl text-lg leading-8 text-slate-300">
            Esta vista prepara la futura experiencia de autenticación
            institucional. Mientras tanto, redirige al login oficial de Moodle
            sin romper la administración nativa del LMS.
          </p>
        </section>

        <section className="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-cyan-950/20 backdrop-blur">
          <div className="space-y-5">
            <label className="block">
              <span className="mb-2 block text-sm text-slate-300">Correo institucional</span>
              <div className="flex items-center rounded-2xl border border-white/10 bg-slate-900/70 px-4">
                <Mail className="h-4 w-4 text-slate-500" />
                <input
                  className="w-full bg-transparent px-3 py-4 text-sm outline-none placeholder:text-slate-500"
                  placeholder="nombre@tau.edu"
                />
              </div>
            </label>
            <label className="block">
              <span className="mb-2 block text-sm text-slate-300">Contraseña</span>
              <div className="flex items-center rounded-2xl border border-white/10 bg-slate-900/70 px-4">
                <LockKeyhole className="h-4 w-4 text-slate-500" />
                <input
                  type="password"
                  className="w-full bg-transparent px-3 py-4 text-sm outline-none placeholder:text-slate-500"
                  placeholder="••••••••"
                />
              </div>
            </label>
            <Button asChild className="w-full">
              <a href="http://localhost:8080/login/index.php">Continuar en Moodle</a>
            </Button>
          </div>
        </section>
      </div>
    </main>
  );
}

