# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A stock analysis/recommendation platform for the Indonesian market (IDX),
built on a Laravel starter kit. The kit originally shipped a Blade +
Metronic RBAC admin panel (users/roles/menus, an approval-workflow engine,
a payment integration); that legacy admin has been **removed entirely** —
the app is now **Inertia.js + React + TypeScript** end to end, including
login. See `ai/stock-module.md` for the full history/rationale and
`ai/guidelines.md` for coding conventions.

Stack: Laravel 13, PHP 8.3+, PostgreSQL, Redis (cache; queue currently
`database`), Vite, Tailwind v4. `ai/erd.md` is a DBML entity diagram kept in
sync with migrations — **update it whenever a migration changes the
schema**.

## Commands

```bash
# Install
composer install
npm install                    # requires Node >= 20.19 (Vite 7 / Tailwind v4).
                                # This machine's default `node` is v18 — use
                                # `nvm use 22` (already installed) before any npm/vite command.

# Dev (server + queue listener + log watcher + vite, all at once)
composer run dev
# ...or individually:
php artisan serve
npm run dev

# Build frontend for production
npm run build

# Tests (whole suite)
php artisan test
composer test                  # same, plus `config:clear` first
# Single test file / method
php artisan test tests/Feature/Stocks/StockIndexTest.php
php artisan test --filter=test_it_lists_stocks_with_their_latest_price

# Formatting
./vendor/bin/pint              # PHP, PSR-12 baseline — run before committing
npx tsc --noEmit                # TypeScript type-check (no build)

# DB
php artisan migrate
php artisan db:seed            # re-runnable: every seeder deletes-then-recreates its own table(s)
```

### Test database

`phpunit.xml` points tests at a **real Postgres database** (`stockrec_test`),
not SQLite, because the app relies on Postgres-specific behavior (e.g.
`ILIKE`, numeric precision) that SQLite won't reproduce. Create it once with
`createdb stockrec_test` (or via `psql`) before running tests. `phpunit.xml`
also overrides `APP_URL` to `http://localhost` — the real `.env` has
`APP_URL=http://localhost/stock` for a local path-based vhost alias, which
breaks `route()`-generated URLs inside the test HTTP client if not
overridden.

**Never run `php artisan migrate:fresh` (or similar) with an explicit
`--database=`/`--env=` flag and expect it to target the test database** —
without an actual `.env.testing` file, those flags don't switch
`DB_DATABASE`, so the command silently runs against the dev database
(`stockrec`) instead. The test suite migrates `stockrec_test` on its own via
`RefreshDatabase` in each Feature test class; you don't need to migrate it
by hand. If you do need to reset it manually, prefix the env var:
`DB_DATABASE=stockrec_test php artisan migrate:fresh`.

## Architecture

Backend layering: thin `Http/Controllers/*` → `Actions/*` (single-use-case
classes, framework-agnostic data fetching, e.g. `ListStocksAction`) →
`Http/Resources/*` (explicit response shape — **never** pass raw Eloquent
models to `Inertia::render`) → React page in `resources/js/Pages/*`. Shared
UI in `resources/js/Components/**`, page shell in
`resources/js/Layouts/AppLayout.tsx`. Root Blade template is
`resources/views/app.blade.php` (the only Blade view left in the app).

### Inertia + Resource prop-serialization gotcha

When a `JsonResource`/`ResourceCollection` is passed as an Inertia prop
value, Inertia resolves it via `toResponse()`, which wraps single resources
and resource collections in `{"data": ...}` — **including resources nested
inside another resource's `toArray()`**, not just top-level ones. To get a
predictable, undecorated shape on the frontend, call `->resolve()`
explicitly wherever a resource or nested resource is embedded (see
`StockController` and `StockDetailResource` for the pattern). For paginated
data, prefer `$paginator->through(fn ($m) => (new XResource($m))->resolve())`
and pass the paginator itself — its native `toArray()`/`jsonSerialize()`
(current_page, data, links, per_page, total, …) is more reliable through
Inertia's pipeline than wrapping it in a Resource collection.

### Database convention: two PK styles by table role

- **Dimension/reference tables** (`users`, `sectors`, `companies`, `stocks`,
  `api_keys`): UUID primary key (`$table->char('id', 36)->primary()`,
  generated via the `uuidv7()` helper in the model's `creating` event), plus
  `created_by`/`updated_by`/`deleted_by` (UUID, nullable) and `SoftDeletes`.
- **High-volume, append-only fact tables** (`stock_prices`): bigint identity
  PK instead, no audit columns, no soft deletes — a `source` column carries
  data lineage instead (e.g. `seed:dev`). See `ai/stock-module.md` for the
  rationale.

### Auth

Session-guard login only (`App\Http\Controllers\Auth\LoginController`) — no
self-registration/password-reset UI; this is a personal, single-operator
app with seeded users (`database/seeders/UserSeeder.php`). The login form
field is named `identity`, not `email`/`username` — `credentials()` sniffs
whether the value is an email or a username. `showLoginForm()` returns
`Inertia::render('Auth/Login')`. A separate JWT guard (`tymon/jwt-auth`)
exists for a possible future public API but nothing uses it yet —
`routes/api.php` is currently empty.

### Testing conventions

Feature tests for Inertia pages use `Inertia\Testing\AssertableInertia` via
`$response->assertInertia(fn (Assert $page) => $page->component(...)->where(...))`.
Pure calculation logic (e.g. `App\Support\Stocks\PriceChangeCalculator`)
gets its own deterministic unit tests with known input/output pairs rather
than only being exercised indirectly through a Feature test.
