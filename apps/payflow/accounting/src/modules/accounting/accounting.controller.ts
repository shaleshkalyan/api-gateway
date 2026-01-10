import { Request, Response, NextFunction } from "express";
import { AccountingService } from "./accounting.service";
import { success } from "../../utils/response";
import {
  validateCreateWallet,
  validateLedgerEntry,
} from "./accounting.validator";

const service = new AccountingService();

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

export async function ledgerEntry(
  req: Request,
  res: Response,
  next: NextFunction
) {
  try {
    validateLedgerEntry(req.body);
    const result = await service.applyLedger(
      req.body.wallet_id,
      req.body.type,
      req.body.amount
    );
    res.json(success(result));
  } catch (err) {
    next(err);
  }
}
