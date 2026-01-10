import { AppError } from "../../utils/error";

export function validateCreateWallet(body: any) {
  if (!body.user_id || !body.currency) {
    throw new AppError("user_id and currency are required");
  }
}

export function validateLedgerEntry(body: any) {
  if (!body.wallet_id || !body.type || !body.amount) {
    throw new AppError("wallet_id, type, amount are required");
  }
}
