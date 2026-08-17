import { Area, AreaChart, CartesianGrid, ReferenceLine, ResponsiveContainer, Tooltip, TooltipContentProps, XAxis, YAxis } from 'recharts';
import { PriceLevel, StockPricePoint } from '@/types/stock';

interface StockPriceChartProps {
    prices: StockPricePoint[];
    currency: string;
    supportLevels?: PriceLevel[];
    resistanceLevels?: PriceLevel[];
}

const LINE_COLOR = '#2563eb';
const SUPPORT_COLOR = '#10b981';
const RESISTANCE_COLOR = '#ef4444';

function formatPrice(value: number, currency: string): string {
    return `${currency} ${value.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
}

function formatAxisDate(value: string): string {
    return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
}

function formatTooltipDate(value: string): string {
    return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

function ChartTooltip({ active, payload, currency }: TooltipContentProps & { currency: string }) {
    if (!active || !payload || payload.length === 0) {
        return null;
    }

    const point = payload[0]!.payload as StockPricePoint;

    return (
        <div className="rounded-md border border-slate-200 bg-white/95 px-3 py-2 text-xs shadow-sm">
            <div className="font-medium text-slate-700">{formatTooltipDate(point.trading_date)}</div>
            <div className="mt-1 grid grid-cols-2 gap-x-3 text-slate-500 tabular-nums">
                <span>Open</span>
                <span className="text-right text-slate-700">{formatPrice(point.open, currency)}</span>
                <span>High</span>
                <span className="text-right text-slate-700">{formatPrice(point.high, currency)}</span>
                <span>Low</span>
                <span className="text-right text-slate-700">{formatPrice(point.low, currency)}</span>
                <span>Close</span>
                <span className="text-right font-medium text-slate-900">{formatPrice(point.close, currency)}</span>
                <span>Volume</span>
                <span className="text-right text-slate-700">{point.volume.toLocaleString('id-ID')}</span>
            </div>
        </div>
    );
}

export default function StockPriceChart({ prices, currency, supportLevels = [], resistanceLevels = [] }: StockPriceChartProps) {
    if (prices.length === 0) {
        return <div className="flex h-64 items-center justify-center text-sm text-slate-400">Belum ada data harga.</div>;
    }

    const closes = prices.map((p) => p.close);
    const levelValues = [...supportLevels, ...resistanceLevels].map((l) => l.level);
    const min = Math.min(...closes, ...levelValues);
    const max = Math.max(...closes, ...levelValues);
    const padding = (max - min || max * 0.01) * 0.1;

    return (
        <div role="img" aria-label={`Grafik harga penutupan, ${prices.length} hari terakhir`}>
            <ResponsiveContainer width="100%" height={280}>
                <AreaChart data={prices} margin={{ top: 8, right: 8, left: 8, bottom: 0 }}>
                    <defs>
                        <linearGradient id="price-area" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stopColor={LINE_COLOR} stopOpacity={0.16} />
                            <stop offset="100%" stopColor={LINE_COLOR} stopOpacity={0} />
                        </linearGradient>
                    </defs>

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
                        domain={[min - padding, max + padding]}
                        tickFormatter={(value: number) => Math.round(value).toLocaleString('id-ID')}
                        tick={{ fontSize: 10, fill: '#94a3b8' }}
                        axisLine={false}
                        tickLine={false}
                        width={64}
                    />

                    <Tooltip
                        content={(props: TooltipContentProps) => <ChartTooltip {...props} currency={currency} />}
                        cursor={{ stroke: '#94a3b8', strokeDasharray: '3 3' }}
                    />

                    {supportLevels.map((level) => (
                        <ReferenceLine
                            key={`support-${level.level}`}
                            y={level.level}
                            stroke={SUPPORT_COLOR}
                            strokeDasharray="4 4"
                            strokeOpacity={0.6}
                            label={{
                                value: level.level.toLocaleString('id-ID'),
                                position: 'insideBottomLeft',
                                fill: SUPPORT_COLOR,
                                fontSize: 10,
                            }}
                        />
                    ))}
                    {resistanceLevels.map((level) => (
                        <ReferenceLine
                            key={`resistance-${level.level}`}
                            y={level.level}
                            stroke={RESISTANCE_COLOR}
                            strokeDasharray="4 4"
                            strokeOpacity={0.6}
                            label={{
                                value: level.level.toLocaleString('id-ID'),
                                position: 'insideTopLeft',
                                fill: RESISTANCE_COLOR,
                                fontSize: 10,
                            }}
                        />
                    ))}

                    <Area
                        type="monotone"
                        dataKey="close"
                        stroke={LINE_COLOR}
                        strokeWidth={2}
                        fill="url(#price-area)"
                        dot={false}
                        activeDot={{ r: 4, strokeWidth: 2, stroke: LINE_COLOR, fill: 'white' }}
                        isAnimationActive={false}
                    />
                </AreaChart>
            </ResponsiveContainer>
        </div>
    );
}
