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

            $summary['synced']++;
            $summary['rows'] += count($rows);

            usleep(200_000);
        }

        return $summary;
    }
}
