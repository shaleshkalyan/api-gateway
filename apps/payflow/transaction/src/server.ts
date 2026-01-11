import app from "./app";
import { env } from "./config/constants";

app.listen(env.port, () => {
  console.log(`Transaction service running on ${env.port}`);
});
