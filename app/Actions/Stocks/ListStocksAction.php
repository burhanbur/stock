<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use App\Models\StockPrice;
use App\Models\Watchlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListStocksAction
{
    /**
     * @param  array{search?: ?string, sector_id?: ?string, sort?: ?string, per_page?: ?int, watchlist_only?: ?bool}  $filters
     */
    public function execute(array $filters, ?string $userId = null): LengthAwarePaginator
    {
        $sort = $filters['sort'] ?? 'ticker';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $column = in_array($column, ['ticker', 'company_name', 'latest_close', 'created_at'], true) ? $column : 'ticker';

        $query = Stock::query()
            ->with(['company.sector', 'latestPrice'])
            ->search($filters['search'] ?? null)
            ->inSector($filters['sector_id'] ?? null);

        if ($userId && ! empty($filters['watchlist_only'])) {
            $query->whereHas('watchlists', fn ($q) => $q->where('user_id', $userId));
        }

        $this->applySort($query, $column, $direction);

        $stocks = $query
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        $this->attachRecommendationInputs($stocks->getCollection());
        $this->attachWatchlistStatus($stocks->getCollection(), $userId);

        return $stocks;
    }

    /**
     * `company_name`/`latest_close` aren't columns on `stocks`, so sorting
     * by them needs a join (company name) or a correlated subquery (latest
     * close, via the same "latest of many" idea as the `latestPrice`
     * relation) instead of a plain `orderBy`.
     */
    private function applySort(Builder $query, string $column, string $direction): void
    {
        match ($column) {
            'company_name' => $query
                ->join('companies', 'companies.id', '=', 'stocks.company_id')
                ->orderBy('companies.name', $direction)
                ->select('stocks.*'),
            'latest_close' => $query->orderBy(
                StockPrice::select('close')
                    ->whereColumn('stock_id', 'stocks.id')
                    ->orderByDesc('trading_date')
                    ->limit(1),
                $direction
            ),
            default => $query->orderBy($column, $direction),
        };
    }

    /**
     * Batch-loads ~6 months of prices for every stock on the page in a
     * single query (grouped in PHP) instead of one query per stock —
     * enough history for the momentum/risk calculators.
     */
    private function attachRecommendationInputs(Collection $stocks): void
    {
        $stockIds = $stocks->pluck('id');

        $pricesByStock = StockPrice::query()
            ->whereIn('stock_id', $stockIds)
            ->where('trading_date', '>=', now()->subMonths(6))
            ->orderBy('trading_date')
            ->get()
            ->groupBy('stock_id');

        $stocks->each(function (Stock $stock) use ($pricesByStock) {
            $stock->setRelation('recentPrices', $pricesByStock->get($stock->id, collect()));
        });
    }

    private function attachWatchlistStatus(Collection $stocks, ?string $userId): void
    {
        $watchlistedIds = $userId
            ? Watchlist::query()->where('user_id', $userId)->whereIn('stock_id', $stocks->pluck('id'))->pluck('stock_id')->all()
            : [];

        $stocks->each(function (Stock $stock) use ($watchlistedIds) {
            $stock->is_watchlisted = in_array($stock->id, $watchlistedIds, true);
        });
    }
}
