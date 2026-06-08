<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Flowcurement') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-100 font-sans text-slate-900 antialiased">
        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen">
            <x-admin.sidebar />
            <x-admin.toast :message="session('toast')" :type="session('toast_type', 'success')" />

            <div class="flex min-h-screen min-w-0 flex-col transition-all duration-200" :style="{ paddingLeft: sidebarCollapsed ? '6rem' : '18rem' }">
                <x-admin.navbar />

                <main class="w-full flex-1 px-4 pb-6 pt-20 sm:px-6 lg:px-8">
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>

                <x-admin.footer />
            </div>
        </div>

        @livewireScripts
        @auth
            <script>
                (() => {
                    const url = @json(route('system.heartbeat'));
                    const ping = () => {
                        if (document.visibilityState !== 'visible') return;
                        fetch(url, {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            cache: 'no-store',
                        }).catch(() => {});
                    };

                    window.setInterval(ping, 120000);
                    document.addEventListener('visibilitychange', ping);
                })();
            </script>
        @endauth
    </body>
</html>
