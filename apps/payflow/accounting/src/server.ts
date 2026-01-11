import app from "./app";
import { env } from "./config/constants";

app.listen(env.port, () => {
  console.log(`Payflow Accounting Service running on ${env.port}`);
});
