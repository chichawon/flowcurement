@props(['value', 'tone' => 'slate'])

@php
    $classes = [
        'slate' => 'bg-slate-100 text-slate-700',
        'cyan' => 'bg-cyan-50 text-cyan-800',
        'amber' => 'bg-amber-50 text-amber-800',
        'emerald' => 'bg-emerald-50 text-emerald-800',
    ][$tone] ?? 'bg-slate-100 text-slate-700';
@endphp

<span class="inline-flex rounded-md px-2 py-1 text-xs font-medium {{ $classes }}">
    {{ $slot->isEmpty() ? str((string) $value)->replace('_', ' ')->headline() : $slot }}
</span>
