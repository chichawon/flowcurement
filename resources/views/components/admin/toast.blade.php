@props([
    'message' => null,
    'type' => 'success',
])

<div
    x-data="{
        show: false,
        message: @js($message),
        type: @js($type),
        timeout: null,
        styles: {
            success: {
                icon: 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                iconClass: 'bg-emerald-100 text-emerald-700',
                barClass: 'bg-emerald-500',
            },
            danger: {
                icon: 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
                iconClass: 'bg-red-100 text-red-700',
                barClass: 'bg-red-500',
            },
            info: {
                icon: 'm11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z',
                iconClass: 'bg-cyan-100 text-cyan-700',
                barClass: 'bg-cyan-500',
            },
        },
        currentStyle() {
            return this.styles[this.type] || this.styles.success;
        },
        open(message, type = 'success') {
            this.message = message;
            this.type = type || 'success';
            this.show = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.show = false, 3500);
        },
    }"
    x-init="if (message) open(message, type)"
    x-on:toast.window="open($event.detail.message, $event.detail.type)"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed right-4 top-20 z-[60] w-[calc(100%-2rem)] max-w-sm overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl sm:right-6"
    role="status"
>
    <div class="flex items-start gap-3 p-4">
        <div class="grid size-9 shrink-0 place-items-center rounded-full" :class="currentStyle().iconClass">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" :d="currentStyle().icon" />
            </svg>
        </div>

        <div class="min-w-0 flex-1 pt-0.5">
            <p class="text-sm font-semibold text-slate-950" x-text="message"></p>
        </div>

        <button type="button" class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="show = false" aria-label="Dismiss notification">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="h-1" :class="currentStyle().barClass"></div>
</div>
