import { useState } from 'react';
import { router } from '@inertiajs/react';
import { CheckCircle2, XCircle } from 'lucide-react';
import { Quiz, QuizAttemptResult } from '@/types/learning';

interface QuizCardProps {
    quiz: Quiz;
    latestAttempt: QuizAttemptResult | null;
}

export default function QuizCard({ quiz, latestAttempt }: QuizCardProps) {
    const [answers, setAnswers] = useState<Record<string, string>>({});
    const [retrying, setRetrying] = useState(false);
    const [submitting, setSubmitting] = useState(false);

    const showResults = latestAttempt !== null && !retrying;
    const resultsByQuestion = showResults
        ? new Map(latestAttempt.results.map((result) => [result.question_id, result]))
        : null;
    const allAnswered = quiz.questions.every((question) => answers[question.id]);

    const submit = () => {
        setSubmitting(true);
        router.post(
            route('learning.quizzes.attempts.store', quiz.id),
            { answers },
            {
                preserveScroll: true,
                onFinish: () => setSubmitting(false),
                onSuccess: () => setRetrying(false),
            },
        );
    };

    return (
        <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div className="mb-4 flex items-center justify-between">
                <h3 className="text-sm font-semibold text-slate-900">{quiz.title}</h3>
                {showResults && (
                    <span className="text-sm font-medium text-slate-600 tabular-nums">
                        Skor: {latestAttempt.correct_answers}/{latestAttempt.total_questions} ({latestAttempt.score}%)
                    </span>
                )}
            </div>

            <div className="space-y-6">
                {quiz.questions.map((question, index) => {
                    const result = resultsByQuestion?.get(question.id);

                    return (
                        <div key={question.id}>
                            <p className="mb-2 text-sm font-medium text-slate-800">
                                {index + 1}. {question.question}
                            </p>
                            <div className="space-y-2">
                                {question.options.map((option) => {
                                    const isSelected = showResults
                                        ? result?.selected_option_id === option.id
                                        : answers[question.id] === option.id;
                                    const isCorrectOption = showResults && result?.correct_option_id === option.id;

                                    let optionClass = 'border-slate-200 hover:border-slate-300';
                                    if (showResults) {
                                        if (isCorrectOption) {
                                            optionClass = 'border-emerald-300 bg-emerald-50';
                                        } else if (isSelected) {
                                            optionClass = 'border-red-300 bg-red-50';
                                        }
                                    } else if (isSelected) {
                                        optionClass = 'border-blue-400 bg-blue-50';
                                    }

                                    return (
                                        <button
                                            key={option.id}
                                            type="button"
                                            disabled={showResults}
                                            onClick={() => setAnswers((prev) => ({ ...prev, [question.id]: option.id }))}
                                            className={`flex w-full items-center justify-between rounded-lg border px-3 py-2 text-left text-sm transition-colors ${optionClass} ${
                                                showResults ? 'cursor-default' : 'cursor-pointer'
                                            }`}
                                        >
                                            <span className="text-slate-700">{option.text}</span>
                                            {showResults && isCorrectOption && (
                                                <CheckCircle2 size={16} className="shrink-0 text-emerald-600" aria-hidden="true" />
                                            )}
                                            {showResults && isSelected && !isCorrectOption && (
                                                <XCircle size={16} className="shrink-0 text-red-500" aria-hidden="true" />
                                            )}
                                        </button>
                                    );
                                })}
                            </div>
                            {showResults && result?.explanation && (
                                <p className="mt-2 rounded-md bg-slate-50 px-3 py-2 text-xs text-slate-600">{result.explanation}</p>
                            )}
                        </div>
                    );
                })}
            </div>

            <div className="mt-5">
                {showResults ? (
                    <button
                        type="button"
                        onClick={() => {
                            setAnswers({});
                            setRetrying(true);
                        }}
                        className="text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        Coba Lagi
                    </button>
                ) : (
                    <button
                        type="button"
                        onClick={submit}
                        disabled={!allAnswered || submitting}
                        className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm shadow-blue-600/30 transition-colors hover:bg-blue-700 disabled:opacity-50 disabled:shadow-none"
                    >
                        Periksa Jawaban
                    </button>
                )}
            </div>
        </div>
    );
}
