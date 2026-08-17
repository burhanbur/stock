import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { CartesianGrid, Legend, Line, LineChart, ResponsiveContainer, Tooltip, TooltipContentProps, XAxis, YAxis } from 'recharts';
import AppLayout from '@/Layouts/AppLayout';
import ChangeBadge from '@/Components/ChangeBadge';
import WatchlistButton from '@/Components/WatchlistButton';
import { recommendationStyleFor } from '@/lib/recommendationStyles';
import { StockDetail } from '@/types/stock';

interface StocksCompareProps {
    stocks: StockDetail[];
}

const LINE_COLORS = ['#2563eb', '#7c3aed', '#0d9488'];

function formatPrice(value: number | null, currency: string): string {
    if (value === null) {
        return '-';
    }

    return `${currency} ${value.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
}

function formatAxisDate(value: string): string {
    return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
}

/**
 * Builds a merged series of "% change since the start of each stock's own
 * window" keyed by trading_date, so stocks on wildly different price scales
 * (e.g. IDR 1.775 vs IDR 14.900) can be plotted on one shared axis.
 */
function buildNormalizedSeries(stocks: StockDetail[]): Array<Record<string, number | string>> {
    const byDate = new Map<string, Record<string, number | string>>();

    stocks.forEach((stock) => {
        const basePrice = stock.prices[0]?.close;
        if (!basePrice) return;

        stock.prices.forEach((point) => {
            const pctChange = ((point.close - basePrice) / basePrice) * 100;
            const row = byDate.get(point.trading_date) ?? { trading_date: point.trading_date };
            row[stock.ticker] = Math.round(pctChange * 100) / 100;
            byDate.set(point.trading_date, row);
        });
    });

    return Array.from(byDate.values()).sort((a, b) => String(a.trading_date).localeCompare(String(b.trading_date)));
}

function CompareTooltip({ active, payload, label }: TooltipContentProps) {
    if (!active || !payload || payload.length === 0) {
        return null;
    }

    return (
        <div className="rounded-md border border-slate-200 bg-white/95 px-3 py-2 text-xs shadow-sm">
            <div className="font-medium text-slate-700">{formatAxisDate(String(label))}</div>
            <div className="mt-1 space-y-0.5">
                {payload.map((entry) => (
                    <div key={entry.name} className="flex items-center justify-between gap-4 tabular-nums">
                        <span style={{ color: entry.color }} className="font-medium">
                            {entry.name}
                        </span>
                        <span className="text-slate-700">
                            {Number(entry.value) > 0 ? '+' : ''}
                            {entry.value}%
                        </span>
                    </div>
                ))}
            </div>
        </div>
    );
}

const METRIC_ROWS: Array<{
    label: string;
    render: (stock: StockDetail) => React.ReactNode;
}> = [
    { label: 'Perusahaan', render: (s) => <span className="text-slate-700">{s.company.name}</span> },
    { label: 'Sektor', render: (s) => <span className="text-slate-700">{s.sector?.name ?? '-'}</span> },
    {
        label: 'Harga Terakhir',
        render: (s) => <span className="font-medium tabular-nums text-slate-900">{formatPrice(s.latest_close, s.currency)}</span>,
    },
    { label: 'Perubahan Harian', render: (s) => <ChangeBadge change={s.change} changePercent={s.change_percent} /> },
    {
        label: 'Rekomendasi',
        render: (s) => (
            <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-medium ${recommendationStyleFor(s.recommendation.label).badge}`}>
                {s.recommendation.label} {s.recommendation.score !== null && `(${s.recommendation.score}/100)`}
            </span>
        ),
    },
    {
        label: 'Skor Momentum',
        render: (s) => (
            <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-medium ${recommendationStyleFor(s.recommendation.momentum.label).badge}`}>
                {s.recommendation.momentum.label}
            </span>
        ),
    },
    {
        label: 'Skor Risiko',
        render: (s) => (
            <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-medium ${recommendationStyleFor(s.recommendation.risk.label).badge}`}>
                {s.recommendation.risk.label}
            </span>
        ),
    },
    {
        label: 'Volatilitas Tahunan',
        render: (s) => (
            <span className="tabular-nums text-slate-700">
                {s.recommendation.risk.annualized_volatility_percent !== null ? `${s.recommendation.risk.annualized_volatility_percent}%` : '-'}
            </span>
        ),
    },
    {
        label: 'Watchlist',
        render: (s) => <WatchlistButton ticker={s.ticker} isWatchlisted={s.is_watchlisted} size={18} />,
    },
];

