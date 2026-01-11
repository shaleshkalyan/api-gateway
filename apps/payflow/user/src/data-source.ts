import "reflect-metadata";
import { DataSource } from "typeorm";
import { env } from "./config/constants";
import { User } from "./modules/auth/user.entity";

export const AppDataSource = new DataSource({
  type: "postgres",
  url: env.databaseUrl,
  entities: [User],
  migrations: ["dist/migrations/*.js"],
  synchronize: false,
  logging: false,
});
