import { AppError } from "../../utils/error";

export function validateCreateWallet(body: any) {
  if (!body.user_id || !body.currency) {
    throw new AppError("user_id and currency are required", 400);
  }
}

export function validateLedgerEntry(body: any) {
  if (
    !body.transaction_id ||
    !body.user_id ||
    !body.currency ||
    !body.type ||
    !body.amount
  ) {
    throw new AppError(
      "transaction_id, user_id, currency, type, amount are required",
      400
    );
  }
}
