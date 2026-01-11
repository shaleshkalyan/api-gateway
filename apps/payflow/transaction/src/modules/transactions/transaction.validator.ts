import { AppError } from "../../utils/error";

export function validateTransaction(body: any) {
  if (!body.user_id || !body.amount || !body.currency || !body.type) {
    throw new AppError("Invalid transaction payload");
  }
}

export function validateTransactionStatus(body: any) {
    if (!body.transaction_id) {
      throw new AppError("transaction_id is required", 400);
    }
}
