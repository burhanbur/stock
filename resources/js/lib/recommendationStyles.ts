export const recommendationLabelStyles: Record<string, { badge: string; bar: string }> = {
    Beli: { badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', bar: 'bg-emerald-500' },
    Tahan: { badge: 'bg-amber-50 text-amber-700 border-amber-200', bar: 'bg-amber-500' },
    Jual: { badge: 'bg-red-50 text-red-700 border-red-200', bar: 'bg-red-500' },
    'Risiko Rendah': { badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', bar: 'bg-emerald-500' },
    'Risiko Sedang': { badge: 'bg-amber-50 text-amber-700 border-amber-200', bar: 'bg-amber-500' },
    'Risiko Tinggi': { badge: 'bg-red-50 text-red-700 border-red-200', bar: 'bg-red-500' },
    'Data belum cukup': { badge: 'bg-slate-100 text-slate-500 border-slate-200', bar: 'bg-slate-300' },
};

export function recommendationStyleFor(label: string) {
    return recommendationLabelStyles[label] ?? recommendationLabelStyles['Data belum cukup']!;
}
