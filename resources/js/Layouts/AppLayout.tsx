import { PropsWithChildren, useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { GraduationCap, LineChart, LogOut, Menu, X } from 'lucide-react';
import { SharedPageProps } from '@/types';

const nav = [
    { label: 'Saham', href: () => route('stocks.index'), match: 'stocks.*', icon: LineChart },
    { label: 'Belajar', href: () => route('learning.index'), match: 'learning.*', icon: GraduationCap },
];

function initials(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

function Brand() {
    return (
        <div className="flex h-16 shrink-0 items-center gap-2.5 px-5">
            <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-sm shadow-blue-600/30">
                <LineChart size={18} strokeWidth={2.5} aria-hidden="true" />
            </div>
            <span className="truncate text-sm font-semibold tracking-tight text-slate-900">Stock Recommendation</span>
        </div>
    );
}

function NavLinks({ onNavigate }: { onNavigate?: () => void }) {
    return (
        <nav className="flex-1 space-y-1 px-3">
            {nav.map((item) => {
                const active = route().current(item.match);
                const Icon = item.icon;

                return (
                    <Link
                        key={item.label}
                        href={item.href()}
                        onClick={onNavigate}
                        className={`group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors ${
                            active ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                        }`}
                    >
                        <Icon
                            size={18}
                            strokeWidth={2}
                            className={active ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-500'}
                            aria-hidden="true"
                        />
                        {item.label}
                    </Link>
                );
            })}
        </nav>
    );
}

function UserCard({ name }: { name: string }) {
    return (
        <div className="border-t border-slate-200 p-3">
            <div className="flex items-center gap-3 rounded-lg px-2 py-2">
                <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-600">
                    {initials(name)}
                </div>
                <div className="min-w-0 flex-1">
                    <div className="truncate text-sm font-medium text-slate-900">{name}</div>
                </div>
                <button
                    type="button"
                    onClick={() => router.post(route('logout'))}
                    title="Keluar"
                    className="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700"
                >
                    <LogOut size={16} aria-hidden="true" />
                </button>
            </div>
        </div>
    );
}

export default function AppLayout({ children }: PropsWithChildren) {
    const { auth, flash } = usePage<SharedPageProps>().props;
    const [mobileOpen, setMobileOpen] = useState(false);

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900">
            {/* Desktop sidebar */}
            <aside className="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-200 bg-white lg:flex">
                <Brand />
                <NavLinks />
                {auth.user && <UserCard name={auth.user.name} />}
            </aside>

            {/* Mobile sidebar drawer */}
            {mobileOpen && (
                <div className="fixed inset-0 z-40 lg:hidden">
                    <div className="absolute inset-0 bg-slate-900/40" onClick={() => setMobileOpen(false)} />
                    <aside className="absolute inset-y-0 left-0 flex w-64 flex-col bg-white shadow-xl">
                        <div className="flex items-center justify-between border-b border-slate-100 pr-3">
                            <Brand />
                            <button
                                type="button"
                                onClick={() => setMobileOpen(false)}
                                className="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                            >
                                <X size={18} aria-hidden="true" />
                            </button>
                        </div>
                        <div className="mt-2">
                            <NavLinks onNavigate={() => setMobileOpen(false)} />
                        </div>
                        {auth.user && <UserCard name={auth.user.name} />}
                    </aside>
                </div>
            )}

            <div className="flex min-h-screen flex-col lg:pl-64">
                <header className="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur sm:px-6 lg:hidden">
                    <button
                        type="button"
                        onClick={() => setMobileOpen(true)}
                        className="flex h-9 w-9 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100"
                    >
                        <Menu size={20} aria-hidden="true" />
                    </button>
                    <span className="text-sm font-semibold text-slate-900">Stock Recommendation</span>
                </header>

                {flash.notification && (
                    <div className="px-4 pt-4 sm:px-6 lg:px-8">
                        <div
                            className={`rounded-lg border px-4 py-2.5 text-sm ${
                                flash.notification.level === 'error'
                                    ? 'border-red-200 bg-red-50 text-red-700'
                                    : 'border-emerald-200 bg-emerald-50 text-emerald-700'
                            }`}
                        >
                            {flash.notification.message}
                        </div>
                    </div>
                )}

                <main className="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    <div className="mx-auto max-w-6xl">{children}</div>
                </main>

                <footer className="px-4 py-6 text-xs text-slate-400 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-6xl">Data pengembangan bersifat sintetis dan hanya untuk keperluan demo — bukan data pasar riil.</div>
                </footer>
            </div>
        </div>
    );
}
