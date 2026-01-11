import { Pool } from "pg";
import { env } from "./constants";

export const db = new Pool({
  connectionString: env.databaseUrl,
});
