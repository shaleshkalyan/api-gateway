CREATE TABLE IF NOT EXISTS payflow_idempotency_keys (
  key TEXT PRIMARY KEY,
  transaction_id UUID,
  created_at TIMESTAMP DEFAULT NOW()
);
