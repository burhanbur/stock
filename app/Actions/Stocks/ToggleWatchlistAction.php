<?php

namespace App\Actions\Stocks;

use App\Models\Stock;
use App\Models\User;
use App\Models\Watchlist;

class ToggleWatchlistAction
{
    /**
     * @return bool True if the stock is now watchlisted, false if it was removed.
     */
    public function execute(User $user, Stock $stock): bool
    {
        $existing = Watchlist::query()
            ->where('user_id', $user->id)
            ->where('stock_id', $stock->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Watchlist::create([
            'user_id' => $user->id,
            'stock_id' => $stock->id,
        ]);

        return true;
    }
}
