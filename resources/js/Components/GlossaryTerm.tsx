import { ReactNode, useEffect, useRef, useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { SharedPageProps } from '@/types';

interface GlossaryTermProps {
    /** Slug in learning_glossary_terms, e.g. "momentum" or "rata-rata-bergerak". */
    slug: string;
    /** Override display text — defaults to the term's own label. */
    children?: ReactNode;
}

/**
 * Inline glossary reference: click to reveal a definition popover sourced
 * from the `glossaryTerms` shared Inertia prop (no network round trip), so
 * a beginner reading a stock/lesson page doesn't lose their place
 * navigating to the full glossary. Falls back to plain text if the slug
 * isn't found (e.g. a lesson references a term not yet in the glossary).
 */
export default function GlossaryTerm({ slug, children }: GlossaryTermProps) {
    const { glossaryTerms } = usePage<SharedPageProps>().props;
    const [open, setOpen] = useState(false);
    const containerRef = useRef<HTMLSpanElement>(null);

    const entry = glossaryTerms[slug];

    useEffect(() => {
        if (!open) return;

        const handleClickOutside = (e: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);

        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [open]);

    if (!entry) {
        return <span>{children ?? slug}</span>;
    }

    return (
        <span ref={containerRef} className="relative inline-block">
            <button
                type="button"
                onClick={() => setOpen((o) => !o)}
                className="underline decoration-dotted underline-offset-2 hover:text-blue-700"
            >
                {children ?? entry.term}
            </button>

            {open && (
                <span className="absolute left-0 top-full z-20 mt-1.5 block w-64 rounded-lg border border-slate-200 bg-white p-3 text-left text-xs normal-case shadow-lg">
                    <span className="block text-sm font-semibold text-slate-900">
                        {entry.term}
                        {entry.full_name && <span className="font-normal text-slate-400"> ({entry.full_name})</span>}
                    </span>
                    <span className="mt-1 block leading-relaxed text-slate-600">{entry.simple_definition}</span>
                    <Link
                        href={route('learning.glossary', { search: entry.term })}
                        className="mt-2 inline-block font-medium text-blue-600 hover:underline"
                    >
                        Lihat di glosarium →
                    </Link>
                </span>
            )}
        </span>
    );
}
