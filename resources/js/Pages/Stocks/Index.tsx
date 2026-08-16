import { useEffect, useRef, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
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

export default function StocksIndex({ stocks, sectors, filters }: StocksIndexProps) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [sectorId, setSectorId] = useState(filters.sector_id ?? '');
    const isFirstRender = useRef(true);

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
                <div>
                    <div className="font-semibold text-slate-900">{row.ticker}</div>
                    <div className="text-xs text-slate-400">{row.exchange}</div>
                </div>
            ),
        },
        {
            key: 'company',
            header: 'Perusahaan',
            render: (row) => row.company_name ?? '-',
        },
        {
            key: 'sector',
            header: 'Sektor',
            render: (row) =>
                row.sector ? (
                    <span className="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
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
            render: (row) => <span className="tabular-nums">{formatPrice(row.latest_close, row.currency)}</span>,
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

            <div className="mb-6 flex items-end justify-between gap-4">
                <div>
                    <h1 className="text-xl font-semibold text-slate-900">Daftar Saham</h1>
                    <p className="mt-1 text-sm text-slate-500">{stocks.total} saham terdaftar di IDX (data pengembangan)</p>
                </div>
            </div>

            <div className="mb-4 flex flex-wrap gap-3">
                <input
                    type="text"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder="Cari ticker atau nama perusahaan..."
                    className="w-72 rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
                <select
                    value={sectorId}
                    onChange={(e) => setSectorId(e.target.value)}
                    className="rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                <nav className="mt-4 flex flex-wrap items-center gap-1">
                    {stocks.links.map((link, index) => (
                        <Link
                            key={index}
                            href={link.url ?? '#'}
                            preserveState
                            className={`rounded-md px-3 py-1.5 text-sm ${
                                link.active
                                    ? 'bg-blue-600 text-white'
                                    : link.url
                                      ? 'text-slate-600 hover:bg-slate-100'
                                      : 'cursor-not-allowed text-slate-300'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ))}
                </nav>
            )}
        </AppLayout>
    );
}
