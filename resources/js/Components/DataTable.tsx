import { ReactNode } from 'react';
import { ArrowDown, ArrowUp, ArrowUpDown, Inbox } from 'lucide-react';

export interface DataTableColumn<T> {
    key: string;
    header: string;
    render: (row: T) => ReactNode;
    align?: 'left' | 'right';
    /** Present + `onSort` on the table makes this column's header clickable. */
    sortKey?: string;
}

interface DataTableProps<T> {
    columns: DataTableColumn<T>[];
    rows: T[];
    rowKey: (row: T) => string;
    onRowClick?: (row: T) => void;
    emptyMessage?: string;
    /** Current sort string, e.g. "ticker" (asc) or "-ticker" (desc). */
    sort?: string | null;
    onSort?: (nextSort: string) => void;
}

function nextSortFor(currentSort: string | null | undefined, sortKey: string): string {
    return currentSort === sortKey ? `-${sortKey}` : sortKey;
}

export default function DataTable<T>({
    columns,
    rows,
    rowKey,
    onRowClick,
    emptyMessage = 'Tidak ada data.',
    sort,
    onSort,
}: DataTableProps<T>) {
    return (
        <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                    <thead className="bg-slate-50/80">
                        <tr>
                            {columns.map((column) => {
                                const isSortable = Boolean(column.sortKey && onSort);
                                const isDesc = sort === `-${column.sortKey}`;
                                const isActive = isSortable && (sort === column.sortKey || isDesc);

                                return (
                                    <th
                                        key={column.key}
                                        className={`px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 ${
                                            column.align === 'right' ? 'text-right' : 'text-left'
                                        }`}
                                    >
                                        {isSortable ? (
                                            <button
                                                type="button"
                                                onClick={() => onSort!(nextSortFor(sort, column.sortKey!))}
                                                className={`inline-flex items-center gap-1 transition-colors hover:text-slate-700 ${
                                                    column.align === 'right' ? 'flex-row-reverse' : ''
                                                } ${isActive ? 'text-slate-700' : ''}`}
                                            >
                                                {column.header}
                                                {isActive ? (
                                                    isDesc ? (
                                                        <ArrowDown size={12} aria-hidden="true" />
                                                    ) : (
                                                        <ArrowUp size={12} aria-hidden="true" />
                                                    )
                                                ) : (
                                                    <ArrowUpDown size={12} className="text-slate-300" aria-hidden="true" />
                                                )}
                                            </button>
                                        ) : (
                                            column.header
                                        )}
                                    </th>
                                );
                            })}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {rows.length === 0 && (
                            <tr>
                                <td colSpan={columns.length} className="px-4 py-12">
                                    <div className="flex flex-col items-center justify-center gap-2 text-slate-400">
                                        <Inbox size={28} strokeWidth={1.5} aria-hidden="true" />
                                        <span className="text-sm">{emptyMessage}</span>
                                    </div>
                                </td>
                            </tr>
                        )}
                        {rows.map((row) => (
                            <tr
                                key={rowKey(row)}
                                onClick={onRowClick ? () => onRowClick(row) : undefined}
                                className={onRowClick ? 'cursor-pointer transition-colors hover:bg-slate-50' : undefined}
                            >
                                {columns.map((column) => (
                                    <td
                                        key={column.key}
                                        className={`px-4 py-3.5 text-slate-700 ${column.align === 'right' ? 'text-right' : 'text-left'}`}
                                    >
                                        {column.render(row)}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
