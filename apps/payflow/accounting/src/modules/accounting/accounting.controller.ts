import { Request, Response, NextFunction } from "express";
import { AccountingService } from "./accounting.service";
import { success } from "../../utils/response";
import {
  validateCreateWallet,
  validateLedgerEntry,
} from "./accounting.validator";

const service = new AccountingService();

/**
 * Optional API
 * You can keep this for admin/manual wallet creation
 * (Transaction flow does NOT use this)
 */
export async function createWallet(
  req: Request,
  res: Response,
  next: NextFunction
) {
  try {
    validateCreateWallet(req.body);

    const result = await service.createWallet(
      req.body.user_id,
      req.body.currency
    );

    res.json(success(result));
  } catch (err) {
    next(err);
  }
}

/**
 * Core API used by Transaction Service
 */
export async function ledgerEntry(
  req: Request,
  res: Response,
  next: NextFunction
) {
  try {
    validateLedgerEntry(req.body);

    const { transaction_id, user_id, currency, type, amount } = req.body;

    const result = await service.applyLedgerEntry({
      transaction_id,
      user_id,
      currency,
      type,
      amount,
    });

    res.json(success(result));
  } catch (err) {
    next(err);
  }
}
