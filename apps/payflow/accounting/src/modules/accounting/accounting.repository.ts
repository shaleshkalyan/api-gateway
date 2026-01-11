import { db } from "../../config/database";

export class AccountingRepository {
  async createWallet(id: string, userId: string, currency: string) {
    await db.query(
      `INSERT INTO payflow_wallets (id, user_id, currency, balance)
       VALUES ($1, $2, $3, 0)`,
      [id, userId, currency]
    );
  }

  async getWalletByUser(userId: string, currency: string) {
    const res = await db.query(
      `SELECT id, user_id, currency, balance
       FROM payflow_wallets
       WHERE user_id = $1 AND currency = $2`,
      [userId, currency]
    );

    return res.rows[0] || null;
  }

  async getWalletForUpdate(client: any, walletId: string) {
    const res = await client.query(
      `SELECT id, balance
       FROM payflow_wallets
       WHERE id = $1
       FOR UPDATE`,
      [walletId]
    );

    return res.rows[0] || null;
  }

  async updateBalance(client: any, walletId: string, balance: number) {
    await client.query(
      `UPDATE payflow_wallets
       SET balance = $1
       WHERE id = $2`,
      [balance, walletId]
    );
  }

  async insertLedger(
    client: any,
    data: {
      id: string;
      wallet_id: string;
      transaction_id: string;
      type: string;
      amount: number;
      balance_after: number;
    }
  ) {
    await client.query(
      `INSERT INTO payflow_ledger_entries
       (id, wallet_id, transaction_id, type, amount, balance_after)
       VALUES ($1, $2, $3, $4, $5, $6)`,
      [
        data.id,
        data.wallet_id,
        data.transaction_id,
        data.type,
        data.amount,
        data.balance_after,
      ]
    );
  }
}
