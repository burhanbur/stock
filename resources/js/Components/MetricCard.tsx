import { ReactNode } from 'react';

interface MetricCardProps {
    label: string;
    value: ReactNode;
    sublabel?: ReactNode;
}

export default function MetricCard({ label, value, sublabel }: MetricCardProps) {
    return (
        <div className="rounded-lg border border-slate-200 bg-white p-4">
            <div className="text-xs font-medium uppercase tracking-wide text-slate-400">{label}</div>
            <div className="mt-1 text-2xl font-semibold text-slate-900 tabular-nums">{value}</div>
            {sublabel && <div className="mt-1 text-sm text-slate-500">{sublabel}</div>}
        </div>
    );
}
