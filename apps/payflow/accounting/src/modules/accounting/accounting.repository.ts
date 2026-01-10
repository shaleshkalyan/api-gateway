import { db } from "../../config/database";

export class AccountingRepository {
  async createWallet(id: string, userId: string, currency: string) {
    await db.query(
      "INSERT INTO payflow_wallets (id, user_id, currency) VALUES ($1,$2,$3)",
      [id, userId, currency]
    );
  }

  async getWalletForUpdate(client: any, walletId: string) {
    const res = await client.query(
      "SELECT balance FROM payflow_wallets WHERE id=$1 FOR UPDATE",
      [walletId]
    );
    return res.rows[0];
  }

  async updateBalance(client: any, walletId: string, balance: number) {
    await client.query(
      "UPDATE payflow_wallets SET balance=$1 WHERE id=$2",
      [balance, walletId]
    );
  }

  async insertLedger(client: any, data: any) {
    await client.query(
      `INSERT INTO payflow_ledger_entries
       (id, wallet_id, type, amount, balance_after)
       VALUES ($1,$2,$3,$4,$5)`,
      [
        data.id,
        data.wallet_id,
        data.type,
        data.amount,
        data.balance_after,
      ]
    );
  }
}
