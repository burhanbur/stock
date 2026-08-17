import { ComponentPropsWithoutRef } from 'react';
import ReactMarkdown, { type Components } from 'react-markdown';
import remarkGfm from 'remark-gfm';

const components: Components = {
    h2: (props: ComponentPropsWithoutRef<'h2'>) => (
        <h2 className="mt-8 mb-3 text-lg font-semibold text-slate-900 first:mt-0" {...props} />
    ),
    h3: (props: ComponentPropsWithoutRef<'h3'>) => <h3 className="mt-6 mb-2 text-base font-semibold text-slate-900" {...props} />,
    p: (props: ComponentPropsWithoutRef<'p'>) => <p className="mb-4 leading-relaxed text-slate-700" {...props} />,
    ul: (props: ComponentPropsWithoutRef<'ul'>) => <ul className="mb-4 list-disc space-y-1 pl-5 text-slate-700" {...props} />,
    ol: (props: ComponentPropsWithoutRef<'ol'>) => <ol className="mb-4 list-decimal space-y-1 pl-5 text-slate-700" {...props} />,
    li: (props: ComponentPropsWithoutRef<'li'>) => <li className="leading-relaxed" {...props} />,
    strong: (props: ComponentPropsWithoutRef<'strong'>) => <strong className="font-semibold text-slate-900" {...props} />,
    blockquote: (props: ComponentPropsWithoutRef<'blockquote'>) => (
        <blockquote className="mb-4 border-l-2 border-blue-200 bg-blue-50/50 py-2 pl-4 text-slate-700 italic" {...props} />
    ),
    code: (props: ComponentPropsWithoutRef<'code'>) => (
        <code className="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[0.85em] text-slate-800" {...props} />
    ),
    table: (props: ComponentPropsWithoutRef<'table'>) => (
        <div className="mb-4 overflow-x-auto rounded-md border border-slate-200">
            <table className="min-w-full divide-y divide-slate-200 text-sm" {...props} />
        </div>
    ),
    thead: (props: ComponentPropsWithoutRef<'thead'>) => <thead className="bg-slate-50" {...props} />,
    th: (props: ComponentPropsWithoutRef<'th'>) => (
        <th className="px-3 py-2 text-left text-xs font-medium tracking-wide text-slate-500 uppercase" {...props} />
    ),
    td: (props: ComponentPropsWithoutRef<'td'>) => <td className="border-t border-slate-100 px-3 py-2 text-slate-700" {...props} />,
    hr: (props: ComponentPropsWithoutRef<'hr'>) => <hr className="my-6 border-slate-200" {...props} />,
};

interface LessonContentProps {
    content: string;
}

export default function LessonContent({ content }: LessonContentProps) {
    return (
        <div className="text-sm">
            <ReactMarkdown remarkPlugins={[remarkGfm]} components={components}>
                {content}
            </ReactMarkdown>
        </div>
    );
}
