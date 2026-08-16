interface ChangeBadgeProps {
    change: number;
    changePercent: number;
}

export default function ChangeBadge({ change, changePercent }: ChangeBadgeProps) {
    const isFlat = changePercent === 0;
    const isUp = changePercent > 0;

    const tone = isFlat ? 'bg-slate-100 text-slate-600' : isUp ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700';
    const arrow = isFlat ? '' : isUp ? '▲' : '▼';
    const sign = change > 0 ? '+' : '';

    return (
        <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-sm font-medium tabular-nums ${tone}`}>
            <span aria-hidden="true">{arrow}</span>
            {sign}
            {change.toFixed(2)} ({sign}
            {changePercent.toFixed(2)}%)
        </span>
    );
}
