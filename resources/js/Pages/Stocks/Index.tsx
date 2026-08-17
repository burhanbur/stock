import { useEffect, useRef, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, RefreshCw, Search } from 'lucide-react';
import AppLayout from '@/Layouts/AppLayout';
import DataTable, { DataTableColumn } from '@/Components/DataTable';
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
    const [syncing, setSyncing] = useState(false);
    const isFirstRender = useRef(true);

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

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const timeout = setTimeout(() => {
            router.get(
                route('stocks.index'),
                { search: search || undefined, sector_id: sectorId || undefined, sort: filters.sort || undefined },
                { preserveState: true, replace: true },
            );
        }, 300);

        return () => clearTimeout(timeout);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, sectorId]);

    const columns: DataTableColumn<StockListItem>[] = [
        {
            key: 'ticker',
            header: 'Ticker',
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
            key: 'latest_close',
            header: 'Harga Terakhir',
            align: 'right',
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
            </div>

            <DataTable
                columns={columns}
                rows={stocks.data}
                rowKey={(row) => row.id}
                onRowClick={(row) => router.visit(route('stocks.show', row.ticker))}
                emptyMessage="Tidak ada saham yang cocok dengan pencarian."
            />

            {stocks.links.length > 3 && (
                <nav className="mt-4 flex flex-wrap items-center justify-center gap-1">
                    {stocks.links.map((link, index) => {
                        const isPrev = index === 0;
                        const isNext = index === stocks.links.length - 1;
                        const icon = isPrev ? <ChevronLeft size={16} /> : isNext ? <ChevronRight size={16} /> : null;

                        return (
                            <Link
                                key={index}
                                href={link.url ?? '#'}
                                preserveState
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
            )}
        </AppLayout>
    );
}
