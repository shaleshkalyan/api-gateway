import dotenv from "dotenv";
dotenv.config();

export const env = {
  port: Number(process.env.PORT || 4001),
  jwtPublicKey: process.env.JWT_PUBLIC_KEY as string,
};