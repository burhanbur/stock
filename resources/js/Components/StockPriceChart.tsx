import { useMemo, useState, MouseEvent } from 'react';
import { StockPricePoint } from '@/types/stock';

interface StockPriceChartProps {
    prices: StockPricePoint[];
    currency: string;
}

const WIDTH = 760;
const HEIGHT = 260;
const PADDING = { top: 16, right: 16, bottom: 28, left: 56 };
const LINE_COLOR = '#2563eb';

function formatPrice(value: number, currency: string): string {
    return `${currency} ${value.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
}

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

export default function StockPriceChart({ prices, currency }: StockPriceChartProps) {
    const [hoverIndex, setHoverIndex] = useState<number | null>(null);

    const chart = useMemo(() => {
        if (prices.length === 0) {
            return null;
        }

        const closes = prices.map((p) => p.close);
        const min = Math.min(...closes);
        const max = Math.max(...closes);
        const range = max - min || 1;

        const innerWidth = WIDTH - PADDING.left - PADDING.right;
        const innerHeight = HEIGHT - PADDING.top - PADDING.bottom;

        const xFor = (index: number) =>
            PADDING.left + (prices.length === 1 ? innerWidth / 2 : (index / (prices.length - 1)) * innerWidth);
        const yFor = (close: number) => PADDING.top + innerHeight - ((close - min) / range) * innerHeight;

        const points = prices.map((p, index) => ({ x: xFor(index), y: yFor(p.close), point: p }));
        const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x.toFixed(2)} ${p.y.toFixed(2)}`).join(' ');
        const areaPath = `${linePath} L ${points[points.length - 1].x.toFixed(2)} ${PADDING.top + innerHeight} L ${points[0].x.toFixed(2)} ${PADDING.top + innerHeight} Z`;

        const gridLines = Array.from({ length: 4 }, (_, i) => {
            const value = min + (range * i) / 3;
            return { value, y: yFor(value) };
        });

        return { points, linePath, areaPath, gridLines, min, max, innerWidth, xFor };
    }, [prices]);

    if (!chart || prices.length === 0) {
        return <div className="flex h-64 items-center justify-center text-sm text-slate-400">Belum ada data harga.</div>;
    }

    const handleMove = (event: MouseEvent<SVGSVGElement>) => {
        const rect = event.currentTarget.getBoundingClientRect();
        const ratio = (event.clientX - rect.left) / rect.width;
        const viewBoxX = ratio * WIDTH;
        const nearest = chart.points.reduce((closestIndex, p, index) => {
            const current = Math.abs(p.x - viewBoxX);
            const closest = Math.abs(chart.points[closestIndex].x - viewBoxX);
            return current < closest ? index : closestIndex;
        }, 0);
        setHoverIndex(nearest);
    };

    const hovered = hoverIndex !== null ? chart.points[hoverIndex] : chart.points[chart.points.length - 1];

    return (
        <div className="relative">
            <svg
                viewBox={`0 0 ${WIDTH} ${HEIGHT}`}
                width="100%"
                height={HEIGHT}
                onMouseMove={handleMove}
                onMouseLeave={() => setHoverIndex(null)}
                className="overflow-visible"
                role="img"
                aria-label={`Grafik harga penutupan, ${prices.length} hari terakhir`}
            >
                <defs>
                    <linearGradient id="price-area" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stopColor={LINE_COLOR} stopOpacity="0.16" />
                        <stop offset="100%" stopColor={LINE_COLOR} stopOpacity="0" />
                    </linearGradient>
                </defs>

                {chart.gridLines.map((line) => (
                    <g key={line.value}>
                        <line
                            x1={PADDING.left}
                            x2={WIDTH - PADDING.right}
                            y1={line.y}
                            y2={line.y}
                            stroke="#e2e8f0"
                            strokeWidth={1}
                        />
                        <text x={PADDING.left - 8} y={line.y} textAnchor="end" dominantBaseline="middle" className="fill-slate-400 text-[10px]">
                            {Math.round(line.value).toLocaleString('id-ID')}
                        </text>
                    </g>
                ))}

                <path d={chart.areaPath} fill="url(#price-area)" />
                <path d={chart.linePath} fill="none" stroke={LINE_COLOR} strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" />

                <circle
                    cx={chart.points[chart.points.length - 1].x}
                    cy={chart.points[chart.points.length - 1].y}
                    r={4}
                    fill={LINE_COLOR}
                />

                {hoverIndex !== null && (
                    <>
                        <line
                            x1={hovered.x}
                            x2={hovered.x}
                            y1={PADDING.top}
                            y2={HEIGHT - PADDING.bottom}
                            stroke="#94a3b8"
                            strokeWidth={1}
                            strokeDasharray="3 3"
                        />
                        <circle cx={hovered.x} cy={hovered.y} r={4} fill="white" stroke={LINE_COLOR} strokeWidth={2} />
                    </>
                )}

                <text x={PADDING.left} y={HEIGHT - 6} className="fill-slate-400 text-[10px]">
                    {formatDate(prices[0].trading_date)}
                </text>
                <text x={WIDTH - PADDING.right} y={HEIGHT - 6} textAnchor="end" className="fill-slate-400 text-[10px]">
                    {formatDate(prices[prices.length - 1].trading_date)}
                </text>
            </svg>

            <div className="pointer-events-none absolute top-0 right-0 rounded-md border border-slate-200 bg-white/95 px-3 py-2 text-xs shadow-sm">
                <div className="font-medium text-slate-700">{formatDate(hovered.point.trading_date)}</div>
                <div className="mt-1 grid grid-cols-2 gap-x-3 text-slate-500 tabular-nums">
                    <span>Open</span>
                    <span className="text-right text-slate-700">{formatPrice(hovered.point.open, currency)}</span>
                    <span>High</span>
                    <span className="text-right text-slate-700">{formatPrice(hovered.point.high, currency)}</span>
                    <span>Low</span>
                    <span className="text-right text-slate-700">{formatPrice(hovered.point.low, currency)}</span>
                    <span>Close</span>
                    <span className="text-right font-medium text-slate-900">{formatPrice(hovered.point.close, currency)}</span>
                    <span>Volume</span>
                    <span className="text-right text-slate-700">{hovered.point.volume.toLocaleString('id-ID')}</span>
                </div>
            </div>
        </div>
    );
}
