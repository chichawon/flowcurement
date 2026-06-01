@props(['status'])

<span @class([
    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
    'bg-emerald-600 text-white' => $status === 'active',
    'bg-slate-600 text-white' => $status !== 'active',
])>
    {{ str($status)->headline() }}
</span>
