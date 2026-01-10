import { db } from "../../config/database";
import { AccountingRepository } from "./accounting.repository";
import { AppError } from "../../utils/error";
import { v4 as uuid } from "uuid";

export class AccountingService {
  private repo = new AccountingRepository();

  async createWallet(userId: string, currency: string) {
    const walletId = uuid();
    await this.repo.createWallet(walletId, userId, currency);
    return { wallet_id: walletId };
  }

  async applyLedger(
    walletId: string,
    type: "credit" | "debit",
    amount: number
  ) {
    const client = await db.connect();

    try {
      await client.query("BEGIN");

      const wallet = await this.repo.getWalletForUpdate(client, walletId);
      if (!wallet) throw new AppError("Wallet not found", 404);

      const currentBalance = Number(wallet.balance);
      const newBalance =
        type === "credit"
          ? currentBalance + amount
          : currentBalance - amount;

      if (newBalance < 0) {
        throw new AppError("Insufficient balance");
      }

      await this.repo.updateBalance(client, walletId, newBalance);

      await this.repo.insertLedger(client, {
        id: uuid(),
        wallet_id: walletId,
        type,
        amount,
        balance_after: newBalance,
      });

      await client.query("COMMIT");
      return { wallet_id: walletId, balance: newBalance };
    } catch (err) {
      await client.query("ROLLBACK");
      throw err;
    } finally {
      client.release();
    }
  }
}
