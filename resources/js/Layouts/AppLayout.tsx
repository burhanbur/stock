import { PropsWithChildren } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { SharedPageProps } from '@/types';

const nav = [
    { label: 'Saham', href: () => route('stocks.index'), match: 'stocks.*' },
    { label: 'Belajar', href: () => route('learning.index'), match: 'learning.*' },
];

export default function AppLayout({ children }: PropsWithChildren) {
    const { auth, flash } = usePage<SharedPageProps>().props;

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900">
            <header className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
                    <div className="flex items-center gap-6">
                        <Link href={route('stocks.index')} className="text-sm font-semibold tracking-tight text-slate-900">
                            Stock Recommendation
                        </Link>
                        <nav className="flex items-center gap-1">
                            {nav.map((item) => (
                                <Link
                                    key={item.label}
                                    href={item.href()}
                                    className={`rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${
                                        route().current(item.match)
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                                    }`}
                                >
                                    {item.label}
                                </Link>
                            ))}
                        </nav>
                    </div>

                    <div className="flex items-center gap-4">
                        {auth.user && <span className="text-sm text-slate-500">{auth.user.name}</span>}
                        <button
                            type="button"
                            onClick={() => router.post(route('logout'))}
                            className="text-sm text-slate-500 underline-offset-2 hover:text-slate-900 hover:underline"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </header>

            {flash.notification && (
                <div
                    className={`mx-auto mt-4 max-w-6xl rounded-md px-4 py-2 text-sm sm:mx-auto ${
                        flash.notification.level === 'error'
                            ? 'bg-red-50 text-red-700'
                            : 'bg-emerald-50 text-emerald-700'
                    }`}
                >
                    {flash.notification.message}
                </div>
            )}

            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6">{children}</main>

            <footer className="mx-auto max-w-6xl px-4 py-6 text-xs text-slate-400 sm:px-6">
                Data pengembangan bersifat sintetis dan hanya untuk keperluan demo — bukan data pasar riil.
            </footer>
        </div>
    );
}
