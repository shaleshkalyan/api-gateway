import { db } from "../../config/database";
import axios from "axios";
import { randomUUID } from "crypto";
import { env } from "../../config/constants";
import { AppError } from "../../utils/error";
import { logger } from "../../utils/logger";

export class TransactionService {
  async createTransaction(body: any) {
    const transactionId = randomUUID();
    const idempotencyKey = body.idempotency_key;

    logger.info("Transaction started", {
      transaction_id: transactionId,
      user_id: body.user_id,
      amount: body.amount,
      type: body.type,
    });

    if (idempotencyKey) {
      logger.info("Checking idempotency key", {
        idempotency_key: idempotencyKey,
      });

      const exists = await db.query(
        "SELECT transaction_id FROM payflow_idempotency_keys WHERE key=$1",
        [idempotencyKey]
      );

      if (exists.rows.length > 0) {
        logger.info("Idempotent request detected", {
          idempotency_key: idempotencyKey,
          existing_transaction_id: exists.rows[0].transaction_id,
        });

        return { transaction_id: exists.rows[0].transaction_id };
      }
    }

    await db.query("BEGIN");
    logger.info("Database transaction started", {
      transaction_id: transactionId,
    });

    try {
      await db.query(
        `INSERT INTO payflow_transactions
         (id, user_id, amount, currency, type, status)
         VALUES ($1,$2,$3,$4,$5,'PENDING')`,
        [
          transactionId,
          body.user_id,
          body.amount,
          body.currency,
          body.type,
        ]
      );

      logger.info("Transaction record inserted", {
        transaction_id: transactionId,
      });

      logger.info("Calling accounting service", {
        transaction_id: transactionId,
        accounting_url: env.accountingUrl,
      });

      await axios.post(`${env.accountingUrl}/ledger/entry`, {
        transaction_id: transactionId,
        user_id : body.user_id,
        amount: body.amount,
        currency: body.currency,
        type: body.type,
      });

      logger.info("Accounting service success", {
        transaction_id: transactionId,
      });

      await db.query(
        "UPDATE payflow_transactions SET status='SUCCESS' WHERE id=$1",
        [transactionId]
      );

      if (idempotencyKey) {
        await db.query(
          "INSERT INTO payflow_idempotency_keys (key, transaction_id) VALUES ($1,$2)",
          [idempotencyKey, transactionId]
        );
      }

      await db.query("COMMIT");

      logger.info("Transaction committed successfully", {
        transaction_id: transactionId,
      });

      return { transaction_id: transactionId };
    } catch (error: any) {
      await db.query("ROLLBACK");

      logger.error("Transaction failed and rolled back", {
        transaction_id: transactionId,
        error_message: error.message,
      });

      throw new AppError("Transaction failed", 500);
    }
  }
}
