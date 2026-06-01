@props([
    'show' => false,
    'maxWidth' => '3xl',
])

@php
    $maxWidthClass = [
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '5xl' => 'sm:max-w-5xl',
    ][$maxWidth] ?? 'sm:max-w-3xl';
@endphp

<div
    wire:show="{{ $show }}"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    role="dialog"
    aria-modal="true"
>
    <div wire:click="$set('{{ $show }}', false)" class="fixed inset-0 bg-slate-950/60"></div>

    <div
        class="relative mx-auto mt-8 w-full {{ $maxWidthClass }} rounded-lg bg-white shadow-xl"
    >
        {{ $slot }}
    </div>
</div>
