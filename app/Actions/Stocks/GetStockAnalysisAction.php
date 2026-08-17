<?php

namespace App\Actions\Stocks;

use App\Models\StockPrice;
use App\Support\Stocks\SignalBacktester;
use App\Support\Stocks\SupportResistanceCalculator;

class GetStockAnalysisAction
{
    /**
     * Uses the stock's full price history (not just the chart's rolling
     * window) — both the swing-point detection and the backtest benefit
     * from as much history as is available.
     *
     * @return array{support_resistance: array, backtest: array}
     */
    public function execute(string $stockId, float $currentPrice): array
    {
        $prices = StockPrice::query()
            ->where('stock_id', $stockId)
            ->orderBy('trading_date')
            ->get(['high', 'low', 'close']);

        $ohlc = $prices->map(fn (StockPrice $p) => ['high' => (float) $p->high, 'low' => (float) $p->low])->values()->all();
        $closes = $prices->pluck('close')->map(fn ($c) => (float) $c)->values()->all();

        return [
            'support_resistance' => SupportResistanceCalculator::calculate($ohlc, $currentPrice),
            'backtest' => SignalBacktester::run($closes),
        ];
    }
}
