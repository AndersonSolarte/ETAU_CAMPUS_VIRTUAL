import { useEffect, useState } from "react";

import { http } from "@/services/http";

type HealthStatus = {
  status: "idle" | "loading" | "ok" | "error";
  service?: string;
};

export function useHealthCheck() {
  const [health, setHealth] = useState<HealthStatus>({ status: "idle" });

  useEffect(() => {
    let active = true;

    async function fetchHealth() {
      setHealth({ status: "loading" });

      try {
        const response = await http.get("/health");

        if (active) {
          setHealth({
            status: "ok",
            service: response.data.service as string
          });
        }
      } catch {
        if (active) {
          setHealth({ status: "error" });
        }
      }
    }

    void fetchHealth();

    return () => {
      active = false;
    };
  }, []);

  return health;
}

