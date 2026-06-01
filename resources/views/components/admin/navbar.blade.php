<header class="fixed right-0 top-0 z-30 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur transition-all duration-200" :style="{ left: sidebarCollapsed ? '6rem' : '18rem' }">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            <button type="button" class="rounded-md p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 lg:hidden" @click="sidebarOpen = true" aria-label="Open sidebar">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <div class="min-w-0">
                <p class="truncate text-xs font-semibold uppercase tracking-wider text-slate-500">Enterprise Resource Planning</p>
                <!-- <h1 class="truncate text-base font-semibold text-slate-950 sm:text-lg">{{ $title ?? 'Dashboard' }}</h1> -->
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-3">
            <!-- <button type="button" class="hidden rounded-md border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:inline-flex">Quick Create</button> -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        <span class="grid size-8 place-items-center rounded-md bg-slate-900 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden max-w-40 truncate sm:block">{{ auth()->user()->name }}</span>
                        <svg class="size-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</header>
