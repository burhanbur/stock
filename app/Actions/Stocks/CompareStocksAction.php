<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompareStocksAction
{
    public const MIN_STOCKS = 2;

    public const MAX_STOCKS = 3;

    public function __construct(private GetStockDetailAction $detailAction) {}

    /**
     * @param  array<int, string>  $tickers
     * @return array{stocks: array<int, Stock>, not_found: array<int, string>}
     */
    public function execute(array $tickers, ?string $userId): array
    {
        $tickers = array_slice(array_values(array_unique(array_map('strtoupper', $tickers))), 0, self::MAX_STOCKS);

        $stocks = [];
        $notFound = [];

        foreach ($tickers as $ticker) {
            try {
                $stocks[] = $this->detailAction->execute($ticker, $userId);
            } catch (ModelNotFoundException) {
                $notFound[] = $ticker;
            }
        }

        return ['stocks' => $stocks, 'not_found' => $notFound];
    }
}
