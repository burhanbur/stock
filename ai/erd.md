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

// A user's personal shortlist of stocks to track. Per-user preference join
// table — same shape as learning_progress (UUID PK, hard unique pair,
// no soft deletes) rather than the dimension-table audit-column convention,
// since toggling membership is a hard add/remove, not something with history.
table watchlists {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  stock_id uuid [ref: > stocks.id]
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]

  indexes {
    (user_id, stock_id) [unique]
  }
}

//////////////////////////////////////////////////////////////
// LEARNING DOMAIN (Stock Learning Center)
//////////////////////////////////////////////////////////////
table learning_modules {
  id uuid [pk]
  order smallint [note: 'Curriculum sequence, e.g. Module 01 = 1']
  slug varchar [unique]
  level varchar(20) [note: 'ModuleLevel enum: beginner, intermediate, advanced, quant']
  title varchar
  description text
  created_by uuid
  updated_by uuid
  deleted_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  deleted_at timestamp

  indexes {
    (order)
  }
}

// Prerequisites are intentionally implicit: a lesson requires the previous
// `order` within its module to be completed, and a module requires the
// previous module (by `order`) to be fully completed — not a general
// prerequisite graph. See ai/learning-module.md.
table learning_lessons {
  id uuid [pk]
  module_id uuid [ref: > learning_modules.id]
  order smallint
  slug varchar [unique]
  title varchar
  estimated_minutes smallint [default: 10]
  learning_objectives json [note: 'Array of strings']
  key_terms json [note: 'Array of learning_glossary_terms.slug referenced by this lesson']
  content longtext [note: 'Lesson body, Markdown']
  summary text
  created_by uuid
  updated_by uuid
  deleted_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  deleted_at timestamp

  indexes {
    (module_id, order) [unique]
  }
}

table learning_quizzes {
  id uuid [pk]
  lesson_id uuid [ref: > learning_lessons.id]
  title varchar
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
}

table learning_questions {
  id uuid [pk]
  quiz_id uuid [ref: > learning_quizzes.id]
  order smallint
  type varchar(20) [default: 'multiple_choice', note: 'QuestionType enum']
  question text
  explanation text [note: 'Shown after answering, correct or not']
  difficulty varchar(10) [note: 'easy, medium, hard']
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
}

table learning_question_options {
  id uuid [pk]
  question_id uuid [ref: > learning_questions.id]
  order smallint
  text varchar
  is_correct boolean [default: false]
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
}

table learning_glossary_terms {
  id uuid [pk]
  slug varchar [unique]
  term varchar
  full_name varchar [note: 'e.g. "Return on Equity" for the term "ROE"']
  simple_definition text
  formal_definition text
  example text
  application_usage text [note: '"Why this matters to our system" section']
  related_term_slugs json
  created_by uuid
  updated_by uuid
  deleted_by uuid
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  deleted_at timestamp
}

table learning_progress {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  lesson_id uuid [ref: > learning_lessons.id]
  status varchar(20) [default: 'in_progress', note: 'ProgressStatus enum']
  started_at timestamp
  completed_at timestamp
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]

  indexes {
    (user_id, lesson_id) [unique]
  }
}

table learning_quiz_attempts {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  quiz_id uuid [ref: > learning_quizzes.id]
  total_questions smallint
  correct_answers smallint
  score decimal(5,2) [note: 'Percentage, 0-100']
  answers json [note: 'Map of question_id => selected_option_id']
  attempted_at timestamp
  created_at timestamp [default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]

  indexes {
    (user_id, quiz_id)
  }
}
