import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { PaginationLink } from '@/types/stock';

interface PaginationProps {
    links: PaginationLink[];
}

export default function Pagination({ links }: PaginationProps) {
    if (links.length <= 3) {
        return null;
    }

    return (
        <nav className="mt-4 flex flex-wrap items-center justify-center gap-1">
            {links.map((link, index) => {
                const isPrev = index === 0;
                const isNext = index === links.length - 1;
                const icon = isPrev ? <ChevronLeft size={16} /> : isNext ? <ChevronRight size={16} /> : null;

                return (
                    <Link
                        key={index}
                        href={link.url ?? '#'}
                        preserveState
                        preserveScroll
                        className={`flex h-8 min-w-8 items-center justify-center rounded-lg px-2.5 text-sm font-medium transition-colors ${
                            link.active
                                ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30'
                                : link.url
                                  ? 'text-slate-600 hover:bg-slate-100'
                                  : 'cursor-not-allowed text-slate-300'
                        }`}
                    >
                        {icon ?? <span dangerouslySetInnerHTML={{ __html: link.label }} />}
                    </Link>
                );
            })}
        </nav>
    );
}
