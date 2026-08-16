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
            'sort' => ['nullable', 'string', 'in:ticker,-ticker,created_at,-created_at'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
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
     * @return array{search: ?string, sector_id: ?string, sort: ?string, per_page: ?int}
     */
    public function filters(): array
    {
        return $this->only(['search', 'sector_id', 'sort', 'per_page']);
    }
}
