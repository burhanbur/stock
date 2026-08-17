<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncStockPricesAction
{
    public function __construct(private FetchYahooFinancePricesAction $fetch) {}

    /**
     * Fetches and upserts price history for one or all stocks from Yahoo
     * Finance, in a single synchronous pass.
     *
     * @return array{synced: int, failed: int, rows: int, errors: array<int, string>}
     */
    public function execute(?string $ticker = null, string $range = '6mo'): array
    {
        $stocks = $ticker
            ? Stock::query()->where('ticker', strtoupper($ticker))->get()
            : Stock::all();

        $summary = ['synced' => 0, 'failed' => 0, 'rows' => 0, 'errors' => []];

        foreach ($stocks as $stock) {
            try {
                $rows = $this->fetch->execute($stock, $range);
            } catch (Throwable $e) {
                $summary['failed']++;
                $summary['errors'][] = "{$stock->ticker}: {$e->getMessage()}";

                continue;
            }

            if (empty($rows)) {
                $summary['failed']++;
                $summary['errors'][] = "{$stock->ticker}: tidak ada data yang dikembalikan.";

                continue;
            }

            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('stock_prices')->upsert(
                    $chunk,
                    ['stock_id', 'trading_date'],
                    ['open', 'high', 'low', 'close', 'volume', 'source']
                );
            }

            // Real data now exists for this stock — any leftover synthetic
            // seed rows (dates Yahoo didn't return, e.g. gaps/nulls) would
            // otherwise sit alongside real prices on a completely different
            // scale and corrupt volatility/momentum calculations.
            DB::table('stock_prices')
                ->where('stock_id', $stock->id)
                ->where('source', 'seed:dev')
                ->delete();

            $summary['synced']++;
            $summary['rows'] += count($rows);

            usleep(200_000);
        }

        return $summary;
    }
}
