import { useEffect, useRef, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Check, RefreshCw, Scale, Search, Star, X } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import DataTable, { DataTableColumn } from '@/Components/DataTable';
import Pagination from '@/Components/Pagination';
import WatchlistButton from '@/Components/WatchlistButton';
import { recommendationStyleFor } from '@/lib/recommendationStyles';
import { PaginatedResponse, Sector, StockListFilters, StockListItem } from '@/types/stock';

interface StocksIndexProps {
    stocks: PaginatedResponse<StockListItem>;
    sectors: Sector[];
    filters: StockListFilters;
}

function formatPrice(value: number | null, currency: string): string {
    if (value === null) {
        return '-';
    }

    return `${currency} ${value.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
}

function tickerAccent(ticker: string): string {
    const palette = [
        'bg-blue-50 text-blue-700',
        'bg-violet-50 text-violet-700',
        'bg-teal-50 text-teal-700',
        'bg-amber-50 text-amber-700',
        'bg-rose-50 text-rose-700',
        'bg-cyan-50 text-cyan-700',
    ];
    const index = ticker.split('').reduce((sum, char) => sum + char.charCodeAt(0), 0) % palette.length;

    return palette[index]!;
}

export default function StocksIndex({ stocks, sectors, filters }: StocksIndexProps) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [sectorId, setSectorId] = useState(filters.sector_id ?? '');
    const [watchlistOnly, setWatchlistOnly] = useState(Boolean(filters.watchlist_only));
    const [syncing, setSyncing] = useState(false);
    const [selected, setSelected] = useState<string[]>([]);
    const isFirstRender = useRef(true);

    const MAX_COMPARE = 3;

    const toggleSelect = (ticker: string) => {
        setSelected((prev) => {
            if (prev.includes(ticker)) return prev.filter((t) => t !== ticker);
            if (prev.length >= MAX_COMPARE) return prev;
            return [...prev, ticker];
        });
    };

    const syncPrices = () => {
        router.post(
            route('stocks.sync-prices'),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => setSyncing(true),
                onFinish: () => setSyncing(false),
            },
        );
    };

    const visit = (overrides: Record<string, string | number | undefined> = {}) => {
        const params: Record<string, string | number> = {};
        if (search) params.search = search;
        if (sectorId) params.sector_id = sectorId;
        if (filters.sort) params.sort = filters.sort;
        if (watchlistOnly) params.watchlist_only = 1;

        Object.entries(overrides).forEach(([key, value]) => {
            if (value === undefined || value === '') {
                delete params[key];
            } else {
                params[key] = value;
            }
        });

        router.get(route('stocks.index'), params, { preserveState: true, preserveScroll: true, replace: true });
    };

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const timeout = setTimeout(() => visit(), 300);

        return () => clearTimeout(timeout);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, sectorId, watchlistOnly]);

    const columns: DataTableColumn<StockListItem>[] = [
        {
            key: 'compare',
            header: '',
            render: (row) => {
                const isSelected = selected.includes(row.ticker);
                const disabled = !isSelected && selected.length >= MAX_COMPARE;

                return (
                    <button
                        type="button"
                        onClick={(e) => {
                            e.stopPropagation();
                            toggleSelect(row.ticker);
                        }}
                        disabled={disabled}
                        title={isSelected ? 'Hapus dari perbandingan' : 'Tambahkan ke perbandingan'}
                        className={`flex h-5 w-5 items-center justify-center rounded border transition-colors ${
                            isSelected ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white hover:border-blue-400'
                        } ${disabled ? 'cursor-not-allowed opacity-40' : ''}`}
                    >
                        {isSelected && <Check size={12} strokeWidth={3} aria-hidden="true" />}
                    </button>
                );
            },
        },
        {
            key: 'watchlist',
            header: '',
            render: (row) => <WatchlistButton ticker={row.ticker} isWatchlisted={row.is_watchlisted} size={18} />,
        },
        {
            key: 'ticker',
            header: 'Ticker',
            sortKey: 'ticker',
            render: (row) => (
                <div className="flex items-center gap-3">
                    <div
                        className={`flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xs font-bold ${tickerAccent(row.ticker)}`}
                    >
                        {row.ticker.slice(0, 2)}
                    </div>
                    <div>
                        <div className="font-semibold text-slate-900">{row.ticker}</div>
                        <div className="text-xs text-slate-400">{row.exchange}</div>
                    </div>
                </div>
            ),
        },
        {
            key: 'company',
            header: 'Perusahaan',
            sortKey: 'company_name',
            render: (row) => <span className="text-slate-700">{row.company_name ?? '-'}</span>,
        },
        {
            key: 'sector',
            header: 'Sektor',
            render: (row) =>
                row.sector ? (
                    <span className="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                        {row.sector.name}
                    </span>
                ) : (
                    <span className="text-slate-400">-</span>
                ),
        },
        {
            key: 'recommendation',
            header: 'Rekomendasi',
            render: (row) => (
                <span
                    className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-medium ${recommendationStyleFor(row.recommendation.label).badge}`}
                >
                    {row.recommendation.label}
                </span>
            ),
        },
        {
            key: 'latest_close',
            header: 'Harga Terakhir',
            align: 'right',
            sortKey: 'latest_close',
            render: (row) => <span className="font-medium tabular-nums text-slate-900">{formatPrice(row.latest_close, row.currency)}</span>,
        },
        {
            key: 'latest_trading_date',
            header: 'Tanggal',
            align: 'right',
            render: (row) => <span className="text-slate-400">{row.latest_trading_date ?? '-'}</span>,
        },
    ];

    return (
        <AppLayout>
            <Head title="Saham" />

            <div className="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight text-slate-900">Daftar Saham</h1>
                    <p className="mt-1 text-sm text-slate-500">
                        <span className="font-medium tabular-nums text-slate-700">{stocks.total}</span> saham terdaftar di IDX (data
                        pengembangan)
                    </p>
                </div>

                <button
                    type="button"
                    onClick={syncPrices}
                    disabled={syncing}
                    className="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm shadow-blue-600/30 transition-opacity hover:opacity-95 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <RefreshCw size={16} className={syncing ? 'animate-spin' : ''} aria-hidden="true" />
                    {syncing ? 'Menyinkronkan...' : 'Sync Data'}
                </button>
            </div>

            <div className="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                <div className="relative min-w-[16rem] flex-1">
                    <Search size={16} className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true" />
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Cari ticker atau nama perusahaan..."
                        className="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                    />
                </div>
                <select
                    value={sectorId}
                    onChange={(e) => setSectorId(e.target.value)}
                    className="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                    <option value="">Semua Sektor</option>
                    {sectors.map((sector) => (
                        <option key={sector.id} value={sector.id}>
                            {sector.name}
                        </option>
                    ))}
                </select>
                <button
                    type="button"
                    onClick={() => setWatchlistOnly((prev) => !prev)}
                    className={`inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-medium transition-colors ${
                        watchlistOnly
                            ? 'border-amber-200 bg-amber-50 text-amber-700'
                            : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100'
                    }`}
                >
                    <Star size={14} fill={watchlistOnly ? 'currentColor' : 'none'} aria-hidden="true" />
                    Hanya Watchlist
                </button>
            </div>

            <div className={selected.length > 0 ? 'pb-20' : ''}>
                <DataTable
                    columns={columns}
                    rows={stocks.data}
                    rowKey={(row) => row.id}
                    onRowClick={(row) => router.visit(route('stocks.show', row.ticker))}
                    emptyMessage={watchlistOnly ? 'Belum ada saham di watchlist kamu.' : 'Tidak ada saham yang cocok dengan pencarian.'}
                    sort={filters.sort}
                    onSort={(nextSort) => visit({ sort: nextSort })}
                />

                <Pagination links={stocks.links} />
            </div>

            {selected.length > 0 && (
                <div className="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-lg backdrop-blur sm:px-6 lg:pl-64">
                    <div className="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3">
                        <div className="flex flex-wrap items-center gap-2">
                            <span className="text-sm text-slate-500">Bandingkan:</span>
                            {selected.map((ticker) => (
                                <span
                                    key={ticker}
                                    className="inline-flex items-center gap-1 rounded-full bg-blue-50 py-1 pl-2.5 pr-1.5 text-xs font-medium text-blue-700"
                                >
                                    {ticker}
                                    <button
                                        type="button"
                                        onClick={() => toggleSelect(ticker)}
                                        className="rounded-full p-0.5 text-blue-400 hover:bg-blue-100 hover:text-blue-700"
                                    >
                                        <X size={12} aria-hidden="true" />
                                    </button>
                                </span>
                            ))}
                        </div>
                        <div className="flex items-center gap-3">
                            <button type="button" onClick={() => setSelected([])} className="text-sm text-slate-500 hover:text-slate-700">
                                Batal
                            </button>
                            <button
                                type="button"
                                disabled={selected.length < 2}
                                onClick={() => router.visit(route('stocks.compare', { tickers: selected.join(',') }))}
                                className="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm shadow-blue-600/30 transition-opacity hover:opacity-95 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Scale size={16} aria-hidden="true" />
                                Bandingkan ({selected.length})
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
