import dotenv from "dotenv";
import { z } from "zod";

import type { AppEnv } from "../types/env.js";

dotenv.config();

const envSchema = z.object({
  NODE_ENV: z.string().default("development"),
  PORT: z.coerce.number().default(4000),
  JWT_SECRET: z.string().min(10),
  DATABASE_URL: z.string().min(1),
  MOODLE_BASE_URL: z.string().url().default("http://localhost:8080")
});

export const env: AppEnv = envSchema.parse(process.env);

