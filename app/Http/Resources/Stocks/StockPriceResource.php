<?php

namespace App\Http\Resources\Stocks;

use App\Models\StockPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin StockPrice */
class StockPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'trading_date' => $this->trading_date->toDateString(),
            'open' => (float) $this->open,
            'high' => (float) $this->high,
            'low' => (float) $this->low,
            'close' => (float) $this->close,
            'volume' => $this->volume,
        ];
    }
}
