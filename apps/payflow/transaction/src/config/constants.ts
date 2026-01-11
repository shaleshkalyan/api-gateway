import dotenv from "dotenv";
dotenv.config();

export const env = {
  port: Number(process.env.PORT || 4003),
  databaseUrl: process.env.DATABASE_URL as string,
  accountingUrl: process.env.ACCOUNTING_SERVICE_URL as string,
  realtimeUrl: process.env.REALTIME_URL as string,
  realtimeToken: process.env.REALTIME_SERVICE_TOKEN as string,
};

if (!env.databaseUrl || !env.accountingUrl || !env.realtimeToken || !env.realtimeUrl) {
  throw new Error("Missing env variables");
}
