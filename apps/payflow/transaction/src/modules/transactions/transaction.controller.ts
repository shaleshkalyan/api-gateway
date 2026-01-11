import { Request, Response, NextFunction } from "express";
import { TransactionService } from "./transaction.service";
import { validateTransaction } from "./transaction.validator";

const service = new TransactionService();

export async function create(req: Request, res: Response, next: NextFunction) {
  try {
    validateTransaction(req.body);
    const result = await service.createTransaction(req.body);
    res.json({ success: true, data: result });
  } catch (e) {
    next(e);
  }
}
