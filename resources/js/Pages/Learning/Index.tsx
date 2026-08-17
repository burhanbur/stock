import { Head, Link } from '@inertiajs/react';
import { BookOpen, Lock, Sparkles } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import ProgressBar from '@/Components/Learning/ProgressBar';
import LevelBadge from '@/Components/Learning/LevelBadge';
import { LearningDashboard } from '@/types/learning';

export default function LearningIndex({
    modules,
    overall_percent,
    total_lessons,
    completed_lessons,
    quiz_average,
    continue_lesson,
}: LearningDashboard) {
    return (
        <AppLayout>
            <Head title="Pusat Belajar Saham" />

            <div className="mb-6">
                <h1 className="text-xl font-semibold text-slate-900">Pusat Belajar Saham</h1>
                <p className="mt-1 text-sm text-slate-500">
                    Dari nol sampai bisa memahami skor rekomendasi saham kami — selangkah demi selangkah.
                </p>
            </div>

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div className="rounded-lg border border-slate-200 bg-white p-4 sm:col-span-2">
                    <div className="flex items-center justify-between text-sm">
                        <span className="font-medium text-slate-700">Progres Keseluruhan</span>
                        <span className="tabular-nums text-slate-500">
                            {completed_lessons}/{total_lessons} pelajaran
                        </span>
                    </div>
                    <ProgressBar percent={overall_percent} className="mt-2" />
                    <div className="mt-1 text-right text-xs text-slate-400">{overall_percent}%</div>
                </div>

                <div className="rounded-lg border border-slate-200 bg-white p-4">
                    <div className="text-sm font-medium text-slate-700">Rata-rata Kuis</div>
                    <div className="mt-1 text-2xl font-semibold text-slate-900 tabular-nums">
                        {quiz_average !== null ? `${quiz_average}%` : '-'}
                    </div>
                </div>
            </div>

            {continue_lesson && (
                <Link
                    href={route('learning.lessons.show', [continue_lesson.module_slug, continue_lesson.lesson_slug])}
                    className="mt-4 flex items-center justify-between rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm hover:bg-blue-100"
                >
                    <span className="flex items-center gap-2 font-medium text-blue-700">
                        <Sparkles size={16} aria-hidden="true" />
                        Lanjutkan Belajar — {continue_lesson.lesson_title}
                    </span>
                    <span className="text-blue-600">&rarr;</span>
                </Link>
            )}

            <div className="mt-8">
                <div className="mb-3 flex items-center justify-between">
                    <h2 className="text-sm font-medium text-slate-500">Modul Pembelajaran</h2>
                    <Link
                        href={route('learning.glossary')}
                        className="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 hover:underline"
                    >
                        <BookOpen size={14} aria-hidden="true" />
                        Kamus Istilah
                    </Link>
                </div>

                <div className="space-y-3">
                    {modules.map((module) => {
                        const content = (
                            <div
                                className={`rounded-lg border bg-white p-4 transition-colors ${
                                    module.is_locked ? 'border-slate-200 opacity-60' : 'border-slate-200 hover:border-blue-300'
                                }`}
                            >
                                <div className="flex items-start justify-between gap-4">
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <span className="text-xs font-medium text-slate-400">Modul {module.order}</span>
                                            <LevelBadge level={module.level} label={module.level_label} />
                                        </div>
                                        <h3 className="mt-1 text-sm font-semibold text-slate-900">{module.title}</h3>
                                        {module.description && <p className="mt-1 text-sm text-slate-500">{module.description}</p>}
                                    </div>
                                    {module.is_locked && <Lock size={18} className="shrink-0 text-slate-400" aria-hidden="true" />}
                                </div>

                                <div className="mt-3 flex items-center gap-3">
                                    <ProgressBar percent={module.percent} className="max-w-xs" />
                                    <span className="shrink-0 text-xs text-slate-400 tabular-nums">
                                        {module.completed_lessons}/{module.total_lessons}
                                    </span>
                                </div>
                            </div>
                        );

                        return module.is_locked ? (
                            <div key={module.slug}>{content}</div>
                        ) : (
                            <Link key={module.slug} href={route('learning.modules.show', module.slug)} className="block">
                                {content}
                            </Link>
                        );
                    })}
                </div>
            </div>
        </AppLayout>
    );
}
