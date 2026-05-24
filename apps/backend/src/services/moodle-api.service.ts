import axios from "axios";

import { env } from "../config/env.js";

export const moodleApi = axios.create({
  baseURL: env.MOODLE_BASE_URL,
  timeout: 10000
});

