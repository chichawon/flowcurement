<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-base font-semibold text-slate-950">Reports Directory</h3>
            <p class="mt-1 text-sm text-slate-500">Select a report to view detailed table records.</p>
        </div>

        <div class="erp-panel-body">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] table-fixed border border-slate-300 text-sm">
                    <colgroup>
                        <col class="w-24">
                        <col class="w-[28%]">
                        <col>
                        <col class="w-40">
                    </colgroup>
                    <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="border border-slate-300 px-3 py-3 text-center">No</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Report Name</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Description</th>
                            <th class="border border-slate-300 px-3 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-slate-300 px-3 py-3 text-center font-semibold">1</td>
                            <td class="border border-slate-300 px-3 py-3 font-semibold text-slate-950">Top 10 Business Partners</td>
                            <td class="border border-slate-300 px-3 py-3 text-slate-700">Ranks clients by sales order amount and order count within a selected date range.</td>
                            <td class="border border-slate-300 px-3 py-3 text-center">
                                <a href="{{ route('reports.top-business-partners') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">
                                    Open
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-3 text-center font-semibold">2</td>
                            <td class="border border-slate-300 px-3 py-3 font-semibold text-slate-950">Company Top Ordered Items</td>
                            <td class="border border-slate-300 px-3 py-3 text-slate-700">Shows each company with the items they order most, including quantity, amount, and latest order date.</td>
                            <td class="border border-slate-300 px-3 py-3 text-center">
                                <a href="{{ route('reports.company-top-ordered-items') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">
                                    Open
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
