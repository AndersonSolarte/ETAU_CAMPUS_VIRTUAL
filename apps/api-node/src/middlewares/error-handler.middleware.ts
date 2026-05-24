import type { NextFunction, Request, Response } from "express";

export function errorHandlerMiddleware(
  error: Error,
  _req: Request,
  res: Response,
  _next: NextFunction
) {
  res.status(500).json({
    status: "error",
    message: error.message || "Unexpected server error"
  });
}

