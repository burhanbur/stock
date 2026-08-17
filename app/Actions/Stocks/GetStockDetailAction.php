<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use App\Models\Watchlist;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetStockDetailAction
{
    /**
     * @param  int  $historyDays  How many trading days of history to load.
     */
    public function execute(string $ticker, ?string $userId = null, int $historyDays = 90): Stock
    {
        /** @var Stock|null $stock */
        $stock = Stock::query()
            ->with(['company.sector'])
            ->where('ticker', strtoupper($ticker))
            ->first();

        if (! $stock) {
            throw new ModelNotFoundException("Stock [{$ticker}] not found.");
        }

        $stock->setRelation(
            'recentPrices',
            $stock->prices()
                ->orderByDesc('trading_date')
                ->limit($historyDays)
                ->get()
                ->sortBy('trading_date')
                ->values(),
        );

        $stock->is_watchlisted = $userId
            ? Watchlist::query()->where('user_id', $userId)->where('stock_id', $stock->id)->exists()
            : false;

        return $stock;
    }
}
