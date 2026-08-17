<?php

namespace App\Http\Requests\Stocks;

use App\Http\Requests\BaseFormRequest;

class ListStocksRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'sector_id' => ['nullable', 'string', 'exists:sectors,id'],
            'sort' => ['nullable', 'string', 'in:ticker,-ticker,company_name,-company_name,latest_close,-latest_close,created_at,-created_at'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'watchlist_only' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.max' => 'Kata kunci pencarian maksimal 100 karakter',
            'sector_id.exists' => 'Sektor tidak ditemukan',
            'sort.in' => 'Urutan tidak valid',
            'per_page.max' => 'Maksimal 100 data per halaman',
        ];
    }

    /**
     * Always returns every key (never the bare `$this->only(...)` result) —
     * when none of these query params are present, `only()` returns `[]`,
     * which `json_encode`s as a JSON *array* (`[]`) instead of an object
     * (`{}`). On the frontend that turns the `filters` prop into a JS
     * array, where e.g. `filters.sort` silently resolves to
     * `Array.prototype.sort` (a function, always truthy) instead of
     * `undefined` — a real bug this shape avoids entirely.
     *
     * @return array{search: ?string, sector_id: ?string, sort: ?string, per_page: ?int, watchlist_only: bool}
     */
    public function filters(): array
    {
        return [
            'search' => $this->input('search'),
            'sector_id' => $this->input('sector_id'),
            'sort' => $this->input('sort'),
            'per_page' => $this->input('per_page'),
            'watchlist_only' => $this->boolean('watchlist_only'),
        ];
    }
}
