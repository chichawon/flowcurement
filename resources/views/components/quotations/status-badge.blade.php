@props(['status'])

@php
    $classes = [
        'draft' => 'bg-slate-600 text-white',
        'sent' => 'bg-cyan-600 text-white',
        'approved' => 'bg-emerald-600 text-white',
        'rejected' => 'bg-red-600 text-white',
        'expired' => 'bg-amber-600 text-white',
        'converted' => 'bg-violet-600 text-white',
    ][$status] ?? 'bg-slate-600 text-white';
@endphp

<span {{ $attributes->class(['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', $classes]) }}>
    {{ str($status)->headline() }}
</span>
