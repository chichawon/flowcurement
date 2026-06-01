@props(['status'])

@php
    $classes = [
        'pending' => 'bg-amber-600 text-white',
        'partial' => 'bg-blue-600 text-white',
        'served' => 'bg-emerald-600 text-white',
        'invoiced' => 'bg-cyan-600 text-white',
        'cancelled' => 'bg-red-600 text-white',
    ][$status] ?? 'bg-slate-600 text-white';
@endphp

<span {{ $attributes->class(['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', $classes]) }}>
    {{ str($status)->headline() }}
</span>
