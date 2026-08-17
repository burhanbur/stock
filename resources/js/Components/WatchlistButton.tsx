import { MouseEvent, useState } from 'react';
import { router } from '@inertiajs/react';
import { Star } from 'lucide-react';

interface WatchlistButtonProps {
    ticker: string;
    isWatchlisted: boolean;
    size?: number;
    className?: string;
}

export default function WatchlistButton({ ticker, isWatchlisted, size = 18, className = '' }: WatchlistButtonProps) {
    const [processing, setProcessing] = useState(false);

    const toggle = (e: MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();

        router.post(
            route('stocks.watchlist.toggle', ticker),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <button
            type="button"
            onClick={toggle}
            disabled={processing}
            title={isWatchlisted ? 'Hapus dari watchlist' : 'Tambah ke watchlist'}
            className={`flex items-center justify-center rounded-md transition-colors disabled:cursor-not-allowed disabled:opacity-60 ${
                isWatchlisted ? 'text-amber-400 hover:text-amber-500' : 'text-slate-300 hover:text-amber-500'
            } ${className}`}
        >
            <Star size={size} fill={isWatchlisted ? 'currentColor' : 'none'} aria-hidden="true" />
        </button>
    );
}
