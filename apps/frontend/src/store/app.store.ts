import { create } from "zustand";
import { persist } from "zustand/middleware";

export type UserRole = "admin" | "teacher" | "student";

export interface UserSession {
  role: UserRole;
  name: string;
  email: string;
  initials: string;
}

type AppState = {
  sidebarOpen: boolean;
  session: UserSession | null;
  setSidebarOpen: (value: boolean) => void;
  setSession: (session: UserSession | null) => void;
  switchRole: (role: UserRole) => void;
};

const DEMO_SESSIONS: Record<UserRole, UserSession> = {
  admin: {
    role: "admin",
    name: "Admin TAU",
    email: "admin@tau.edu.co",
    initials: "AT"
  },
  teacher: {
    role: "teacher",
    name: "Carlos Rodríguez",
    email: "c.rodriguez@tau.edu.co",
    initials: "CR"
  },
  student: {
    role: "student",
    name: "Sofía Méndez",
    email: "sofia.mendez@estudiante.tau.edu.co",
    initials: "SM"
  }
};

export const useAppStore = create<AppState>()(
  persist(
    (set) => ({
      sidebarOpen: true,
      session: DEMO_SESSIONS.admin,

      setSidebarOpen: (value) => set({ sidebarOpen: value }),
      setSession: (session) => set({ session }),
      switchRole: (role) => set({ session: DEMO_SESSIONS[role] }),
    }),
    { name: "tau-app-store" }
  )
);

export { DEMO_SESSIONS };