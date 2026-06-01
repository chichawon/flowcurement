<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login | {{ config('app.name', 'Flowcurement') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-8">
            <section class="w-full max-w-sm">
                <div class="mb-6 text-center">
                    <div class="mx-auto grid size-12 place-items-center rounded-lg bg-slate-950 text-sm font-bold text-cyan-300">
                        FC
                    </div>
                    <h1 class="mt-4 text-2xl font-semibold text-slate-950">Flowcurement</h1>
                    <p class="mt-1 text-sm text-slate-500">Sign in to continue</p>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
                            <input
                                id="username"
                                class="mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                autofocus
                                autocomplete="username"
                            >
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <input
                                id="password"
                                class="mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-cyan-500 focus:ring-cyan-500"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                            >
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center gap-2">
                                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-cyan-600 shadow-sm focus:ring-cyan-500" name="remember">
                                <span class="text-sm text-slate-600">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm font-medium text-cyan-700 hover:text-cyan-800" href="{{ route('password.request') }}">
                                    Forgot?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="w-full rounded-md bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                            Log in
                        </button>
                    </form>
                </div>

                <p class="mt-5 text-center text-xs text-slate-500">
                    &copy; {{ date('Y') }} Flowcurement ERP
                </p>
            </section>
        </main>
    </body>
</html>
