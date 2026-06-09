<div class="overflow-x-auto">
    <table class="w-full table-fixed border border-slate-300 text-sm">
        <colgroup>
            <col class="w-[12%]">
            <col class="w-[13%]">
            <col class="w-[18%]">
            <col class="w-[41%]">
            <col class="w-[16%]">
        </colgroup>
        <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
            <tr>
                <th class="border border-slate-300 px-4 py-3 text-left">Date / Time</th>
                <th class="border border-slate-300 px-4 py-3 text-left">Module</th>
                <th class="border border-slate-300 px-4 py-3 text-left">Action</th>
                <th class="border border-slate-300 px-4 py-3 text-left">Description</th>
                <th class="border border-slate-300 px-4 py-3 text-left">User</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentLogs as $log)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap border border-slate-300 px-4 py-3 text-slate-700">
                        <p class="font-medium">{{ $log->created_at?->timezone(config('app.timezone'))->format('M d, Y') }}</p>
                        <p class="text-xs text-slate-500">{{ $log->created_at?->timezone(config('app.timezone'))->format('h:i A') }}</p>
                    </td>
                    <td class="border border-slate-300 px-4 py-3 font-semibold text-slate-800">
                        {{ str($log->module)->headline() }}
                    </td>
                    <td class="border border-slate-300 px-4 py-3">
                        <span class="inline-flex max-w-full rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold leading-tight text-cyan-800 ring-1 ring-cyan-200">
                            {{ str($log->action)->headline() }}
                        </span>
                    </td>
                    <td class="border border-slate-300 px-4 py-3 text-slate-700">
                        {{ $log->description ?: 'No description recorded.' }}
                    </td>
                    <td class="whitespace-nowrap border border-slate-300 px-4 py-3 text-slate-700">
                        {{ $log->actor?->name ?? 'System' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="border border-slate-300 px-4 py-8 text-center text-slate-500">No transaction logs yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5 flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm text-slate-600 md:flex-row md:items-center md:justify-between">
    <p>
        Showing {{ $recentLogs->firstItem() ?? 0 }} to {{ $recentLogs->lastItem() ?? 0 }} of {{ $recentLogs->total() }} records
    </p>

    <div class="flex items-center justify-end gap-1">
        @if ($recentLogs->onFirstPage())
            <span class="rounded-md border border-slate-200 px-4 py-2 font-semibold text-slate-400">Previous</span>
        @else
            <a href="{{ $recentLogs->previousPageUrl() }}" class="dashboard-log-page rounded-md border border-slate-300 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Previous</a>
        @endif

        <span class="rounded-md bg-slate-950 px-4 py-2 font-semibold text-white">{{ $recentLogs->currentPage() }}</span>

        @if ($recentLogs->hasMorePages())
            <a href="{{ $recentLogs->nextPageUrl() }}" class="dashboard-log-page rounded-md border border-slate-300 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Next</a>
        @else
            <span class="rounded-md border border-slate-200 px-4 py-2 font-semibold text-slate-400">Next</span>
        @endif
    </div>
</div>
