import { Server } from "socket.io";
import { logger } from "../utils/logger";
import { authenticateSocket } from "./socket.auth";

let io: Server;

export function initSocket(httpServer: any) {
  io = new Server(httpServer, {
    cors: {
      origin: "*",
    },
  });

  io.use(authenticateSocket);

  io.on("connection", (socket) => {
    const userId = socket.data.user_id;

    socket.join(`user:${userId}`);

    logger.info("Socket connected", {
      socket_id: socket.id,
      user_id: userId,
    });

    socket.on("disconnect", () => {
      logger.info("Socket disconnected", {
        socket_id: socket.id,
        user_id: userId,
      });
    });
  });

  return io;
}

export function getIO() {
  if (!io) {
    throw new Error("Socket.io not initialized");
  }
  return io;
}
