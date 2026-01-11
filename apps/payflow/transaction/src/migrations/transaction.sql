CREATE TABLE IF NOT EXISTS payflow_transactions (
  id UUID PRIMARY KEY,
  user_id UUID NOT NULL,
  amount NUMERIC(14,2) NOT NULL,
  currency VARCHAR(10) NOT NULL,
  type VARCHAR(10) NOT NULL,
  status VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);