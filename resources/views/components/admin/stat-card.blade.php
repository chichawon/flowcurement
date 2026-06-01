@props([
    'label',
    'value',
    'meta' => null,
    'tone' => 'cyan',
])

@php
    $tones = [
        'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-100',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-100',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $value }}</p>
        </div>
        <span class="rounded-md px-2.5 py-1 text-xs font-semibold ring-1 {{ $tones[$tone] ?? $tones['cyan'] }}">{{ $meta }}</span>
    </div>
    {{ $slot }}
</div>
