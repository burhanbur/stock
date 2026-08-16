
# Stock Recommendation Platform

Ringkasan singkat: aplikasi analisis dan rekomendasi saham pribadi untuk pasar Indonesia (IDX), dibangun di atas Laravel starter kit dengan **Inertia.js + React + TypeScript** sebagai frontend. Dokumen ini menjelaskan persyaratan, dependensi, struktur folder utama, konvensi penulisan kode, dan langkah-langkah setup & pengembangan.

Untuk arsitektur lebih dalam (layering Controller → Action → Resource → Inertia, konvensi schema, dsb.) lihat `CLAUDE.md` dan `ai/guidelines.md`. Riwayat & alasan keputusan arsitektur ada di `ai/stock-module.md`; skema database (DBML) ada di `ai/erd.md`.

## Isi cepat

- Laravel 13, PHP 8.2+ (dijalankan di 8.3)
- Inertia.js + React + TypeScript + Tailwind v4 (Vite) — satu-satunya frontend, tidak ada Blade admin lagi
- PostgreSQL sebagai database (dev maupun test)
- Dependensi utama di-manage lewat `composer.json` dan `package.json`
- Development tools: `laravel/pint`, `phpunit`, `sail` (opsional)
- Konvensi: PSR-12, Laravel conventions, dan Pint untuk formatting

## Persyaratan Sistem

- PHP 8.2+
- Composer (latest stable)
- Node.js **>= 20.19** & npm (dibutuhkan Vite 7 / Tailwind v4). Kalau `node -v` di mesin kamu masih v18, pakai `nvm use 22` dulu sebelum menjalankan perintah npm/vite manapun.
- PostgreSQL (dev database, dan database test terpisah — lihat bagian Testing)
- Redis (dipakai untuk cache)

## Instalasi (cepat)

1. Clone repository
2. Install PHP dependencies

```bash
composer install --prefer-dist --no-interaction
```

3. Install JS dependencies (pastikan Node >= 20.19 aktif)

```bash
npm install
```

4. Siapkan environment

```bash
cp .env.example .env
php artisan key:generate
```

Buat database PostgreSQL untuk development, lalu sesuaikan `DB_*` di `.env`:

```bash
createdb stockrec
php artisan migrate --force
php artisan db:seed
```

5. Jalankan dev server dan proses pendukung (opsional)

```bash
# menjalankan server, queue, logs watcher dan vite (lihat script "dev" di composer.json)
composer run-script dev
```

Atau secara manual:

```bash
php artisan serve
npm run dev
```

## Script composer penting

- `composer run-script dev` — menjalankan development supervisor (server, queue listener, log watcher, vite) — lihat `composer.json` untuk perintah lengkap
- `composer test` — jalankan test suite via `php artisan test`

Daftar script lengkap ada di `composer.json`.

## Dependensi (ringkasan)

Composer, production (ringkasan dari `composer.json`):

- `laravel/framework` ^13.0
- `inertiajs/inertia-laravel`, `tightenco/ziggy` — jembatan Inertia + helper `route()` di sisi JS
- `laravel/ui` — scaffolding auth (login)
- `barryvdh/laravel-dompdf` — generate PDF
- `darkaonline/l5-swagger`, `zircote/swagger-php` — API docs (belum ada endpoint publik yang dipakai)
- `kra8/laravel-snowflake` — ID generator (tidak aktif dipakai; alternatif dari UUID)
- `maatwebsite/excel` — Excel import/export
- `predis/predis` — Redis client
- `simplesoftwareio/simple-qrcode` — QR code generator
- `tymon/jwt-auth` — JWT auth (disiapkan untuk API publik di masa depan, belum ada route yang memakainya)

Composer, development:
- `barryvdh/laravel-debugbar` — debugbar
- `fakerphp/faker` — test data
- `laravel/pint` — code formatter (Pint)
- `phpunit/phpunit` — unit/integration tests
- `nunomaduro/collision` — error handler for CLI
- `laravel/sail` — optional, docker local dev

npm:
- `react`, `react-dom`, `@inertiajs/react` — frontend
- `typescript`, `@types/react`, `@types/react-dom` — TypeScript
- `tailwindcss`, `@tailwindcss/vite` — styling
- `ziggy-js` — companion untuk `tightenco/ziggy`
- `laravel-vite-plugin`, `@vitejs/plugin-react`, `vite` — build tooling

Untuk daftar versi lengkap lihat `composer.json` dan `package.json`.

## Autoload & Helpers

File helper yang di-autoload di `composer.json`:
- `app/Helpers/Utilities.php`
- `app/Helpers/Curl.php`

Namespace PSR-4:

