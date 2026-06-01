<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->quotation_no }}</title>
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
            width: 120px;
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

        .items .right {
            text-align: right;
        }

        .items .center {
            text-align: center;
        }

        .item-image-cell {
            width: 58px;
        }

        .item-image {
            height: 44px;
            object-fit: contain;
            width: 44px;
        }

        .item-placeholder {
            background: #f1f5f9;
            color: #64748b;
            display: block;
            font-size: 16px;
            font-weight: 700;
            height: 44px;
            line-height: 44px;
            text-align: center;
            width: 44px;
        }

        .grand-total td {
            background: #0891b2;
            border-color: #0891b2;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 6px;
        }

        .remarks {
            border: 1px solid #cbd5e1;
            margin-top: 16px;
            padding: 9px 10px;
        }
    </style>
</head>
<body>
    <h1>Sales Quotation</h1>

    <table>
        <tr>
            <td style="width: 56%; vertical-align: top;">
                <strong>{{ config('app.name', 'Flowcurement') }}</strong><br>
                <span class="muted">Procurement and ERP Management</span>
            </td>
            <td style="width: 44%; vertical-align: top;">
                <table class="meta">
                    <tr>
                        <td class="meta-label">Date</td>
                        <td>{{ $quotation->quotation_date?->format('m/d/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Quotation No</td>
                        <td>{{ $quotation->quotation_no }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Customer ID</td>
                        <td>{{ $quotation->businessPartner?->company_code ?? $quotation->businessPartner?->id }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Quotation Valid Until</td>
                        <td>{{ $quotation->validity_date?->format('m/d/Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <td style="width: 45%;">
                <div class="section-title">Prepared By</div>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%;">
                <div class="section-title">Quotation For</div>
            </td>
        </tr>
        <tr>
            <td class="section-body">
                {{ $quotation->preparedBy?->name ?? 'System' }}<br>
                <span class="muted">Agent: {{ $quotation->agent_name }}</span>
            </td>
            <td></td>
            <td class="section-body">
                <strong>{{ $quotation->businessPartner?->company_name }}</strong><br>
                {{ $quotation->company_address }}<br>
                {{ $quotation->contact_person }}<br>
                {{ $quotation->contact_no }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 43%;">Item Details</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">UOM</th>
                <th style="width: 16%;">Unit Price</th>
                <th style="width: 16%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $row)
                @php
                    $imagePath = $row->item?->item_image ? public_path('storage/'.$row->item->item_image) : null;
                    $hasImage = $imagePath && file_exists($imagePath);
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>
                        <table>
                            <tr>
                                <td class="item-image-cell">
                                    @if ($hasImage)
                                        <img class="item-image" src="{{ $imagePath }}" alt="">
                                    @else
                                        <span class="item-placeholder">{{ strtoupper(substr($row->item?->item_name ?? 'I', 0, 1)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $row->item?->item_name }}</strong>
                                    @if ($row->description)
                                        <br>{{ $row->description }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="center">{{ number_format((float) $row->quantity, 0) }}</td>
                    <td class="center">{{ $row->unitMeasure?->name }}</td>
                    <td class="right">{{ number_format((float) $row->item_price, 2) }}</td>
                    <td class="right">{{ number_format((float) $row->total, 2) }}</td>
                </tr>
            @endforeach

            <tr class="grand-total">
                <td colspan="5">Grand Total</td>
                <td class="right">{{ number_format((float) $quotation->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if ($quotation->remarks)
        <div class="remarks">
            <strong>Remarks:</strong> {{ $quotation->remarks }}
        </div>
    @endif
</body>
</html>
