import { PrismaClient } from "@prisma/client";
import { Router } from "express";

import { createAiCourseBuilderModule } from "../modules/ai-course-builder/ai-course-builder.module.js";
import { createAiToolsModule } from "../modules/ai-tools/ai-tools.module.js";
import { healthRouter } from "./health.routes.js";

const db = new PrismaClient();

const apiRouter = Router();

apiRouter.use(healthRouter);
apiRouter.use("/ai-course-builder", createAiCourseBuilderModule(db));
apiRouter.use("/ai", createAiToolsModule());

export { apiRouter };
