import app from "./app";
import { env } from "./config/constants";
import { db } from "./config/database";

(async () => {
  const result = await db.query("SELECT NOW()");
  console.log("DB time:", result.rows[0]);
})();

app.listen(env.port, () => {
  console.log(`Payflow Accounting Service running on ${env.port}`);
});
