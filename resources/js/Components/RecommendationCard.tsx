import { Activity, Minus, ShieldAlert, ShieldCheck, ShieldQuestion, TrendingDown, TrendingUp } from 'lucide-react';
import { StockRecommendation } from '@/types/stock';
import { recommendationStyleFor as styleFor } from '@/lib/recommendationStyles';
import GlossaryTerm from '@/Components/GlossaryTerm';

interface RecommendationCardProps {
    recommendation: StockRecommendation;
}

export default function RecommendationCard({ recommendation }: RecommendationCardProps) {
    const { score, label, momentum, risk } = recommendation;
    const overallStyle = styleFor(label);

    if (score === null) {
        return (
            <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div className="flex items-center gap-2 text-sm text-slate-500">
                    <ShieldQuestion size={18} aria-hidden="true" />
                    Belum cukup riwayat harga untuk menghitung rekomendasi (butuh minimal ~50 hari data).
                </div>
            </div>
        );
    }

    const MomentumIcon = momentum.label === 'Beli' ? TrendingUp : momentum.label === 'Jual' ? TrendingDown : Minus;
    const RiskIcon = risk.label === 'Risiko Rendah' ? ShieldCheck : risk.label === 'Risiko Tinggi' ? ShieldAlert : ShieldQuestion;

    return (
        <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="flex flex-wrap items-center justify-between gap-4">
                <div className="flex items-center gap-3">
                    <div className={`flex h-11 w-11 items-center justify-center rounded-xl border ${overallStyle.badge}`}>
                        <Activity size={20} aria-hidden="true" />
                    </div>
                    <div>
                        <div className="text-xs font-semibold uppercase tracking-wide text-slate-400">Rekomendasi</div>
                        <div className="text-lg font-bold text-slate-900">{label}</div>
                    </div>
                </div>
                <div className={`rounded-full border px-3 py-1 text-sm font-semibold tabular-nums ${overallStyle.badge}`}>Skor {score}/100</div>
            </div>

            <p className="mt-3 text-xs text-slate-400">
                Skor teknikal berbasis riwayat harga (bukan analisis fundamental) — gabungan sinyal <GlossaryTerm slug="momentum">momentum</GlossaryTerm>{' '}
                dan <GlossaryTerm slug="risiko">risiko</GlossaryTerm>. Hanya untuk keperluan belajar, bukan saran investasi.
            </p>

            <div className="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div className="rounded-lg border border-slate-100 bg-slate-50/60 p-4">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <MomentumIcon size={16} className="text-slate-500" aria-hidden="true" />
                            <span className="text-sm font-medium text-slate-700">
                                <GlossaryTerm slug="momentum">Skor Momentum</GlossaryTerm>
                            </span>
                        </div>
                        <span className={`rounded-full border px-2 py-0.5 text-xs font-medium ${styleFor(momentum.label).badge}`}>
                            {momentum.label}
                        </span>
                    </div>
                    {momentum.score !== null && (
                        <>
                            <div className="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                                <div className={`h-full rounded-full ${styleFor(momentum.label).bar}`} style={{ width: `${momentum.score}%` }} />
                            </div>
                            <dl className="mt-3 space-y-1 text-xs text-slate-500">
                                <div className="flex justify-between">
                                    <dt>Return 20 hari</dt>
                                    <dd className="tabular-nums text-slate-700">
                                        {momentum.momentum_percent! > 0 ? '+' : ''}
                                        {momentum.momentum_percent}%
                                    </dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt>
                                        <GlossaryTerm slug="rata-rata-bergerak">MA20 / MA50</GlossaryTerm>
                                    </dt>
                                    <dd className="tabular-nums text-slate-700">
                                        {momentum.sma20?.toLocaleString('id-ID')} / {momentum.sma50?.toLocaleString('id-ID')}
                                    </dd>
                                </div>
                            </dl>
                        </>
                    )}
                </div>

                <div className="rounded-lg border border-slate-100 bg-slate-50/60 p-4">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <RiskIcon size={16} className="text-slate-500" aria-hidden="true" />
                            <span className="text-sm font-medium text-slate-700">
                                <GlossaryTerm slug="risiko">Skor Risiko</GlossaryTerm>
                            </span>
                        </div>
                        <span className={`rounded-full border px-2 py-0.5 text-xs font-medium ${styleFor(risk.label).badge}`}>{risk.label}</span>
                    </div>
                    {risk.score !== null && (
                        <>
                            <div className="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                                <div className={`h-full rounded-full ${styleFor(risk.label).bar}`} style={{ width: `${risk.score}%` }} />
                            </div>
                            <dl className="mt-3 space-y-1 text-xs text-slate-500">
                                <div className="flex justify-between">
                                    <dt>
                                        <GlossaryTerm slug="volatilitas">Volatilitas tahunan</GlossaryTerm>
                                    </dt>
                                    <dd className="tabular-nums text-slate-700">{risk.annualized_volatility_percent}%</dd>
                                </div>
                            </dl>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}
