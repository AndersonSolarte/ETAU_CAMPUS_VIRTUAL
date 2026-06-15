export type AppEnv = {
  NODE_ENV: string;
  PORT: number;
  JWT_SECRET: string;
  DATABASE_URL: string;
  MOODLE_BASE_URL: string;
  MOODLE_WS_TOKEN: string;
  OPENAI_API_KEY: string;
  OPENAI_MODEL: string;
  OPENAI_MAX_TOKENS: number;
};
