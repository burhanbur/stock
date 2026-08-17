import { FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { LineChart, Lock, User } from 'lucide-react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        identity: '',
        password: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <div className="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-50 px-4">
            <Head title="Masuk" />

            <div className="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-blue-200/40 blur-3xl" aria-hidden="true" />
            <div className="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-indigo-200/40 blur-3xl" aria-hidden="true" />

            <div className="relative w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-900/5">
                <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-sm shadow-blue-600/30">
                    <LineChart size={22} strokeWidth={2.5} aria-hidden="true" />
                </div>

                <h1 className="mt-4 text-lg font-semibold text-slate-900">Stock Recommendation</h1>
                <p className="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan.</p>

                <form onSubmit={submit} className="mt-6 space-y-4">
                    <div>
                        <label htmlFor="identity" className="block text-sm font-medium text-slate-700">
                            Email atau Username
                        </label>
                        <div className="relative mt-1.5">
                            <User size={16} className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true" />
                            <input
                                id="identity"
                                type="text"
                                autoFocus
                                value={data.identity}
                                onChange={(e) => setData('identity', e.target.value)}
                                className="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        {errors.identity && <p className="mt-1 text-sm text-red-600">{errors.identity}</p>}
                    </div>

                    <div>
                        <label htmlFor="password" className="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <div className="relative mt-1.5">
                            <Lock size={16} className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true" />
                            <input
                                id="password"
                                type="password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                className="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm transition-colors focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                            />
                        </div>
                        {errors.password && <p className="mt-1 text-sm text-red-600">{errors.password}</p>}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-3 py-2.5 text-sm font-medium text-white shadow-sm shadow-blue-600/30 transition-opacity hover:opacity-95 disabled:opacity-60"
                    >
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    );
}
