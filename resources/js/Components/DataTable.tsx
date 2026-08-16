import { ReactNode } from 'react';

export interface DataTableColumn<T> {
    key: string;
    header: string;
    render: (row: T) => ReactNode;
    align?: 'left' | 'right';
}

interface DataTableProps<T> {
    columns: DataTableColumn<T>[];
    rows: T[];
    rowKey: (row: T) => string;
    onRowClick?: (row: T) => void;
    emptyMessage?: string;
}

export default function DataTable<T>({ columns, rows, rowKey, onRowClick, emptyMessage = 'Tidak ada data.' }: DataTableProps<T>) {
    return (
        <div className="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table className="min-w-full divide-y divide-slate-200 text-sm">
                <thead className="bg-slate-50">
                    <tr>
                        {columns.map((column) => (
                            <th
                                key={column.key}
                                className={`px-4 py-2.5 text-xs font-medium uppercase tracking-wide text-slate-500 ${
                                    column.align === 'right' ? 'text-right' : 'text-left'
                                }`}
                            >
                                {column.header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                    {rows.length === 0 && (
                        <tr>
                            <td colSpan={columns.length} className="px-4 py-8 text-center text-slate-400">
                                {emptyMessage}
                            </td>
                        </tr>
                    )}
                    {rows.map((row) => (
                        <tr
                            key={rowKey(row)}
                            onClick={onRowClick ? () => onRowClick(row) : undefined}
                            className={onRowClick ? 'cursor-pointer hover:bg-slate-50' : undefined}
                        >
                            {columns.map((column) => (
                                <td
                                    key={column.key}
                                    className={`px-4 py-3 text-slate-700 ${column.align === 'right' ? 'text-right' : 'text-left'}`}
                                >
                                    {column.render(row)}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
