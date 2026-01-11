import { Request, Response, NextFunction } from "express";
import { TransactionService } from "./transaction.service";
import { validateTransaction, validateTransactionStatus } from "./transaction.validator";
import { logger } from "../../utils/logger";

const service = new TransactionService();

export async function create(req: Request, res: Response, next: NextFunction) {
  try {
    logger.info("Transaction request received", {
      path: req.path,
      body: req.body,
    });

    validateTransaction(req.body);

    const result = await service.createTransaction(req.body);

    logger.info("Transaction request completed", {
      transaction_id: result.transaction_id,
    });

    res.json({ success: true, data: result });
  } catch (e) {
    next(e);
  }
}

export async function transactionStatus(
  req: Request,
  res: Response,
  next: NextFunction
) {
  try {
    const { transaction_id } = req.body;
    validateTransactionStatus(req.body);

    const result = await service.getTransactionStatus(transaction_id);
    res.json({ success: true, data: result });
  } catch (err) {
    next(err);
  }
}