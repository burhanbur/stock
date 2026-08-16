# Stock Domain Module — Architecture Notes

Foundation for the IDX stock recommendation platform, built on top of the
existing Laravel Starter Kit. The app started as a Blade + Metronic RBAC
admin panel; that legacy admin (Users/Roles/Menus/Routes CRUD, the Approval
workflow module, Payment integration, and all Metronic-themed Blade views)
has since been **removed entirely** to avoid maintaining two parallel UI
stacks. The app is now **Inertia.js + React + TypeScript** end to end —
everything under `Http/Controllers/Stocks`, `Actions/Stocks`,
`Http/Resources/Stocks`, `resources/js/Pages/Stocks` follows that stack, and
so does the (now Inertia-based) login page. See `ai/guidelines.md` for the
current coding conventions.

## What exists today

- **Domain tables**: `sectors`, `companies`, `stocks`, `stock_prices` (see
  `ai/erd.md`). `stock` is kept separate from `company` because tickers can
  change over time; `company` is the stable identifier.
- **Historical-first**: no `current_price` column anywhere — everything reads
  from `stock_prices` (daily OHLCV), including "latest price" (via Eloquent's
  `latestOfMany()`), so the schema is ready for time-series analysis and
  backtesting later without a migration.
- **Read path**: `StockController` (thin) → `ListStocksAction` /
  `GetStockDetailAction` (data fetch, still framework-agnostic Eloquent) →
  `Http/Resources/Stocks/*Resource` (explicit shape sent to the frontend,
  never raw Eloquent models) → Inertia → React pages under
  `resources/js/Pages/Stocks`.
- **Derived data kept separate from raw data**: day-over-day change % is
  computed by `App\Support\Stocks\PriceChangeCalculator`, a pure function
  with its own unit tests — not stored, not computed inline in a resource.
- **Auth**: session guard + `User` model reused from the starter kit as-is.
  The login *page* was rebuilt as an Inertia/React page
  (`resources/js/Pages/Auth/Login.tsx`) once the Blade views it used to
  render into were removed, but the underlying auth logic
  (`LoginController::credentials()`, the `identity` field sniffing
  email-vs-username) is untouched. Registration, password reset, and email
  verification were dropped — this is a personal, single-operator app with
  seeded users, not a self-service product.
- **Seed data**: real IDX tickers/companies (`CompanySeeder`), but daily
  prices are a synthetic random walk (`StockPriceSeeder`, `source =
  seed:dev`) — clearly not real market data. The UI shows a "data
  pengembangan" disclaimer for this reason.

## Deliberate deviations from a from-scratch blueprint

- **The legacy RBAC/Approval/Payment admin was deleted, not kept alongside
  the new stack.** Controllers, models, Blade views, migrations (and their
  tables — dropped via `migrate:fresh`), seeders, and the Metronic/Bootstrap
  frontend assets are gone. Packages that existed only to serve that admin
  (`lab404/laravel-impersonate`, `yajra/laravel-datatables-oracle`,
  `realrashid/sweet-alert` in composer; `bootstrap`, `sass`, `@popperjs/core`,
  `axios` in npm) were removed too. Kept: JWT auth, Swagger/API docs,
  Excel/PDF/QR-code packages, and `ApiKey` — general infra not tied to the
  admin UI, potentially useful for a future public API.
- **Laravel 13, not 12** — the starter kit was already on 13; no reason to
  downgrade.
- **`stock_prices` uses a bigint identity PK**, not UUID like every other
  table in this app. It's a high-volume, append-only fact table; the
  storage/index cost of a UUID PK isn't worth it there. Every dimension
  table (`sectors`, `companies`, `stocks`) still follows the app's UUID
  convention.
- **Tests run against a real Postgres database** (`stockrec_test`), not
  SQLite. The starter kit's default `phpunit.xml` pointed at SQLite, but
  this app leans on Postgres-specific behavior (`ILIKE`, numeric precision)
  that SQLite won't reliably reproduce.
- Fixed two latent starter-kit bugs surfaced while adding tests:
  `UserFactory` didn't set `username` and set a `remember_token` column
  that doesn't exist on `users` — both made `User::factory()->create()`
  fail on Postgres. Also added `APP_URL` override in `phpunit.xml` — the
  real `.env` has `APP_URL=http://localhost/stock` for a local path-based
  vhost alias, which broke every `route()`-based HTTP test.

## Explicitly not built yet (by design — see the original blueprint)

Financial/technical metrics, scoring engine, ranking, backtesting, ML,
watchlists/portfolio, a real market data provider integration, and the
import pipeline (`stocks:import` command → job → provider). The
`Actions/Stocks`, `Services/*`, `Jobs/*` structure this module establishes
is meant to absorb those without a rewrite — add them when there's an actual
requirement driving them, not before.

## Running it

- Frontend build needs **Node ≥ 20.19** (Vite 7 / Tailwind v4 requirement).
  This machine's default `node` is 18; use `nvm use 22` first.
- `npm run dev` for local development (Vite dev server + HMR for the React
  pages), `npm run build` for production assets.
- `php artisan test` covers the new module (unit tests for
  `PriceChangeCalculator`, feature tests for both Inertia pages).
