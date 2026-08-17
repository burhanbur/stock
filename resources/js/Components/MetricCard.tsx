import { ReactNode } from 'react';
import { LucideIcon } from 'lucide-react';

interface MetricCardProps {
    label: string;
    value: ReactNode;
    sublabel?: ReactNode;
    icon?: LucideIcon;
}

export default function MetricCard({ label, value, sublabel, icon: Icon }: MetricCardProps) {
    return (
        <div className="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md">
            <div className="flex items-center justify-between">
                <div className="text-xs font-semibold uppercase tracking-wide text-slate-400">{label}</div>
                {Icon && (
                    <div className="flex h-7 w-7 items-center justify-center rounded-md bg-slate-50 text-slate-400">
                        <Icon size={14} aria-hidden="true" />
                    </div>
                )}
            </div>
            <div className="mt-1.5 text-2xl font-semibold tabular-nums text-slate-900">{value}</div>
            {sublabel && <div className="mt-1 text-sm text-slate-500">{sublabel}</div>}
        </div>
    );
}
