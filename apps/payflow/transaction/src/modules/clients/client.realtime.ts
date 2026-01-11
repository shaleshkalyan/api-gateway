import axios from "axios";
import { logger } from "../../utils/logger";
import { env } from "../../config/constants";



const realtimeClient = axios.create({
  baseURL: env.realtimeUrl,
  timeout: 3000,
  headers: {
    "x-service-token": env.realtimeToken,
    "Content-Type": "application/json",
  },
});

export async function emitRealtimeEvent(
  userId: string,
  event: string,
  payload: any
) {
  try {
    await realtimeClient.post("/emit", {
      user_id: userId,
      event,
      payload,
    });

    logger.info("Realtime event published", {
      user_id: userId,
      event,
    });
  } catch (error: any) {
    logger.error("Realtime event failed", {
      user_id: userId,
      event,
      error: error.message,
    });
  }
}
