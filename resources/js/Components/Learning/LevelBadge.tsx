import { ModuleLevel } from '@/types/learning';

const styles: Record<ModuleLevel, string> = {
    beginner: 'bg-emerald-50 text-emerald-700',
    intermediate: 'bg-blue-50 text-blue-700',
    advanced: 'bg-amber-50 text-amber-700',
    quant: 'bg-violet-50 text-violet-700',
};

interface LevelBadgeProps {
    level: ModuleLevel;
    label: string;
}

export default function LevelBadge({ level, label }: LevelBadgeProps) {
    return <span className={`inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${styles[level]}`}>{label}</span>;
}
