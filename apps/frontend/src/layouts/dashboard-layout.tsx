import { Outlet } from "react-router-dom";
import { LayoutDashboard, Menu } from "lucide-react";

import { Button } from "@/components/ui/button";
import { useAppStore } from "@/store/app.store";

export function DashboardLayout() {
  const { sidebarOpen, setSidebarOpen } = useAppStore();

  return (
    <div className="min-h-screen bg-slate-950 text-white">
      <header className="border-b border-white/10 bg-slate-950/90 backdrop-blur">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
          <div className="flex items-center gap-3">
            <Button
              variant="secondary"
              size="sm"
              className="border-white/15 bg-white/5 text-white hover:bg-white/10 hover:text-white"
              onClick={() => setSidebarOpen(!sidebarOpen)}
            >
              <Menu className="mr-2 h-4 w-4" />
              Menú
            </Button>
            <div>
              <p className="text-xs uppercase tracking-[0.32em] text-cyan-300">
                TAU Platform
              </p>
              <p className="text-sm text-slate-300">Dashboard base institucional</p>
            </div>
          </div>
          <div className="flex items-center gap-2 text-sm text-slate-400">
            <LayoutDashboard className="h-4 w-4" />
            Preparado para SSO y widgets Moodle
          </div>
        </div>
      </header>

      <div className="mx-auto grid max-w-7xl gap-6 px-6 py-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside
          className={`rounded-3xl border border-white/10 bg-white/5 p-5 ${
            sidebarOpen ? "block" : "hidden lg:block"
          }`}
        >
          <p className="text-xs uppercase tracking-[0.28em] text-slate-400">
            Navegación
          </p>
          <div className="mt-4 space-y-3 text-sm text-slate-200">
            <div className="rounded-2xl bg-white/5 px-4 py-3">Resumen ejecutivo</div>
            <div className="rounded-2xl bg-white/5 px-4 py-3">Integraciones LMS</div>
            <div className="rounded-2xl bg-white/5 px-4 py-3">Analítica futura</div>
          </div>
        </aside>

        <section className="rounded-[2rem] border border-white/10 bg-white/5 p-6">
          <Outlet />
        </section>
      </div>
    </div>
  );
}

