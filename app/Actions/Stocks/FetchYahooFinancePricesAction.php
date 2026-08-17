<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;

class FetchYahooFinancePricesAction
{
    public const SOURCE = 'provider:yahoo_finance';

    /**
     * Fetches daily OHLCV history for a stock from Yahoo Finance's
     * unofficial chart endpoint. IDX tickers are queried with the
     * `.JK` suffix (e.g. BBCA -> BBCA.JK).
     *
     * @return array<int, array{stock_id: string, trading_date: string, open: float, high: float, low: float, close: float, volume: int, source: string, created_at: CarbonImmutable}>
     */
    public function execute(Stock $stock, string $range = '6mo'): array
    {
        $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->get("https://query1.finance.yahoo.com/v8/finance/chart/{$stock->ticker}.JK", [
                'range' => $range,
                'interval' => '1d',
            ])
            ->throw();

        $result = $response->json('chart.result.0');

        if (! $result) {
            return [];
        }

        $timezone = $result['meta']['exchangeTimezoneName'] ?? 'Asia/Jakarta';
        $timestamps = $result['timestamp'] ?? [];
        $quote = $result['indicators']['quote'][0] ?? [];

        $rows = [];

        foreach ($timestamps as $i => $timestamp) {
            $close = $quote['close'][$i] ?? null;

            if ($close === null) {
                continue;
            }

            $rows[] = [
                'stock_id' => $stock->id,
                'trading_date' => CarbonImmutable::createFromTimestamp($timestamp, $timezone)->toDateString(),
                'open' => round($quote['open'][$i], 2),
                'high' => round($quote['high'][$i], 2),
                'low' => round($quote['low'][$i], 2),
                'close' => round($close, 2),
                'volume' => (int) ($quote['volume'][$i] ?? 0),
                'source' => self::SOURCE,
                'created_at' => CarbonImmutable::now(),
            ];
        }

        return $rows;
    }
}
