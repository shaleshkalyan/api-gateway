import { Router, Request, Response } from "express";
import { success } from "../utils/response";
import { logger } from "../utils/logger";
import { getIO } from "../sockets/socket";

export const emitRouter = Router();

/**
 * Internal API
 * Called by Transaction Service
 */
emitRouter.post("/", (req: Request, res: Response) => {
  const { user_id, event, payload } = req.body;

  if (!user_id || !event) {
    return res.status(400).json({
      success: false,
      error: "user_id and event are required",
    });
  }

  const io = getIO();

  io.to(`user:${user_id}`).emit(event, payload);

  logger.info("Realtime event emitted", {
    user_id,
    event,
    payload,
  });

  res.json(success({ delivered: true }));
});
