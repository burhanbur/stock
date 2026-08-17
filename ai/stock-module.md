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
- **Public JSON API** (`routes/api.php`, `/api/v1/stocks`, `/api/v1/stocks/{ticker}`):
  read-only, gated by the pre-existing `api.key` middleware
  (`X-API-KEY` header, checked against `App\Models\ApiKey`). Reuses the exact
  same `Actions/Stocks/*` and `Http/Resources/Stocks/*` as the Inertia pages —
  the API controller (`Http/Controllers/Api/Stocks/StockController`) is a
  thin second consumer of the same domain layer, not a parallel
  implementation. Responses go through `App\Traits\ApiResponse` (standard
  `{success, message, data, pagination?}` envelope, already used elsewhere in
  the starter kit) and `transform.response.keys` middleware (camelCases
  response keys — the Inertia side stays snake_case, matching Laravel/PHP
  convention, since that's a different consumer). Generate a key with
  `php artisan api:generate-key "name" --permissions=stocks.read`.

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
- Fixed a real, currently-reachable bug in the pre-existing
  `App\Http\Middleware\ValidateApiKey`: it cached the `ApiKey` **Eloquent
  model** in Redis via `Cache::remember()`. Round-tripping a serialized
  object through this app's Redis setup (Predis) corrupts it —
  `unserialize()` comes back as `__PHP_Incomplete_Class`, so every second
  request with the same key within the 5-minute cache window 500'd. Fixed
  by caching `$model->getAttributes()` (a plain array) instead and
  rehydrating via `(new ApiKey())->setRawAttributes($attributes, true)` on
  a cache hit — never cache an Eloquent model object through this app's
  cache store, only plain arrays/scalars.

## Recommendation scoring (v1 — technical only)

`Stocks/Show` computes a live recommendation from price history alone (no
fundamentals): `App\Support\Stocks\MomentumScoreCalculator` (SMA20/SMA50
trend + 20-day return) and `App\Support\Stocks\RiskScoreCalculator`
(annualized volatility from daily returns) each produce a 0-100 score, pure
and unit-tested the same way as `PriceChangeCalculator`.
`RecommendationScoreCalculator::combine()` weighs them (70% momentum, 30%
risk) into a single Beli/Tahan/Jual label, assembled in
`StockDetailResource` and rendered by `Components/RecommendationCard.tsx`.
Both calculators return `null`/"Data belum cukup" below their minimum
history requirement (50 and 31 closing prices respectively) instead of
guessing. The Learning glossary's `momentum` and `rata-rata-bergerak` terms
link directly from the card.

This is deliberately **not** the five-component fundamental+valuation+risk
engine the glossary/lessons already reference (ROE in Module 5, Dividend
Yield in Module 6, full Risk Score in Module 13) — that needs financial
statement data (`ai/erd.md` has no such tables yet) this app doesn't fetch.
Treat this as the first, price-data-only slice of that eventual engine, not
a replacement for it.

**Data quality caveat**: `stocks:sync-prices` deletes any leftover
`source = seed:dev` rows for a stock once real data is synced for it —
mixing synthetic and real prices on different scales silently wrecked the
volatility calculation (one stock briefly scored 277% annualized
volatility because a few synthetic-era rows Yahoo didn't return data for
survived the upsert). If a future provider integration has similar gaps,
re-check this before trusting the risk score.

The list page (`Stocks/Index`) shows the same recommendation label per row,
computed the same way via the shared `App\Support\Stocks\StockRecommendationBuilder`
(used by both `StockListResource` and `StockDetailResource` so the two
never compute it differently) — `ListStocksAction` batch-loads ~6 months of
prices for every stock on the page in one query instead of one query per
row.

**Scheduled sync**: `routes/console.php` runs `stocks:sync-prices` daily at
17:00 WIB (`Schedule::command(...)->dailyAt(...)->timezone(...)`) so prices
stay fresh without the manual "Sync Data" button — that requires
`php artisan schedule:work` (or a real cron entry calling
`schedule:run` every minute) actually running; it does nothing on its own
just by existing in the route file.

## Watchlist

`watchlists` (migration + `App\Models\Watchlist`) is a per-user join table,
same shape as `learning_progress` (UUID PK, unique `(user_id, stock_id)`,
no soft deletes — toggling is a hard add/remove) rather than the
dimension-table audit-column convention. `App\Actions\Stocks\ToggleWatchlistAction`
flips membership; `POST /stocks/{ticker}/watchlist`
(`StockController::toggleWatchlist`) is the only entry point. `ListStocksAction`
and `GetStockDetailAction` both take an optional `$userId` to attach
`is_watchlisted` per stock (batch-loaded for the list, a single `exists()`
check for the detail page) — `null` when there's no authenticated user
(there always is one here, since every stock route sits behind `auth`
middleware, but the parameter stays optional so the actions don't hard-require
a request context). The list page also accepts `?watchlist_only=1`.

**Frontend gotcha hit here**: `ListStocksRequest::filters()` must never
return `$this->only([...])` directly — when none of those query params are
present, PHP's `only()` returns `[]`, which `json_encode`s as a JSON array,
not an object. On the frontend that turns `filters` into a JS array, where
`filters.sort` silently resolves to `Array.prototype.sort` (a function,
always truthy) instead of `undefined`, corrupting the next request's query
string. Always return a fully-keyed array so it serializes as an object.
Separately, never combine a default Tailwind color class with a
conditionally-applied one for the same CSS property (e.g.
`` `text-slate-300 ${active ? 'text-amber-400' : ''}` ``) — Tailwind's
generated stylesheet order, not JSX class-string order, decides which one
wins, so the "conditional" class can silently lose. Pick one class per
branch instead (see `Components/WatchlistButton.tsx`).

## Explicitly not built yet (by design — see the original blueprint)

Fundamental/valuation metrics (need financial statement data), ranking
beyond a per-stock score, backtesting, ML, portfolio tracking (P&L, cost
basis), and the import pipeline (`stocks:import` command → job → provider)
for anything beyond the Yahoo Finance sync (`stocks:sync-prices` /
`POST /stocks/sync-prices`, see `App\Actions\Stocks\SyncStockPricesAction`
and `FetchYahooFinancePricesAction`) — that sync is unofficial/best-effort,
not a real market data provider integration. The `Actions/Stocks`,
`Services/*`, `Jobs/*` structure this module establishes is meant to absorb
the rest without a rewrite — add them when there's an actual requirement
driving them, not before.

## Running it

- Frontend build needs **Node ≥ 20.19** (Vite 7 / Tailwind v4 requirement).
  This machine's default `node` is 18; use `nvm use 22` first.
- `npm run dev` for local development (Vite dev server + HMR for the React
  pages), `npm run build` for production assets.
- `php artisan test` covers the new module (unit tests for
  `PriceChangeCalculator`, feature tests for both Inertia pages).