export default function StocksCompare({ stocks }: StocksCompareProps) {
    const chartData = buildNormalizedSeries(stocks);

    return (
        <AppLayout>
            <Head title={`Bandingkan ${stocks.map((s) => s.ticker).join(' vs ')}`} />

            <Link
                href={route('stocks.index')}
                className="inline-flex items-center gap-1 text-sm text-slate-500 transition-colors hover:text-slate-900"
            >
                <ArrowLeft aria-hidden="true" size={16} />
                Kembali ke daftar saham
            </Link>

            <div className="mt-3 mb-6">
                <h1 className="text-2xl font-semibold tracking-tight text-slate-900">
                    Bandingkan {stocks.map((s) => s.ticker).join(' vs ')}
                </h1>
                <p className="mt-1 text-sm text-slate-500">Perbandingan sisi-berdampingan berbasis data historis, bukan saran investasi.</p>
            </div>

            <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <h2 className="mb-4 text-sm font-semibold text-slate-700">Perubahan Harga (%) — dinormalisasi dari awal periode</h2>
                <div role="img" aria-label="Grafik perbandingan perubahan harga relatif antar saham">
                    <ResponsiveContainer width="100%" height={300}>
                        <LineChart data={chartData} margin={{ top: 8, right: 8, left: 8, bottom: 0 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke="#e2e8f0" vertical={false} />
                            <XAxis
                                dataKey="trading_date"
                                tickFormatter={formatAxisDate}
                                tick={{ fontSize: 10, fill: '#94a3b8' }}
                                axisLine={{ stroke: '#e2e8f0' }}
                                tickLine={false}
                                minTickGap={40}
                            />
                            <YAxis
                                tickFormatter={(value: number) => `${value > 0 ? '+' : ''}${value}%`}
                                tick={{ fontSize: 10, fill: '#94a3b8' }}
                                axisLine={false}
                                tickLine={false}
                                width={56}
                            />
                            <Tooltip content={(props: TooltipContentProps) => <CompareTooltip {...props} />} />
                            <Legend wrapperStyle={{ fontSize: 12 }} />
                            {stocks.map((stock, index) => (
                                <Line
                                    key={stock.ticker}
                                    type="monotone"
                                    dataKey={stock.ticker}
                                    stroke={LINE_COLORS[index % LINE_COLORS.length]}
                                    strokeWidth={2}
                                    dot={false}
                                    connectNulls
                                    isAnimationActive={false}
                                />
                            ))}
                        </LineChart>
                    </ResponsiveContainer>
                </div>
            </div>

            <div className="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200 text-sm">
                        <thead className="bg-slate-50/80">
                            <tr>
                                <th className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Metrik</th>
                                {stocks.map((stock) => (
                                    <th key={stock.id} className="px-4 py-3 text-left">
                                        <button
                                            type="button"
                                            onClick={() => router.visit(route('stocks.show', stock.ticker))}
                                            className="text-sm font-bold text-slate-900 hover:text-blue-700 hover:underline"
                                        >
                                            {stock.ticker}
                                        </button>
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {METRIC_ROWS.map((row) => (
                                <tr key={row.label}>
                                    <td className="px-4 py-3.5 text-xs font-medium uppercase tracking-wide text-slate-400">{row.label}</td>
                                    {stocks.map((stock) => (
                                        <td key={stock.id} className="px-4 py-3.5">
                                            {row.render(stock)}
                                        </td>
                                    ))}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
