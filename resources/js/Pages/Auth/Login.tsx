import { FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';

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
        <div className="flex min-h-screen items-center justify-center bg-slate-50 px-4">
            <Head title="Masuk" />

            <div className="w-full max-w-sm rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h1 className="text-lg font-semibold text-slate-900">Stock Recommendation</h1>
                <p className="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan.</p>

                <form onSubmit={submit} className="mt-6 space-y-4">
                    <div>
                        <label htmlFor="identity" className="block text-sm font-medium text-slate-700">
                            Email atau Username
                        </label>
                        <input
                            id="identity"
                            type="text"
                            autoFocus
                            value={data.identity}
                            onChange={(e) => setData('identity', e.target.value)}
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                        {errors.identity && <p className="mt-1 text-sm text-red-600">{errors.identity}</p>}
                    </div>

                    <div>
                        <label htmlFor="password" className="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            className="mt-1 w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                        {errors.password && <p className="mt-1 text-sm text-red-600">{errors.password}</p>}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"
                    >
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    );
}
