import http from "http";
import app from "./app";
import { initSocket } from "./sockets/socket";
import { logger } from "./utils/logger";

const PORT = process.env.PORT || 4010;

const server = http.createServer(app);

initSocket(server);

server.listen(PORT, () => {
  logger.info("Realtime service started", { port: PORT });
});
