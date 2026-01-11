import app from "./app";
import { env } from "./config/constants";
import { AppDataSource } from "./data-source";

AppDataSource.initialize().then(() => {
  app.listen(env.port, () =>
    console.log(`User service running on ${env.port}`)
  );
});
