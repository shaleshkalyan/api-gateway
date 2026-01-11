CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE payflow_wallets (
  id UUID PRIMARY KEY,
  user_id UUID NOT NULL,
  currency VARCHAR(10) NOT NULL,
  balance NUMERIC(14,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE payflow_ledger_entries (
  id UUID PRIMARY KEY,
  wallet_id UUID NOT NULL,
  transaction_id UUID NOT NULL,
  type VARCHAR(10) CHECK (type IN ('credit','debit')),
  amount NUMERIC(14,2) NOT NULL,
  balance_after NUMERIC(14,2) NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);
