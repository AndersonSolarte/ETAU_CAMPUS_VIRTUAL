import type { Request, Response } from "express";

export function notFoundMiddleware(req: Request, res: Response) {
  res.status(404).json({
    status: "error",
    message: `Route ${req.originalUrl} not found`
  });
}

