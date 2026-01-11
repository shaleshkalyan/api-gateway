import jwt from "jsonwebtoken";
import { env } from "../config/constants";

export const signToken = (payload: any) =>
  jwt.sign(payload, env.jwtSecret, { expiresIn: "1h" });
