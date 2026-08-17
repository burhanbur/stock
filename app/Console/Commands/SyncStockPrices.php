<?php

namespace App\Console\Commands;

use App\Actions\Stocks\SyncStockPricesAction;
use Illuminate\Console\Command;

class SyncStockPrices extends Command
{
    protected $signature = 'stocks:sync-prices
                            {ticker? : Only sync this ticker (e.g. BBCA)}
                            {--range=6mo : Yahoo Finance range (1mo, 3mo, 6mo, 1y, 2y, 5y, max)}';

    protected $description = 'Sync daily OHLCV price history from Yahoo Finance';

    public function handle(SyncStockPricesAction $action): int
    {
        $summary = $action->execute($this->argument('ticker'), $this->option('range'));

        foreach ($summary['errors'] as $error) {
            $this->warn("  {$error}");
        }

        $this->info("Synced {$summary['synced']} stock(s), {$summary['rows']} row(s), {$summary['failed']} failed.");

        return $summary['synced'] > 0 || $summary['failed'] === 0 ? self::SUCCESS : self::FAILURE;
    }
}
