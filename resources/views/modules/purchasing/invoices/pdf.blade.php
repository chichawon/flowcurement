<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #020617; font-size: 12px; }
        h1 { font-size: 24px; margin: 0 0 12px; }
        .bar { height: 4px; background: #0891b2; margin-bottom: 18px; }
        .grid { width: 100%; margin-bottom: 20px; }
        .grid td { vertical-align: top; padding: 3px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.items th { background: #0891b2; color: #fff; padding: 8px; border: 1px solid #0e7490; }
        table.items td { padding: 8px; border: 1px solid #cbd5e1; }
        .right { text-align: right; }
        .total { background: #020617; color: #fff; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Purchase Invoice</h1>
    <div class="bar"></div>
    <table class="grid">
        <tr><td><strong>Supplier:</strong> {{ $purchaseInvoice->supplier_name }}</td><td class="right"><strong>Invoice No:</strong> {{ $purchaseInvoice->purchase_invoice_no }}</td></tr>
        <tr><td><strong>Supplier Invoice:</strong> {{ $purchaseInvoice->supplier_invoice_no }}</td><td class="right"><strong>Date:</strong> {{ $purchaseInvoice->invoice_date?->format('m/d/Y') }}</td></tr>
        <tr><td><strong>P.O No:</strong> {{ $purchaseInvoice->purchase_order_no ?: 'Direct' }}</td><td class="right"><strong>Due Date:</strong> {{ $purchaseInvoice->due_date?->format('m/d/Y') ?: 'Open' }}</td></tr>
    </table>
    <table class="items">
        <thead><tr><th>#</th><th>Item</th><th>Description</th><th>Unit</th><th class="right">Qty</th><th class="right">Price</th><th class="right">Total</th></tr></thead>
        <tbody>
            @foreach ($purchaseInvoice->items as $item)
                <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $item->item?->item_name }}</strong></td><td>{{ $item->description }}</td><td>{{ str($item->unitMeasure?->name)->headline() }}</td><td class="right">{{ number_format((float) $item->quantity, 0) }}</td><td class="right">{{ number_format((float) $item->price, 2) }}</td><td class="right">{{ number_format((float) $item->total, 2) }}</td></tr>
            @endforeach
            <tr><td colspan="6"><strong>Subtotal</strong></td><td class="right">{{ number_format((float) $purchaseInvoice->subtotal, 2) }}</td></tr>
            <tr><td colspan="6"><strong>Tax Amount</strong></td><td class="right">{{ number_format((float) $purchaseInvoice->tax_amount, 2) }}</td></tr>
            <tr class="total"><td colspan="6">Total Amount</td><td class="right">{{ number_format((float) $purchaseInvoice->total_amount, 2) }}</td></tr>
        </tbody>
    </table>
</body>
</html>
