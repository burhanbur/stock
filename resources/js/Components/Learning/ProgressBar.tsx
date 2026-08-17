interface ProgressBarProps {
    percent: number;
    className?: string;
}

export default function ProgressBar({ percent, className = '' }: ProgressBarProps) {
    const clamped = Math.max(0, Math.min(100, percent));

    return (
        <div
            className={`h-2 w-full overflow-hidden rounded-full bg-slate-100 ${className}`}
            role="progressbar"
            aria-valuenow={clamped}
            aria-valuemin={0}
            aria-valuemax={100}
        >
            <div className="h-full rounded-full bg-blue-600 transition-[width]" style={{ width: `${clamped}%` }} />
        </div>
    );
}
