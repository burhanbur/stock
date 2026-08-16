<?php

namespace Database\Seeders;

use App\Models\Stock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockPriceSeeder extends Seeder
{
    /**
     * Generates ~6 months of synthetic daily OHLCV history per stock
     * using a simple bounded random walk. This is development data
     * only (source = seed:dev), not real market data.
     */
    private const TRADING_DAYS = 130;

    private const BASE_PRICES = [
        'BBCA' => 9800,
        'BBRI' => 4200,
        'BMRI' => 5900,
        'BBNI' => 4900,
        'UNVR' => 3200,
        'ICBP' => 10800,
        'INDF' => 7500,
        'TLKM' => 3100,
        'ASII' => 5200,
        'ANTM' => 1500,
        'ADRO' => 2600,
        'INCO' => 4300,
    ];

    public function run(): void
    {
        DB::table('stock_prices')->delete();

        Stock::all()->each(function (Stock $stock) {
            $rows = $this->generateHistory($stock, self::BASE_PRICES[$stock->ticker] ?? 1000);

            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('stock_prices')->insert($chunk);
            }
        });
    }

    private function generateHistory(Stock $stock, float $basePrice): array
    {
        $rows = [];
        $previousClose = $basePrice;
        $date = CarbonImmutable::now()->subDays((int) round(self::TRADING_DAYS * 1.45));

        $tradingDaysGenerated = 0;

        while ($tradingDaysGenerated < self::TRADING_DAYS) {
            $date = $date->addDay();

            if ($date->isWeekend()) {
                continue;
            }

            $tradingDaysGenerated++;

            $gapPct = $this->randomPct(-0.01, 0.01);
            $dayPct = $this->randomPct(-0.03, 0.03);

            $open = round($previousClose * (1 + $gapPct), 2);
            $close = round($open * (1 + $dayPct), 2);
            $high = round(max($open, $close) * (1 + $this->randomPct(0, 0.015)), 2);
            $low = round(min($open, $close) * (1 - $this->randomPct(0, 0.015)), 2);
            $low = max($low, 1);

            $rows[] = [
                'stock_id' => $stock->id,
                'trading_date' => $date->toDateString(),
                'open' => $open,
                'high' => $high,
                'low' => $low,
                'close' => $close,
                'volume' => random_int(500_000, 40_000_000),
                'source' => 'seed:dev',
                'created_at' => now(),
            ];

            $previousClose = $close;
        }

        return $rows;
    }

    private function randomPct(float $min, float $max): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
