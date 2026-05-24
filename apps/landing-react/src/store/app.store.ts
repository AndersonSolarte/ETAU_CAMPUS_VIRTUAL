import { create } from "zustand";

type AppState = {
  sidebarOpen: boolean;
  setSidebarOpen: (value: boolean) => void;
};

export const useAppStore = create<AppState>((set) => ({
  sidebarOpen: false,
  setSidebarOpen: (value) => set({ sidebarOpen: value })
}));

