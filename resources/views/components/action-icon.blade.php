@props(['name'])

@php
    $paths = [
        'view' => '<path d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12s-3.5 6.75-9.75 6.75S2.25 12 2.25 12Z"/><path d="M12 15.25A3.25 3.25 0 1 0 12 8.75a3.25 3.25 0 0 0 0 6.5Z"/>',
        'edit' => '<path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/><path d="M19.5 7.125 16.875 4.5"/><path d="M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>',
        'print' => '<path d="M6 9V3h12v6"/><path d="M6 18H4.5A2.5 2.5 0 0 1 2 15.5v-4A2.5 2.5 0 0 1 4.5 9h15a2.5 2.5 0 0 1 2.5 2.5v4A2.5 2.5 0 0 1 19.5 18H18"/><path d="M6 14h12v7H6z"/><path d="M18 12h.01"/>',
        'delete' => '<path d="M4 7h16"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M5 7l1 14h12l1-14"/><path d="M9 7V4h6v3"/>',
        'restore' => '<path d="M3 12a9 9 0 1 0 3-6.708"/><path d="M3 4v6h6"/><path d="M12 7v5l3 2"/>',
        'upload' => '<path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"/>',
        'cancel' => '<path d="m6 6 12 12"/><path d="m18 6-12 12"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'size-4 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths[$name] ?? $paths['view'] !!}
</svg>
