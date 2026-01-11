import dotenv from "dotenv";
dotenv.config();

export const env = {
  port: Number(process.env.PORT || 4003),
  databaseUrl: process.env.DATABASE_URL as string,
  accountingUrl: process.env.ACCOUNTING_SERVICE_URL as string,
};

if (!env.databaseUrl || !env.accountingUrl) {
  throw new Error("Missing env variables");
}
