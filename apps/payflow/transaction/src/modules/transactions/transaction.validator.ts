import { AppError } from "../../utils/error";

export function validateTransaction(body: any) {
  if (!body.user_id || !body.amount || !body.currency || !body.type) {
    throw new AppError("Invalid transaction payload");
  }
}
