import dotenv from "dotenv";
dotenv.config();

export const env = {
  port: Number(process.env.PORT || 4002),
  databaseUrl: process.env.DATABASE_URL as string,
  jwtSecret: process.env.JWT_SECRET as string,
};

if (!env.databaseUrl || !env.jwtSecret) {
  throw new Error("Missing environment variables");
}
