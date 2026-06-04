<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $quotation->quotation_no }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900">
    <main class="mx-auto max-w-5xl bg-white p-8 shadow-sm print:shadow-none">
        <div class="flex items-start justify-between gap-6 border-b border-slate-200 pb-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-cyan-700">Flowcurement</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-950">Quotation</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $quotation->quotation_no }}</p>
            </div>
            <div class="text-right text-sm text-slate-600">
                <p>Date: <span class="font-semibold text-slate-950">{{ $quotation->quotation_date?->format('M d, Y') }}</span></p>
                <p class="mt-1">Valid Until: <span class="font-semibold text-slate-950">{{ $quotation->validity_date?->format('M d, Y') }}</span></p>
                <p class="mt-1">Reference: <span class="font-semibold text-slate-950">{{ $quotation->referenceSalesOrder?->sales_order_no ?? 'No Reference' }}</span></p>
            </div>
        </div>

        <div class="grid gap-6 py-6 sm:grid-cols-2">
            <div>
                <h2 class="text-xs font-semibold uppercase text-slate-500">Client</h2>
                <p class="mt-2 text-base font-semibold text-slate-950">{{ $quotation->businessPartner?->company_name }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $quotation->company_address }}</p>
                <p class="mt-2 text-sm text-slate-600">{{ $quotation->contact_person }} {{ $quotation->contact_no ? '| '.$quotation->contact_no : '' }}</p>
            </div>
            <div>
                <h2 class="text-xs font-semibold uppercase text-slate-500">Prepared By</h2>
                <p class="mt-2 text-base font-semibold text-slate-950">{{ $quotation->preparedBy?->name ?? 'System' }}</p>
                <p class="mt-1 text-sm text-slate-600">Agent: {{ $quotation->agent_name }}</p>
            </div>
        </div>

        <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
            <colgroup>
                <col class="w-[22%]">
                <col class="w-[28%]">
                <col class="w-[12%]">
                <col class="w-[12%]">
                <col class="w-[12%]">
                <col class="w-[8%]">
                <col class="w-[12%]">
            </colgroup>
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-3 text-left">Item</th>
                    <th class="px-3 py-3 text-left">Description</th>
                    <th class="px-3 py-3 text-left">Lead Time</th>
                    <th class="px-3 py-3 text-left">Unit</th>
                    <th class="px-3 py-3 text-right">Price</th>
                    <th class="px-3 py-3 text-center">Qty</th>
                    <th class="px-3 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($quotation->items as $row)
                    <tr>
                        <td class="px-3 py-3 font-semibold text-slate-950">{{ $row->item?->item_name }}</td>
                        <td class="px-3 py-3 text-slate-700">{{ $row->description }}</td>
                        <td class="px-3 py-3 text-slate-700">{{ $row->lead_time ?: '-' }}</td>
                        <td class="px-3 py-3 text-slate-700">{{ str($row->unitMeasure?->name)->headline() }}</td>
                        <td class="px-3 py-3 text-right">{{ number_format((float) $row->item_price, 2) }}</td>
                        <td class="px-3 py-3 text-center">{{ number_format((float) $row->quantity, 0) }}</td>
                        <td class="px-3 py-3 text-right font-semibold">{{ number_format((float) $row->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6 flex justify-end">
            <dl class="w-full max-w-xs space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Subtotal</dt>
                    <dd class="font-semibold text-slate-950">{{ number_format((float) $quotation->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-500">Tax {{ number_format((float) $quotation->tax_rate, 0) }}%</dt>
                    <dd class="font-semibold text-slate-950">{{ number_format((float) $quotation->tax_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-t border-slate-200 pt-3 text-base">
                    <dt class="font-semibold text-slate-700">Total</dt>
                    <dd class="font-bold text-slate-950">{{ number_format((float) $quotation->total_amount, 2) }}</dd>
                </div>
            </dl>
        </div>

        @if ($quotation->remarks)
            <div class="mt-8 whitespace-pre-line rounded-lg border border-slate-200 p-4 text-sm text-slate-700">
                {{ $quotation->remarks }}
            </div>
        @endif

        <div class="no-print mt-8 flex justify-end gap-2">
            <a href="{{ route('quotations.show', $quotation) }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
            <button type="button" onclick="window.print()" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Print</button>
        </div>
    </main>
</body>
</html>
