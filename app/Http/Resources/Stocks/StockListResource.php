<?php

namespace App\Http\Resources\Stocks;

use App\Models\Stock;
use App\Support\Stocks\StockRecommendationBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Stock */
class StockListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticker' => $this->ticker,
            'exchange' => $this->exchange,
            'currency' => $this->currency,
            'company_name' => $this->company?->name,
            'sector' => $this->whenLoaded('company', fn () => $this->company->sector ? [
                'id' => $this->company->sector->id,
                'name' => $this->company->sector->name,
            ] : null),
            'latest_close' => $this->latestPrice?->close !== null ? (float) $this->latestPrice->close : null,
            'latest_trading_date' => $this->latestPrice?->trading_date?->toDateString(),
            'is_watchlisted' => (bool) ($this->is_watchlisted ?? false),
            'recommendation' => StockRecommendationBuilder::build($this->recentPrices ?? collect()),
        ];
    }
}
