<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListStocksAction
{
    /**
     * @param  array{search?: ?string, sector_id?: ?string, sort?: ?string, per_page?: ?int}  $filters
     */
    public function execute(array $filters): LengthAwarePaginator
    {
        $sort = $filters['sort'] ?? 'ticker';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $column = in_array($column, ['ticker', 'created_at'], true) ? $column : 'ticker';

        return Stock::query()
            ->with(['company.sector', 'latestPrice'])
            ->search($filters['search'] ?? null)
            ->inSector($filters['sector_id'] ?? null)
            ->orderBy($column, $direction)
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();
    }
}
