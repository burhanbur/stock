table users {
  id uuid [pk]
  name varchar
  username varchar [unique]
  email varchar [unique]
  phone varchar
  password varchar
  email_verified_at datetime
  is_active boolean
  created_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP()`]
  updated_by uuid
  updated_at timestamp [default: `CURRENT_TIMESTAMP()`]
  deleted_by uuid
  deleted_at timestamp
}

table api_keys {
  id uuid [pk]
  name varchar
  key varchar (64) [unique]
  description text
  application varchar
  ip_whitelist json
  permissions json
  is_active boolean [default: true]
  rate_limit int [default: 60]
  last_used_at timestamp
  expires_at timestamp
  created_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP()`]
  updated_by uuid
  updated_at timestamp [default: `CURRENT_TIMESTAMP()`]
  deleted_by uuid
  deleted_at timestamp

  indexes {
    (key) [name: 'idx_key']
    (is_active) [name: 'idx_is_active']
    (expires_at) [name: 'idx_expires_at']
  }
}

//////////////////////////////////////////////////////////////
// STOCK DOMAIN (Inertia + React module)
//////////////////////////////////////////////////////////////
table sectors {
  id uuid [pk]
  code varchar [unique, note: 'IDX sector code, e.g. BANK, CONSUMER']
  name varchar
  description text
  created_by uuid
  updated_by uuid
  deleted_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  deleted_at timestamp
}

table companies {
  id uuid [pk]
  sector_id uuid [ref: > sectors.id, null]
  name varchar [note: 'Registered/legal company name']
  short_name varchar
  description text
  created_by uuid
  updated_by uuid
  deleted_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  deleted_at timestamp

  indexes {
    (sector_id)
  }
}

// A "stock" is the listed instrument (ticker) on an exchange, kept separate
// from "company" because tickers can change/be reused while the company does not.
table stocks {
  id uuid [pk]
  company_id uuid [ref: > companies.id]
  ticker varchar(10) [note: 'Current active ticker, e.g. BBCA']
  exchange varchar(10) [default: 'IDX']
  board varchar(20) [note: 'Main, Development, Acceleration, etc.']
  currency char(3) [default: 'IDR']
  listed_at date
  is_active boolean [default: true]
  created_by uuid
  updated_by uuid
  deleted_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  deleted_at timestamp

  indexes {
    (exchange, ticker) [unique]
    (company_id)
    (is_active)
  }
}

// Daily OHLCV history. High-volume, append-only fact table — intentionally
// uses a bigint identity PK instead of the UUID convention used by the
// dimension tables above, to keep storage/index size down.
table stock_prices {
  id bigint [pk, increment]
  stock_id uuid [ref: > stocks.id]
  trading_date date
  open decimal(18,2)
  high decimal(18,2)
  low decimal(18,2)
  close decimal(18,2)
  volume bigint [default: 0]
  source varchar(50) [note: 'Data lineage, e.g. seed:dev, provider:idx']
  created_at timestamp [default: `CURRENT_TIMESTAMP`]

  indexes {
    (stock_id, trading_date) [unique]
    (trading_date)
  }
}
