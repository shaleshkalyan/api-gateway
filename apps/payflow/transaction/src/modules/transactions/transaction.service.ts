import { db } from "../../config/database";
import axios from "axios";
import { randomUUID } from "crypto";
import { env } from "../../config/constants";
import { AppError } from "../../utils/error";
import { logger } from "../../utils/logger";
import { emitRealtimeEvent } from "../clients/client.realtime";

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
      const exists = await db.query(
        "SELECT transaction_id FROM payflow_idempotency_keys WHERE key=$1",
        [idempotencyKey]
      );

      if (exists.rows.length > 0) {
        return { transaction_id: exists.rows[0].transaction_id };
      }
    }

    await db.query("BEGIN");

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

      await db.query("COMMIT");

      emitRealtimeEvent(body.user_id, "transaction:created", {
        transaction_id: transactionId,
        status: "PENDING",
      });


      await axios.post(`${env.accountingUrl}/ledger/entry`, {
        transaction_id: transactionId,
        user_id: body.user_id,
        amount: body.amount,
        currency: body.currency,
        type: body.type,
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


      emitRealtimeEvent(body.user_id, "transaction:success", {
        transaction_id: transactionId,
        status: "SUCCESS",
      });

      return { transaction_id: transactionId };
    } catch (error: any) {
      await db.query("ROLLBACK");

      await db.query(
        "UPDATE payflow_transactions SET status='FAILED' WHERE id=$1",
        [transactionId]
      );

      emitRealtimeEvent(body.user_id, "transaction:failed", {
        transaction_id: transactionId,
        status: "FAILED",
        reason: error.message,
      });

      logger.error("Transaction failed", {
        transaction_id: transactionId,
        error: error.message,
      });

      throw new AppError("Transaction failed", 500);
    }
  }
  async getTransactionStatus(transactionId: string) {
    logger.info("Transaction status requested", {
      transaction_id: transactionId,
    });

    const tx = await db.query(
      `SELECT id, status, amount, currency, type, created_at
       FROM payflow_transactions
       WHERE id = $1 LIMIT 1`,
      [transactionId]
    );

    if (!tx.rows.length) {
      throw new AppError("Transaction not found", 404);
    }

    return {
      transaction_id: tx.rows[0].id,
      status: tx.rows[0].status,
      amount: tx.rows[0].amount,
      currency: tx.rows[0].currency,
      type: tx.rows[0].type,
      created_at: tx.rows[0].created_at,
    };
  }
}
