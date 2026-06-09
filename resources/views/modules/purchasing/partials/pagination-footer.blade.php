@if ($paginator->total() > 0)
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
    @endphp
    <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
        <p class="text-slate-500">
            Showing <span class="font-semibold text-slate-700">{{ $paginator->firstItem() }}</span>
            to <span class="font-semibold text-slate-700">{{ $paginator->lastItem() }}</span>
            of <span class="font-semibold text-slate-700">{{ $paginator->total() }}</span> records
        </p>
        <div class="flex flex-wrap items-center gap-1">
            <button type="button" wire:click="previousPage" @disabled($paginator->onFirstPage()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Previous</button>
            @for ($page = $startPage; $page <= $endPage; $page++)
                <button type="button" wire:click="gotoPage({{ $page }})" @class([
                    'inline-flex size-9 items-center justify-center rounded-md border text-sm font-semibold',
                    'border-slate-950 bg-slate-950 text-white' => $page === $currentPage,
                    'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => $page !== $currentPage,
                ])>{{ $page }}</button>
            @endfor
            <button type="button" wire:click="nextPage" @disabled(! $paginator->hasMorePages()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Next</button>
        </div>
    </div>
@endif
