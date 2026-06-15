import path from "node:path";

import dotenv from "dotenv";
import { z } from "zod";

import type { AppEnv } from "../types/env.js";

dotenv.config();
dotenv.config({ path: path.resolve(process.cwd(), "../../.env"), override: false });

const postgresUser = process.env.POSTGRES_USER ?? "tau_admin";
const postgresPassword = process.env.POSTGRES_PASSWORD ?? "change_me";
const postgresHost = process.env.POSTGRES_HOST ?? "localhost";
const postgresDb = process.env.POSTGRES_DB ?? "tau_moodle";

const databaseUrl =
  process.env.DATABASE_URL ??
  `postgresql://${postgresUser}:${postgresPassword}@${postgresHost}:5432/${postgresDb}?schema=public`;

const moodleBaseUrl = process.env.MOODLE_BASE_URL ?? process.env.MOODLE_URL ?? "http://localhost:8080";

const envSchema = z.object({
  NODE_ENV: z.string().default("development"),
  PORT: z.coerce.number().default(4000),
  JWT_SECRET: z.string().min(10),
  DATABASE_URL: z.string().min(1),
  MOODLE_BASE_URL: z.string().url().default("http://localhost:8080"),
  MOODLE_WS_TOKEN: z.string().default(""),
  OPENAI_API_KEY: z.string().min(1),
  OPENAI_MODEL: z.string().default("gpt-4o"),
  OPENAI_MAX_TOKENS: z.coerce.number().default(4096)
});

export const env: AppEnv = envSchema.parse({
  ...process.env,
  DATABASE_URL: databaseUrl,
  MOODLE_BASE_URL: moodleBaseUrl
});