- `App\` => `app/`
- `Database\Factories\` => `database/factories/`
- `Database\Seeders\` => `database/seeders/`

## Struktur folder utama (ringkasan dan peran)

Root project mengikuti konvensi Laravel. Beberapa folder penting:

- `app/` — kode aplikasi
	- `app/Http/Controllers/` — controllers (thin — lihat `CLAUDE.md` untuk layering-nya)
	- `app/Actions/` — satu class per use case (mis. `Actions/Stocks/ListStocksAction`)
	- `app/Http/Resources/` — bentuk data eksplisit yang dikirim ke Inertia/React
	- `app/Support/` — pure calculation logic (mis. `Support/Stocks/PriceChangeCalculator`)
	- `app/Models/` — Eloquent models
	- `app/Providers/`, `app/Traits/`, `app/Utilities/`, `app/Helpers/` — infrastruktur umum
- `bootstrap/` — bootstrap app & cached providers
- `config/` — konfigurasi aplikasi
- `database/` — migrations, factories, seeders
- `public/` — web entry point dan hasil build Vite (`public/build`)
- `resources/`
	- `resources/views/app.blade.php` — satu-satunya root Blade template (dipakai Inertia)
	- `resources/js/Pages/` — halaman React per domain (mis. `Pages/Stocks/Index.tsx`)
	- `resources/js/Components/`, `resources/js/Layouts/` — komponen & shell UI yang dipakai ulang
	- `resources/js/types/` — TypeScript types
	- `resources/css/app.css` — entry Tailwind
- `routes/` — `web.php`, `api.php` (masih kosong), `console.php`
- `tests/` — unit & feature tests

## Konvensi Penulisan Kode (Code Style)

1. PSR-12 sebagai baseline style.
2. Gunakan `laravel/pint` untuk formatting otomatis. Jalankan sebelum commit:

```bash
./vendor/bin/pint --ansi
```

atau tambahkan ke pre-commit hook (opsional).

3. Naming conventions

- Controller: `PascalCase` diakhiri `Controller` (mis. `StockController`)
- Action: `PascalCase` menjelaskan use case (mis. `ListStocksAction`)
- Model: `PascalCase` (mis. `Stock`)
- Database migration: snake_case timestamp prefix (Laravel default)
- Route names: dot.notation (mis. `stocks.index`)
- React page/komponen: `PascalCase.tsx`, mengikuti nama route/domain

4. Requests & Validation

- Gunakan Form Request classes yang extend `App\Http\Requests\BaseFormRequest` untuk validasi input, dengan pesan error berbahasa Indonesia.

5. Dependency injection

- Inject services/actions via constructor atau method injection pada controller.

6. Exception handling & API responses

- Untuk API gunakan trait `ApiResponse` (tersedia di `app/Traits/ApiResponse.php`) agar format konsisten.

7. Tests

- Tulis unit test untuk pure logic (kalkulasi, dsb.) dan feature test untuk halaman Inertia menggunakan `Inertia\Testing\AssertableInertia`.

## Cara menjalankan test

Test suite jalan melawan **PostgreSQL sungguhan**, bukan SQLite — buat database test sekali di awal:

```bash
createdb stockrec_test
```

Lalu:

```bash
composer test
```

Atau langsung:

```bash
php artisan test
# satu file/test tertentu:
php artisan test tests/Feature/Stocks/StockIndexTest.php
php artisan test --filter=nama_method_test
```

## API Documentation

Repo ini menyertakan paket Swagger (`darkaonline/l5-swagger` dan `zircote/swagger-php`), disiapkan untuk API publik di masa depan — belum ada endpoint yang aktif memakainya (`routes/api.php` masih kosong).

## Tips Pengembangan

- Gunakan feature branches dan PR untuk perubahan besar.
- Gunakan seeder dan factories untuk membuat data uji yang konsisten.
- Simpan secrets di `.env` dan jangan commit file `.env` ke git.
- Jalankan `composer dump-autoload` bila menambahkan helper atau autoload baru.
- Setiap perubahan skema database, update juga `ai/erd.md`.

## Troubleshooting umum

- Kalau `npm run dev`/`npm run build` gagal dengan error terkait Node version: pastikan `node -v` >= 20.19 (`nvm use 22`).
- Kalau migrasi gagal: pastikan PostgreSQL jalan dan `DB_*` di `.env` sudah benar (database harus sudah dibuat lebih dulu, Laravel tidak membuatkannya otomatis).
- Kalau assets Vite tidak muncul saat development: jalankan `npm run dev` dan periksa console browser untuk error; untuk production pastikan `npm run build` sudah dijalankan (`public/build/manifest.json` harus ada).

## Kontribusi

Jika ingin berkontribusi: fork repo, buat branch baru, jalankan tests dan pastikan Pint lulus formatting. Kirim PR dengan deskripsi perubahan dan testing steps.

## Lisensi

MIT

## Kontak

Jika butuh bantuan spesifik, sertakan langkah yang sudah dicoba dan error message yang muncul.
