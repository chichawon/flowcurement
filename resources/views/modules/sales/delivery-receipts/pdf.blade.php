<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $deliveryReceipt->delivery_receipt_no }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            color: #020617;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.35;
            margin: 0;
        }

        .hero {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            height: 150px;
            padding: 42px 64px 0;
            position: relative;
        }

        .stripe-left,
        .stripe-right {
            background: #0891b2;
            height: 28px;
            position: absolute;
            top: 52px;
        }

        .stripe-left {
            left: 0;
            width: 42px;
        }

        .stripe-right {
            right: 0;
            width: 260px;
        }

        .logo {
            border: 2px solid #020617;
            border-radius: 4px;
            display: inline-block;
            font-size: 13px;
            font-weight: 700;
            height: 54px;
            letter-spacing: 2px;
            line-height: 18px;
            padding-top: 10px;
            text-align: center;
            vertical-align: top;
            width: 64px;
        }

        .company {
            display: inline-block;
            margin-left: 24px;
            vertical-align: top;
        }

        .company strong {
            display: block;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .content {
            padding: 58px 72px 48px;
        }

        h1 {
            font-size: 26px;
            margin: 0 0 44px;
            text-align: center;
        }

        h2 {
            color: #1e293b;
            font-size: 15px;
            margin: 44px 0 18px;
            text-transform: uppercase;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .details {
            font-size: 14px;
            margin-bottom: 40px;
        }

        .details td {
            padding: 3px 0;
            vertical-align: top;
        }

        .label {
            font-weight: 700;
        }

        .muted {
            color: #475569;
        }

        .items th {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 12px;
            padding: 9px 8px;
            text-align: left;
        }

        .items td {
            border: 1px solid #cbd5e1;
            padding: 9px 8px;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .signature-table {
            margin-top: 64px;
        }

        .signature-table td {
            padding-top: 32px;
            text-align: center;
            width: 50%;
        }

        .signature-line {
            border-top: 1px solid #94a3b8;
            display: inline-block;
            padding-top: 8px;
            width: 220px;
        }

        .footer-line {
            border-top: 2px solid #e2e8f0;
            margin-top: 56px;
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="stripe-left"></div>
        <div class="stripe-right"></div>
        <div class="logo">YOUR<br>LOGO</div>
        <div class="company">
            <strong>{{ config('app.name', 'Flowcurement') }}</strong>
            Enterprise Resource Planning<br>
            {{ config('app.url') }}
        </div>
    </div>

    <div class="content">
        <h1>Delivery Receipt</h1>

        <table class="details">
            <tr>
                <td style="width: 55%;">
                    <div><span class="label">RECEIPT NO.:</span> {{ $deliveryReceipt->delivery_receipt_no }}</div>
                    <div><span class="label">DATE:</span> {{ $deliveryReceipt->dr_date?->format('F d, Y') }}</div>
                    <div><span class="label">SALES ORDER NO.:</span> {{ $deliveryReceipt->sales_order_no }}</div>
                    <div><span class="label">CUSTOMER P.O.:</span> {{ $deliveryReceipt->customer_po ?: 'None' }}</div>
                </td>
                <td style="width: 45%;">
                    <div><span class="label">STATUS:</span> {{ str($deliveryReceipt->status)->headline() }}</div>
                    <div><span class="label">REMARKS:</span> {{ str($deliveryReceipt->remarks ?? 'on_hold')->headline() }}</div>
                    <div><span class="label">RECEIVED DATE:</span> {{ $deliveryReceipt->received_date?->format('F d, Y') ?? 'Not uploaded' }}</div>
                </td>
            </tr>
        </table>

        <h2>Delivery Information</h2>
        <table class="details">
            <tr>
                <td style="width: 52%;">
                    <div><span class="label">Recipient Name:</span> {{ $deliveryReceipt->company_name }}</div>
                    <div><span class="label">Recipient Address:</span> {{ $deliveryReceipt->company_address ?: 'Not provided' }}</div>
                    <div><span class="label">Contact Person:</span> {{ $deliveryReceipt->contact_person ?: 'Not provided' }}</div>
                    <div><span class="label">Contact Information:</span> {{ $deliveryReceipt->contact_no ?: 'Not provided' }}</div>
                </td>
                <td style="width: 48%;">
                    <div><span class="label">Agent:</span> {{ $deliveryReceipt->agent_name }}</div>
                    <div><span class="label">Delivered By:</span> {{ $deliveryReceipt->delivered_by ?: 'Not uploaded' }}</div>
                    <div><span class="label">Received By:</span> {{ $deliveryReceipt->received_by ?: 'Not uploaded' }}</div>
                </td>
            </tr>
        </table>

        <h2>Items Delivered</h2>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 38%;">Item Description</th>
                    <th style="width: 13%;" class="center">Quantity</th>
                    <th style="width: 15%;">Unit</th>
                    <th style="width: 34%;">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deliveryReceipt->items as $row)
                    <tr>
                        <td>
                            <strong>{{ $row->item_name }}</strong>
                        </td>
                        <td class="center">{{ number_format((float) $row->delivered_quantity, 0) }}</td>
                        <td>{{ str($row->unitMeasure?->name)->headline() }}</td>
                        <td>{{ $row->salesOrderItem?->description ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="signature-table">
            <tr>
                <td><span class="signature-line">Delivered By</span></td>
                <td><span class="signature-line">Received By</span></td>
            </tr>
        </table>

        <div class="footer-line"></div>
    </div>
</body>
</html>
