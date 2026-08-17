# Stock Recommendation Platform — AI Agent Guidelines

This document outlines the coding standards, architectural patterns, and development workflow for the AI agent to follow when contributing to this codebase. By strictly adhering to these guidelines, the AI agent ensures consistency, predictability, and maintainability.

The app was originally a Blade + Metronic RBAC admin starter kit. That legacy admin panel (Users/Roles/Menus/Routes management, the Approval workflow module, Payment integration, and all Metronic-themed views) has been removed — the app is now Laravel + **Inertia.js + React + TypeScript** end to end. See `ai/stock-module.md` for the full rationale and history.

## 1. Architectural Patterns

- **Framework:** Laravel, with Inertia.js as the primary bridge to the frontend, plus a small read-only public JSON API (`/api/v1/*`) for external consumers — see §6.
- **Design pattern:** Thin controller → Action (single-use-case class, framework-agnostic data fetching) → `Http/Resources/*` (explicit response shape) → `Inertia::render()` → React page. Never pass raw Eloquent models as Inertia props.
- **Query strategy:** Direct Eloquent, no repository pattern. Query scopes on the model (e.g. `Stock::scopeSearch()`) for reusable filtering logic.
- **Frontend:** React + TypeScript, Tailwind v4. Pages live in `resources/js/Pages/**`, shared UI in `resources/js/Components/**`, page shells in `resources/js/Layouts/**`.
- **Language:** Code (identifiers, comments) is English. User-facing strings (labels, flash messages, validation errors) are Indonesian.

## 2. Database & Schema Design

Two conventions coexist, chosen per table's role:

- **Dimension/reference tables** (`users`, `sectors`, `companies`, `stocks`, `api_keys`): UUID primary key (`$table->char('id', 36)->primary()`, generated via the `uuidv7()` helper in the model's `creating` event), plus audit columns `created_by`/`updated_by`/`deleted_by` (UUID, nullable) and `SoftDeletes`.
- **High-volume, append-only fact tables** (`stock_prices`): bigint identity primary key instead — the UUID/audit-column overhead isn't worth it for time-series data that's ingested, not user-edited. No `SoftDeletes`; use a `source` column instead for data lineage (see §7 below).

Foreign keys reference the `id` of related tables and are always indexed. Financial numbers use `decimal`, never floating point.

## 3. Controllers

- Naming: PascalCase ending in `Controller`, under `Http/Controllers/{Domain}/`.
- Keep them thin: validate via a Form Request → call an Action → return `Inertia::render()`. No business logic inline.
- Any resource embedded inside another resource's `toArray()` (e.g. a nested `SectorResource` inside `StockDetailResource`) must be explicitly `->resolve()`d before being returned. Inertia resolves props via each prop's `toResponse()`, which wraps `JsonResource`/`ResourceCollection` output in `{"data": ...}` — including resources nested arbitrarily deep, not just top-level ones. Calling `->resolve()` yourself sidesteps that wrapping and keeps the shape predictable on the TypeScript side. For paginated data, prefer `$paginator->through(fn ($m) => (new XResource($m))->resolve())` and pass the paginator itself, rather than wrapping it in a Resource collection — its native `toArray()` (current_page, data, links, per_page, total, …) survives Inertia's prop resolution reliably.

## 4. Actions

`Actions/{Domain}/*` — one class per application use case (`ListStocksAction`, `GetStockDetailAction`), a single `execute()` method, framework-agnostic (works with Eloquent directly, no HTTP concerns). Only introduce a `Services/*` class when logic is genuinely reused across multiple actions — don't create a service just to have one.

## 5. Form Requests (Validation)

- Location: `Http/Requests/{Domain}/{Action}Request.php`.
- Extend `App\Http\Requests\BaseFormRequest`, not the bare Laravel `FormRequest` — it flashes a Session `notification` and logs on failure, matching the app's error-handling convention.
- Implement `messages()` with Indonesian text.

## 6. Routes

- `routes/web.php` — Inertia pages, grouped under the `auth` middleware. Naming: dot-notation matching the domain (`stocks.index`, `stocks.show`).
- `routes/api.php` — read-only public JSON API (`/api/v1/*`), gated by `api.key:<permission>` middleware (`X-API-KEY` header, checked against `App\Models\ApiKey`; generate one with `php artisan api:generate-key "name" --permissions=<permission>`). Wrapped in the `transform.response.keys` middleware, which camelCases response keys — the API's JSON convention, distinct from the Inertia side's snake_case. An API controller under `Http/Controllers/Api/{Domain}/` must reuse the same `Actions/*` and `Http/Resources/*` as the Inertia controller for that domain, and return responses via `App\Traits\ApiResponse` (`successResponse()`/`errorResponse()`) for the standard `{success, message, data, pagination?}` envelope — never a bespoke shape.

**Never cache an Eloquent model object** (`Cache::remember($key, $ttl, fn () => Model::find(...))`). Round-tripping a serialized model through this app's Redis/Predis setup corrupts it (`unserialize()` returns `__PHP_Incomplete_Class`). Cache `$model->getAttributes()` (a plain array) and rehydrate with `(new Model())->setRawAttributes($attributes, true)` on a cache hit instead — see `ValidateApiKey` middleware for the pattern.

## 7. Data Integrity & Traceability

Because this app analyzes financial data:

- Never silently overwrite historical price data without understanding the source.
- Distinguish raw/ingested values (`stock_prices.source`, e.g. `seed:dev`) from derived metrics (e.g. `App\Support\Stocks\PriceChangeCalculator`) — derived values are computed on read, not stored, and live in pure, unit-tested classes under `Support/{Domain}/`.
- Seed/demo data must be clearly marked as such (`source: seed:dev`) and the UI must disclose when data is synthetic, never presenting fabricated data as real market information.

## 8. Testing

- `php artisan test` (or `composer test`). Tests run against a **real Postgres database** (`stockrec_test`, see `phpunit.xml`), not SQLite — the app relies on Postgres-specific behavior (`ILIKE`, numeric precision).
- Feature tests for Inertia pages use `Inertia\Testing\AssertableInertia` (`->component()`, `->where()`, `->has()`) via `$response->assertInertia(...)`.
- Pure calculation logic (e.g. `PriceChangeCalculator`) gets deterministic unit tests with known input/output pairs — don't only test it indirectly through a Feature test.

## 9. Authentication

Session-guard login only (no self-registration/password-reset UI at present — this is a personal-use app with seeded users). The login form field is named `identity`, not `email`/`username`; `LoginController::credentials()` sniffs whether the value is an email or a username. `LoginController::showLoginForm()` returns `Inertia::render('Auth/Login')`.

---
**Summary for AI execution:** Read context → write/extend a Form Request → write the Action → write the Controller (thin, Resource-shaped, `->resolve()`d props) → write the React page → write tests (unit for pure logic, feature for the Inertia page) → run `php artisan test` and `npx tsc --noEmit` before considering it done.
