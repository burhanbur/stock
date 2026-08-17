import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle2, Lock } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import LevelBadge from '@/Components/Learning/LevelBadge';
import { LearningLessonSummary, LearningModuleDetail } from '@/types/learning';

interface ModuleShowProps {
    module: LearningModuleDetail;
    is_locked: boolean;
    lessons: LearningLessonSummary[];
}

export default function LearningModuleShow({ module, is_locked, lessons }: ModuleShowProps) {
    return (
        <AppLayout>
            <Head title={module.title} />

            <Link
                href={route('learning.index')}
                className="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 hover:underline"
            >
                <ArrowLeft aria-hidden="true" size={16} />
                Kembali ke Pusat Belajar
            </Link>

            <div className="mt-3 flex items-center gap-2">
                <span className="text-xs font-medium text-slate-400">Modul {module.order}</span>
                <LevelBadge level={module.level} label={module.level_label} />
            </div>
            <h1 className="mt-1 text-xl font-semibold text-slate-900">{module.title}</h1>
            {module.description && <p className="mt-1 text-sm text-slate-500">{module.description}</p>}

            {is_locked && (
                <div className="mt-4 flex items-center gap-2 rounded-md bg-amber-50 px-3 py-2 text-sm text-amber-700">
                    <Lock size={16} aria-hidden="true" />
                    Selesaikan modul sebelumnya untuk membuka modul ini.
                </div>
            )}

            <div className="mt-6 divide-y divide-slate-100 overflow-hidden rounded-lg border border-slate-200 bg-white">
                {lessons.map((lesson, index) => {
                    const row = (
                        <div
                            className={`flex items-center justify-between px-4 py-3 ${
                                lesson.is_locked ? 'opacity-50' : 'hover:bg-slate-50'
                            }`}
                        >
                            <div className="flex items-center gap-3">
                                <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-medium text-slate-500">
                                    {index + 1}
                                </span>
                                <span className="text-sm text-slate-800">{lesson.title}</span>
                            </div>
                            <div className="flex shrink-0 items-center gap-3">
                                <span className="text-xs text-slate-400">{lesson.estimated_minutes} menit</span>
                                {lesson.is_completed && <CheckCircle2 size={16} className="text-emerald-600" aria-hidden="true" />}
                                {lesson.is_locked && <Lock size={16} className="text-slate-400" aria-hidden="true" />}
                            </div>
                        </div>
                    );

                    return lesson.is_locked ? (
                        <div key={lesson.slug}>{row}</div>
                    ) : (
                        <Link key={lesson.slug} href={route('learning.lessons.show', [module.slug, lesson.slug])}>
                            {row}
                        </Link>
                    );
                })}
            </div>
        </AppLayout>
    );
}
