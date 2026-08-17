<?php

namespace App\Support\Stocks;

use App\Models\StockPrice;
use Illuminate\Support\Collection;

class StockRecommendationBuilder
{
    /**
     * Shared by StockDetailResource and StockListResource so both compute
     * the recommendation the same way from a stock's recent prices.
     *
     * @param  Collection<int, StockPrice>  $prices  Ascending by trading date.
     * @return array{score: int|null, label: string, momentum: array, risk: array}
     */
    public static function build(Collection $prices): array
    {
        $closes = $prices->pluck('close')->map(fn ($close) => (float) $close)->values()->all();

        $momentum = MomentumScoreCalculator::calculate($closes);
        $risk = RiskScoreCalculator::calculate($closes);
        $overall = RecommendationScoreCalculator::combine($momentum['score'], $risk['score']);

        return [
            'score' => $overall['score'],
            'label' => $overall['label'],
            'momentum' => $momentum,
            'risk' => $risk,
        ];
    }
}
