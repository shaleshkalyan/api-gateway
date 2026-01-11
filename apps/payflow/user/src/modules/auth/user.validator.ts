import { AppError } from "../../utils/error";

export const validateAuth = (body: any) => {
  if (!body.email || !body.password) {
    throw new AppError("email and password required");
  }
};
