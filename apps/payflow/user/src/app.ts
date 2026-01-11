import express from "express";
import authRoutes from "./modules/auth/user.router";
import { errorMiddleware } from "./middleware/error.middleware";

const app = express();
app.use(express.json());
app.use("/", authRoutes);
app.use(errorMiddleware);

export default app;