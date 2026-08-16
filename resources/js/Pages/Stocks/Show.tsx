import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import MetricCard from '@/Components/MetricCard';
import ChangeBadge from '@/Components/ChangeBadge';
import StockPriceChart from '@/Components/StockPriceChart';
import DataTable, { DataTableColumn } from '@/Components/DataTable';
import { StockDetail, StockPricePoint } from '@/types/stock';

interface StockShowProps {
    stock: StockDetail;
}

function formatPrice(value: number | null, currency: string): string {
    if (value === null) {
        return '-';
    }

    return `${currency} ${value.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
}

export default function StockShow({ stock }: StockShowProps) {
    const recentPrices = [...stock.prices].reverse().slice(0, 10);

    const columns: DataTableColumn<StockPricePoint>[] = [
        { key: 'date', header: 'Tanggal', render: (row) => row.trading_date },
        { key: 'open', header: 'Open', align: 'right', render: (row) => row.open.toLocaleString('id-ID') },
        { key: 'high', header: 'High', align: 'right', render: (row) => row.high.toLocaleString('id-ID') },
        { key: 'low', header: 'Low', align: 'right', render: (row) => row.low.toLocaleString('id-ID') },
        { key: 'close', header: 'Close', align: 'right', render: (row) => row.close.toLocaleString('id-ID') },
        { key: 'volume', header: 'Volume', align: 'right', render: (row) => row.volume.toLocaleString('id-ID') },
    ];

    return (
        <AppLayout>
            <Head title={`${stock.ticker} - ${stock.company.name}`} />

            <Link href={route('stocks.index')} className="text-sm text-slate-500 hover:text-slate-900 hover:underline">
                &larr; Kembali ke daftar saham
            </Link>

            <div className="mt-3 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div className="flex items-center gap-3">
                        <h1 className="text-2xl font-semibold text-slate-900">{stock.ticker}</h1>
                        <span className="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">{stock.exchange}</span>
                        {!stock.is_active && (
                            <span className="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700">Tidak aktif</span>
                        )}
                    </div>
                    <p className="mt-1 text-slate-600">{stock.company.name}</p>
                    {stock.sector && <p className="mt-0.5 text-sm text-slate-400">{stock.sector.name}</p>}
                </div>

                <div className="text-right">
                    <div className="text-3xl font-semibold tabular-nums text-slate-900">
                        {formatPrice(stock.latest_close, stock.currency)}
                    </div>
                    <div className="mt-1">
                        <ChangeBadge change={stock.change} changePercent={stock.change_percent} />
                    </div>
                    <div className="mt-1 text-xs text-slate-400">per {stock.latest_trading_date ?? '-'}</div>
                </div>
            </div>

            <div className="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <MetricCard label="Sektor" value={stock.sector?.name ?? '-'} />
                <MetricCard label="Papan" value={stock.board ?? '-'} />
                <MetricCard label="Tanggal Listing" value={stock.listed_at ?? '-'} />
                <MetricCard label="Mata Uang" value={stock.currency} />
            </div>

            <div className="mt-6 rounded-lg border border-slate-200 bg-white p-4">
                <h2 className="mb-4 text-sm font-medium text-slate-500">Harga Penutupan — {stock.prices.length} hari terakhir</h2>
                <StockPriceChart prices={stock.prices} currency={stock.currency} />
            </div>

            <div className="mt-6">
                <h2 className="mb-3 text-sm font-medium text-slate-500">Riwayat Harga Terbaru</h2>
                <DataTable columns={columns} rows={recentPrices} rowKey={(row) => row.trading_date} />
            </div>
        </AppLayout>
    );
}
