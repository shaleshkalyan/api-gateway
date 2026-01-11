import jwt from "jsonwebtoken";
import { logger } from "../utils/logger";

export function authenticateSocket(socket: any, next: any) {
  try {
    const token = socket.handshake.auth?.token;

    if (!token) {
      return next(new Error("Authentication required"));
    }

    const payload = jwt.verify(
      token,
      process.env.JWT_PUBLIC_KEY!
    ) as any;

    socket.data.user_id = payload.sub;

    next();
  } catch (err) {
    logger.error("Socket authentication failed");
    next(new Error("Unauthorized"));
  }
}
