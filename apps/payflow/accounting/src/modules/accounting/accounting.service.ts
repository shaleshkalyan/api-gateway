import { db } from "../../config/database";
import { AccountingRepository } from "./accounting.repository";
import { AppError } from "../../utils/error";
import { randomUUID } from "crypto";
import { logger } from "../../utils/logger";

export class AccountingService {
  private repo = new AccountingRepository();

  /**
   * Optional API
   * Manual/admin wallet creation
   */
  async createWallet(userId: string, currency: string) {
    const existing = await this.repo.getWalletByUser(userId, currency);

    if (existing) {
      return {
        wallet_id: existing.id,
        balance: existing.balance,
      };
    }

    const walletId = randomUUID();

    logger.info("Creating wallet", {
      wallet_id: walletId,
      user_id: userId,
      currency,
    });

    await this.repo.createWallet(walletId, userId, currency);

    return {
      wallet_id: walletId,
      balance: 0,
    };
  }

  /**
   * Core ledger API
   * Used by Transaction Service
   */
  async applyLedgerEntry(body: {
    transaction_id: string;
    user_id: string;
    currency: string;
    type: "CREDIT" | "DEBIT";
    amount: number;
  }) {
    const { transaction_id, user_id, currency, type, amount } = body;

    const client = await db.connect();

    try {
      logger.info("Ledger apply started", {
        transaction_id,
        user_id,
        currency,
        type,
        amount,
      });

      await client.query("BEGIN");

      // Resolve wallet (auto-create if missing)
      let wallet = await this.repo.getWalletByUser(user_id, currency);

      if (!wallet) {
        const walletId = randomUUID();

        logger.info("Auto-creating wallet", {
          wallet_id: walletId,
          user_id,
          currency,
        });

        await this.repo.createWallet(walletId, user_id, currency);

        wallet = {
          id: walletId,
          user_id,
          currency,
          balance: 0,
        };
      }

      // Lock wallet row for update
      const lockedWallet = await this.repo.getWalletForUpdate(
        client,
        wallet.id
      );

      if (!lockedWallet) {
        throw new AppError("Wallet not found", 404);
      }

      const currentBalance = Number(lockedWallet.balance);
      const newBalance =
        type === "CREDIT"
          ? currentBalance + amount
          : currentBalance - amount;

      if (newBalance < 0) {
        throw new AppError("Insufficient balance", 400);
      }

      // Update balance
      await this.repo.updateBalance(client, wallet.id, newBalance);

      // Insert ledger entry
      await this.repo.insertLedger(client, {
        id: randomUUID(),
        wallet_id: wallet.id,
        transaction_id,
        type,
        amount,
        balance_after: newBalance,
      });

      await client.query("COMMIT");

      logger.info("Ledger applied successfully", {
        transaction_id,
        wallet_id: wallet.id,
        balance_after: newBalance,
      });

      return {
        wallet_id: wallet.id,
        balance: newBalance,
      };
    } catch (error: any) {
      await client.query("ROLLBACK");

      logger.error("Ledger apply failed", {
        transaction_id,
        error: error.message,
      });

      throw error;
    } finally {
      client.release();
    }
  }
}
