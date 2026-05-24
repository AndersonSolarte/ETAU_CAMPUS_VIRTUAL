import { Router } from "express";

const router = Router();

router.get("/health", (_req, res) => {
  res.json({
    status: "ok",
    service: "tau-api",
    timestamp: new Date().toISOString()
  });
});

export const apiRouter = router;

