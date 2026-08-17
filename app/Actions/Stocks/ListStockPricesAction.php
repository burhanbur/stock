<?php

namespace App\Actions\Stocks;

use App\Models\StockPrice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListStockPricesAction
{
    private const SORTABLE_COLUMNS = ['trading_date', 'open', 'high', 'low', 'close', 'volume'];

    public function execute(string $stockId, ?string $sort = null, int $perPage = 15): LengthAwarePaginator
    {
        $sort ??= '-trading_date';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $column = in_array($column, self::SORTABLE_COLUMNS, true) ? $column : 'trading_date';

        return StockPrice::query()
            ->where('stock_id', $stockId)
            ->orderBy($column, $direction)
            ->paginate($perPage, pageName: 'price_page')
            ->withQueryString();
    }
}
