import type { Request, Response } from "express";

import { healthModule } from "../modules/health/health.module.js";

export function getHealth(_req: Request, res: Response) {
  res.status(200).json(healthModule.status());
}

