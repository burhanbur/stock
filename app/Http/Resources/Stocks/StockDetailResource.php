<?php

namespace App\Http\Resources\Stocks;

use App\Models\Stock;
use App\Support\Stocks\PriceChangeCalculator;
use App\Support\Stocks\StockRecommendationBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Stock */
class StockDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $prices = $this->recentPrices ?? collect();
        $latest = $prices->last();
        $previous = $prices->count() > 1 ? $prices->slice(-2, 1)->first() : null;

        $change = $latest
            ? PriceChangeCalculator::calculate((float) $latest->close, $previous ? (float) $previous->close : null)
            : ['change' => 0.0, 'change_percent' => 0.0];

        return [
            'id' => $this->id,
            'ticker' => $this->ticker,
            'exchange' => $this->exchange,
            'board' => $this->board,
            'currency' => $this->currency,
            'listed_at' => $this->listed_at?->toDateString(),
            'is_active' => $this->is_active,
            'is_watchlisted' => (bool) ($this->is_watchlisted ?? false),
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'short_name' => $this->company->short_name,
                'description' => $this->company->description,
            ],
            'sector' => $this->company->sector ? (new SectorResource($this->company->sector))->resolve() : null,
            'latest_close' => $latest ? (float) $latest->close : null,
            'latest_trading_date' => $latest?->trading_date->toDateString(),
            'change' => $change['change'],
            'change_percent' => $change['change_percent'],
            'prices' => StockPriceResource::collection($prices)->resolve(),
            'recommendation' => StockRecommendationBuilder::build($prices),
        ];
    }
}
