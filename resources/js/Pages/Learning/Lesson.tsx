import { router, Head, Link } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, CheckCircle2, Circle } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import LessonContent from '@/Components/Learning/LessonContent';
import QuizCard from '@/Components/Learning/QuizCard';
import { AdjacentLesson, LearningLessonDetail, Quiz, QuizAttemptResult } from '@/types/learning';

interface LessonShowProps {
    module: { slug: string; title: string; level: string };
    lesson: LearningLessonDetail;
    is_completed: boolean;
    previous_lesson: AdjacentLesson | null;
    next_lesson: AdjacentLesson | null;
    quiz: Quiz | null;
    latest_attempt: QuizAttemptResult | null;
}

export default function LessonShow({
    module,
    lesson,
    is_completed,
    previous_lesson,
    next_lesson,
    quiz,
    latest_attempt,
}: LessonShowProps) {
    const markComplete = () => {
        router.post(route('learning.lessons.complete', [module.slug, lesson.slug]), {}, { preserveScroll: true });
    };

    return (
        <AppLayout>
            <Head title={lesson.title} />

            <Link
                href={route('learning.modules.show', module.slug)}
                className="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900 hover:underline"
            >
                <ArrowLeft aria-hidden="true" size={16} />
                {module.title}
            </Link>

            <div className="mt-3 flex items-center justify-between gap-4">
                <h1 className="text-2xl font-semibold tracking-tight text-slate-900">{lesson.title}</h1>
                {is_completed && (
                    <span className="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                        <CheckCircle2 size={12} aria-hidden="true" />
                        Selesai
                    </span>
                )}
            </div>
            <p className="mt-1 text-xs text-slate-400">{lesson.estimated_minutes} menit</p>

            {lesson.learning_objectives.length > 0 && (
                <div className="mt-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 className="mb-2 text-xs font-medium tracking-wide text-slate-400 uppercase">Tujuan Belajar</h2>
                    <ul className="space-y-1.5">
                        {lesson.learning_objectives.map((objective) => (
                            <li key={objective} className="flex items-start gap-2 text-sm text-slate-700">
                                <Circle size={6} className="mt-1.5 shrink-0 fill-blue-400 text-blue-400" aria-hidden="true" />
                                {objective}
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            <div className="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <LessonContent content={lesson.content} />
            </div>

            {lesson.key_terms.length > 0 && (
                <div className="mt-4 flex flex-wrap items-center gap-2">
                    <span className="text-xs font-medium text-slate-400">Istilah kunci:</span>
                    {lesson.key_terms.map((term) => (
                        <Link
                            key={term}
                            href={route('learning.glossary', { search: term })}
                            className="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600 hover:bg-slate-200"
                        >
                            {term}
                        </Link>
                    ))}
                </div>
            )}

            {quiz && (
                <div className="mt-6">
                    <QuizCard quiz={quiz} latestAttempt={latest_attempt} />
                </div>
            )}

            {!quiz && !is_completed && (
                <button
                    type="button"
                    onClick={markComplete}
                    className="mt-6 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm shadow-blue-600/30 transition-colors hover:bg-blue-700"
                >
                    Tandai Selesai
                </button>
            )}

            <div className="mt-8 flex items-center justify-between border-t border-slate-200 pt-4 text-sm">
                {previous_lesson ? (
                    <Link
                        href={route('learning.lessons.show', [module.slug, previous_lesson.slug])}
                        className="inline-flex items-center gap-1 text-slate-600 hover:text-slate-900"
                    >
                        <ArrowLeft size={14} aria-hidden="true" />
                        {previous_lesson.title}
                    </Link>
                ) : (
                    <span />
                )}
                {next_lesson && (
                    <Link
                        href={route('learning.lessons.show', [module.slug, next_lesson.slug])}
                        className="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700"
                    >
                        {next_lesson.title}
                        <ArrowRight size={14} aria-hidden="true" />
                    </Link>
                )}
            </div>
        </AppLayout>
    );
}
