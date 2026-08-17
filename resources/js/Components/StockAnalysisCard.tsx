import { History, TrendingDown, TrendingUp } from 'lucide-react';
import { SignalOutcome, StockAnalysis } from '@/types/stock';

interface StockAnalysisCardProps {
    analysis: StockAnalysis;
    currency: string;
}

function formatPrice(value: number, currency: string): string {
    return `${currency} ${value.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
}

function SignalOutcomeRow({ label, icon: Icon, tone, outcome, horizonDays, winMeaning }: {
    label: string;
    icon: typeof TrendingUp;
    tone: 'emerald' | 'red';
    outcome: SignalOutcome;
    horizonDays: number;
    winMeaning: string;
}) {
    const toneClasses = tone === 'emerald' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200';

    if (outcome.count === 0) {
        return (
            <div className="rounded-lg border border-slate-100 bg-slate-50/60 p-4">
                <div className="flex items-center gap-2">
                    <Icon size={16} className="text-slate-400" aria-hidden="true" />
                    <span className="text-sm font-medium text-slate-700">Sinyal {label}</span>
                </div>
                <p className="mt-2 text-xs text-slate-400">Belum pernah muncul dalam riwayat data yang tersedia.</p>
            </div>
        );
    }

    return (
        <div className="rounded-lg border border-slate-100 bg-slate-50/60 p-4">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <Icon size={16} className="text-slate-500" aria-hidden="true" />
                    <span className="text-sm font-medium text-slate-700">Sinyal {label}</span>
                </div>
                <span className={`rounded-full border px-2 py-0.5 text-xs font-medium ${toneClasses}`}>{outcome.count}x muncul</span>
            </div>
            <p className="mt-1 text-xs text-slate-400">{winMeaning}</p>
            <dl className="mt-3 space-y-1 text-xs text-slate-500">
                <div className="flex justify-between">
                    <dt>Win rate ({horizonDays} hari ke depan)</dt>
                    <dd className="font-medium tabular-nums text-slate-700">{outcome.win_rate}%</dd>
                </div>
                <div className="flex justify-between">
                    <dt>Perubahan harga rata-rata</dt>
                    <dd className={`font-medium tabular-nums ${(outcome.avg_return_percent ?? 0) >= 0 ? 'text-emerald-700' : 'text-red-700'}`}>
                        {(outcome.avg_return_percent ?? 0) > 0 ? '+' : ''}
                        {outcome.avg_return_percent}%
                    </dd>
                </div>
            </dl>
        </div>
    );
}

export default function StockAnalysisCard({ analysis, currency }: StockAnalysisCardProps) {
    const { support, resistance } = analysis.support_resistance;

    return (
        <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 className="text-sm font-semibold text-slate-700">Analisis Lanjutan</h2>
            <p className="mt-1 text-xs text-slate-400">
                Berbasis pola harga historis — bukan prediksi harga masa depan. Gunakan sebagai bahan belajar, bukan saran investasi.
            </p>

            <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <h3 className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Level Support</h3>
                    {support.length === 0 ? (
                        <p className="text-sm text-slate-400">Belum terdeteksi.</p>
                    ) : (
                        <ul className="space-y-1.5">
                            {support.map((level) => (
                                <li key={level.level} className="flex items-center justify-between text-sm">
                                    <span className="font-medium tabular-nums text-emerald-700">{formatPrice(level.level, currency)}</span>
                                    <span className="text-xs text-slate-400">{level.touches}x disentuh</span>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
                <div>
                    <h3 className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Level Resistance</h3>
                    {resistance.length === 0 ? (
                        <p className="text-sm text-slate-400">Belum terdeteksi.</p>
                    ) : (
                        <ul className="space-y-1.5">
                            {resistance.map((level) => (
                                <li key={level.level} className="flex items-center justify-between text-sm">
                                    <span className="font-medium tabular-nums text-red-700">{formatPrice(level.level, currency)}</span>
                                    <span className="text-xs text-slate-400">{level.touches}x disentuh</span>
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            </div>

            {analysis.backtest && (
                <div className="mt-5 border-t border-slate-100 pt-4">
                    <div className="mb-3 flex items-center gap-2">
                        <History size={14} className="text-slate-400" aria-hidden="true" />
                        <h3 className="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Backtest Sinyal Skor Kami
                        </h3>
                    </div>
                    <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <SignalOutcomeRow
                            label="Beli"
                            icon={TrendingUp}
                            tone="emerald"
                            outcome={analysis.backtest.beli}
                            horizonDays={analysis.backtest.horizon_days}
                            winMeaning="Menang = harga naik setelahnya"
                        />
                        <SignalOutcomeRow
                            label="Jual"
                            icon={TrendingDown}
                            tone="red"
                            outcome={analysis.backtest.jual}
                            horizonDays={analysis.backtest.horizon_days}
                            winMeaning="Menang = harga turun setelahnya"
                        />
                    </div>
                </div>
            )}
        </div>
    );
}
