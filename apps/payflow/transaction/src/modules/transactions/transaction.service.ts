import { db } from "../../config/database";
import axios from "axios";
import { randomUUID } from "crypto";
import { env } from "../../config/constants";
import { AppError } from "../../utils/error";

export class TransactionService {
    async createTransaction(body: any) {
        const idempotencyKey = body.idempotency_key;
        const transactionId = randomUUID();

        if (idempotencyKey) {
            const exists = await db.query(
                "SELECT transaction_id FROM idempotency_keys WHERE key=$1",
                [idempotencyKey]
            );

            if (exists.rows.length > 0) {
                return { transaction_id: exists.rows[0].transaction_id };
            }
        }


        await db.query("BEGIN");

        try {
            await db.query(
                `INSERT INTO transactions
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

            await axios.post(`${env.accountingUrl}/ledger/entry`, {
                transaction_id: transactionId,
                user_id: body.user_id,
                amount: body.amount,
                currency: body.currency,
                type: body.type,
            });

            await db.query(
                "UPDATE transactions SET status='SUCCESS' WHERE id=$1",
                [transactionId]
            );

            if (idempotencyKey) {
                await db.query(
                    "INSERT INTO idempotency_keys (key, transaction_id) VALUES ($1,$2)",
                    [idempotencyKey, transactionId]
                );
            }

            await db.query("COMMIT");
            return { transaction_id: transactionId };
        } catch (err) {
            await db.query("ROLLBACK");
            throw new AppError("Transaction failed", 500);
        }
    }
}
