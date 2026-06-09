@props(['totals'])

<div class="mt-4 overflow-hidden border border-slate-400 bg-white">
    <table class="w-full table-fixed border-collapse text-sm">
        <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
            <tr>
                <th class="border border-slate-400 px-3 py-2 text-left">Summary</th>
                <th class="border border-slate-400 px-3 py-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Subtotal</td>
                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['subtotal'] ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax Amount</td>
                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['tax_amount'] ?? 0), 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bg-slate-950 text-white">
                <td class="border border-slate-950 px-3 py-3 text-sm font-bold uppercase">Total Amount</td>
                <td class="border border-slate-950 px-3 py-3 text-right text-base font-bold">{{ number_format((float) ($totals['total_amount'] ?? $totals['balance_amount'] ?? 0), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
