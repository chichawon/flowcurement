<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $salesInvoice->sales_invoice_no }}</title>
    <style>
        @page {
            margin: 34px 42px;
        }

        body {
            color: #020617;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.25;
        }

        h1 {
            border-bottom: 2px solid #0891b2;
            font-size: 24px;
            margin: 0 0 16px;
            padding-bottom: 10px;
            text-transform: uppercase;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .muted {
            color: #475569;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta-label {
            font-weight: 700;
            width: 116px;
        }

        .section-table {
            margin: 24px 0 20px;
        }

        .section-title {
            background: #0891b2;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 8px;
            text-align: center;
            text-transform: uppercase;
        }

        .section-body {
            padding: 8px 2px 0;
            vertical-align: top;
        }

        .items th {
            background: #0891b2;
            border: 1px solid #0e7490;
            color: #ffffff;
            font-size: 10px;
            padding: 8px 6px;
            text-align: center;
        }

        .items td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            vertical-align: middle;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .summary {
            margin-top: 14px;
        }

        .summary td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
        }

        .grand-total td {
            background: #020617;
            border-color: #020617;
            color: #ffffff;
            font-weight: 700;
            padding: 9px 6px;
        }

        .remarks {
            margin-top: 16px;
            white-space: pre-line;
        }

        .status {
            border-radius: 20px;
            color: #ffffff;
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 8px;
            text-transform: uppercase;
        }

        .status-unpaid {
            background: #0891b2;
        }

        .status-paid,
        .status-collected {
            background: #059669;
        }

        .status-pending {
            background: #d97706;
        }

        .status-void,
        .status-cancelled {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <h1>Sales Invoice</h1>

    <table>
        <tr>
            <td style="width: 56%; vertical-align: top;">
                <strong>{{ config('app.name', 'Flowcurement') }}</strong><br>
                <span class="muted">Procurement and ERP Management</span>
            </td>
            <td style="width: 44%; vertical-align: top;">
                <table class="meta">
                    <tr>
                        <td class="meta-label">Invoice Date</td>
                        <td>{{ $salesInvoice->invoice_date?->format('m/d/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Invoice No</td>
                        <td>{{ $salesInvoice->sales_invoice_no }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Due Date</td>
                        <td>{{ $salesInvoice->due_date?->format('m/d/Y') ?? 'Not set' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Status</td>
                        <td><span class="status status-{{ $salesInvoice->status }}">{{ str($salesInvoice->status)->headline() }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <td style="width: 45%;">
                <div class="section-title">Bill To</div>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%;">
                <div class="section-title">Reference Details</div>
            </td>
        </tr>
        <tr>
            <td class="section-body">
                <strong>{{ $salesInvoice->company_name }}</strong><br>
                {{ $salesInvoice->company_address }}<br>
                {{ $salesInvoice->contact_person }}<br>
                {{ $salesInvoice->contact_no }}
            </td>
            <td></td>
            <td class="section-body">
                <table class="meta">
                    <tr>
                        <td class="meta-label">Sales Order</td>
                        <td>{{ $salesInvoice->sales_order_no }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Delivery Receipt</td>
                        <td>{{ $salesInvoice->delivery_receipt_no }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Customer P.O.</td>
                        <td>{{ $salesInvoice->customer_po ?: 'None' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Terms</td>
                        <td>{{ $salesInvoice->terms }} day(s)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 21%;">Item</th>
                <th style="width: 20%;">Description</th>
                <th style="width: 9%;">WHT</th>
                <th style="width: 7%;">Qty</th>
                <th style="width: 8%;">UOM</th>
                <th style="width: 11%;">Unit Price</th>
                <th style="width: 10%;">Subtotal</th>
                <th style="width: 9%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salesInvoice->items as $row)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $row->item_name }}</strong>
                        @if ($row->item?->item_code)
                            <br><span class="muted">{{ $row->item->item_code }}</span>
                        @endif
                    </td>
                    <td>{{ $row->description ?: '-' }}</td>
                    <td class="right">{{ number_format((float) $row->withholding_tax_rate, 0) }}%<br>-{{ number_format((float) $row->withholding_tax_amount, 2) }}</td>
                    <td class="center">{{ number_format((float) $row->quantity, 0) }}</td>
                    <td class="center">{{ $row->unitMeasure?->name }}</td>
                    <td class="right">{{ number_format((float) $row->price, 2) }}</td>
                    <td class="right">{{ number_format((float) $row->subtotal, 2) }}</td>
                    <td class="right">{{ number_format((float) $row->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td style="width: 72%; font-weight: 700;">Subtotal</td>
            <td class="right" style="width: 28%; font-weight: 700;">{{ number_format((float) $salesInvoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700;">Tax Amount ({{ number_format((float) $salesInvoice->tax_rate, 0) }}%)</td>
            <td class="right" style="font-weight: 700;">{{ number_format((float) $salesInvoice->tax_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700;">WHT Amount</td>
            <td class="right" style="font-weight: 700;">-{{ number_format((float) $salesInvoice->withholding_tax_amount, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total Amount</td>
            <td class="right">{{ number_format((float) $salesInvoice->total_amount, 2) }}</td>
        </tr>
    </table>

    @if ($salesInvoice->remarks)
        <div class="remarks">
            {{ $salesInvoice->remarks }}
        </div>
    @endif
</body>
</html>
