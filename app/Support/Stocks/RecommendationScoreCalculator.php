<?php

namespace App\Support\Stocks;

class RecommendationScoreCalculator
{
    /** Momentum drives direction more than risk tempers it. */
    private const MOMENTUM_WEIGHT = 0.7;

    private const RISK_WEIGHT = 0.3;

    /**
     * Combines a momentum score and a risk score into a single overall
     * score/label. Pure, deterministic, unit tested with known
     * input/output pairs — same pattern as PriceChangeCalculator.
     *
     * @return array{score: int|null, label: string}
     */
    public static function combine(?int $momentumScore, ?int $riskScore): array
    {
        if ($momentumScore === null || $riskScore === null) {
            return ['score' => null, 'label' => 'Data belum cukup'];
        }

        $score = (int) round($momentumScore * self::MOMENTUM_WEIGHT + $riskScore * self::RISK_WEIGHT);

        // Risk may temper an existing Beli/Jual call toward Tahan via the
        // blended score above, but it must never manufacture a Beli/Jual
        // out of neutral (Tahan) momentum on its own — a flat,
        // low-volatility stock isn't a "buy" just because it's safe.
        $momentumLabel = MomentumScoreCalculator::label($momentumScore);
        $label = $momentumLabel === 'Tahan' ? 'Tahan' : MomentumScoreCalculator::label($score);

        return ['score' => $score, 'label' => $label];
    }
}
