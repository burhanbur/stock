import { useEffect, useRef, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { GlossaryTerm } from '@/types/learning';

interface GlossaryProps {
    terms: GlossaryTerm[];
    search: string | null;
}

export default function Glossary({ terms, search }: GlossaryProps) {
    const [query, setQuery] = useState(search ?? '');
    const isFirstRender = useRef(true);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const timeout = setTimeout(() => {
            router.get(route('learning.glossary'), { search: query || undefined }, { preserveState: true, replace: true });
        }, 300);

        return () => clearTimeout(timeout);
    }, [query]);

    return (
        <AppLayout>
            <Head title="Kamus Istilah Saham" />

            <h1 className="text-xl font-semibold text-slate-900">Kamus Istilah Saham</h1>
            <p className="mt-1 text-sm text-slate-500">Cari istilah yang belum kamu pahami dari pelajaran manapun.</p>

            <input
                type="text"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Cari istilah, misalnya ROE, dividen, IPO..."
                className="mt-4 w-full max-w-md rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            />

            <div className="mt-6 space-y-4">
                {terms.length === 0 && <p className="text-sm text-slate-400">Tidak ada istilah yang cocok.</p>}

                {terms.map((term) => (
                    <div key={term.slug} id={term.slug} className="rounded-lg border border-slate-200 bg-white p-4">
                        <div className="flex items-baseline gap-2">
                            <h2 className="text-sm font-semibold text-slate-900">{term.term}</h2>
                            {term.full_name && <span className="text-sm text-slate-400">({term.full_name})</span>}
                        </div>
                        <p className="mt-1.5 text-sm text-slate-700">{term.simple_definition}</p>

                        {term.formal_definition && (
                            <p className="mt-2 text-xs text-slate-500">
                                <span className="font-medium">Definisi formal:</span> {term.formal_definition}
                            </p>
                        )}
                        {term.example && (
                            <p className="mt-1 text-xs text-slate-500">
                                <span className="font-medium">Contoh:</span> {term.example}
                            </p>
                        )}
                        {term.application_usage && (
                            <p className="mt-2 rounded-md bg-blue-50 px-3 py-2 text-xs text-blue-800">{term.application_usage}</p>
                        )}
                    </div>
                ))}
            </div>
        </AppLayout>
    );
}
