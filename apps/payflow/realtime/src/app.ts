import express from "express";
import cors from "cors";
import { json } from "body-parser";
import { emitRouter } from "./api/emit.controller";
import { logger } from "./utils/logger";

const app = express();

app.use(cors());
app.use(json());

app.get("/health", (_, res) => {
  res.json({ status: "ok" });
});

/**
 * Internal API
 */
app.use("/emit", emitRouter);

app.use((err: any, _req: any, res: any, _next: any) => {
  logger.error("Unhandled error", { error: err.message });
  res.status(500).json({ success: false, error: "Internal server error" });
});

export default app;
