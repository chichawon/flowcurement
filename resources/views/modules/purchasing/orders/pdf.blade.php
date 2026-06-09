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
    <h1>Purchase Order</h1>
    <div class="bar"></div>
    <table class="grid">
        <tr><td><strong>Supplier:</strong> {{ $purchaseOrder->supplier_name }}</td><td class="right"><strong>P.O No:</strong> {{ $purchaseOrder->purchase_order_no }}</td></tr>
        <tr><td><strong>Address:</strong> {{ $purchaseOrder->supplier_address }}</td><td class="right"><strong>Date:</strong> {{ $purchaseOrder->purchase_order_date?->format('m/d/Y') }}</td></tr>
        <tr><td><strong>Contact:</strong> {{ $purchaseOrder->contact_person }} {{ $purchaseOrder->contact_no }}</td><td class="right"><strong>Expected:</strong> {{ $purchaseOrder->expected_delivery_date?->format('m/d/Y') ?: 'Open' }}</td></tr>
    </table>
    <table class="items">
        <thead><tr><th>#</th><th>Item</th><th>Description</th><th>Unit</th><th class="right">Qty</th><th class="right">Price</th><th class="right">Total</th></tr></thead>
        <tbody>
            @foreach ($purchaseOrder->items as $item)
                <tr><td>{{ $loop->iteration }}</td><td><strong>{{ $item->item?->item_name }}</strong></td><td>{{ $item->description }}</td><td>{{ str($item->unitMeasure?->name)->headline() }}</td><td class="right">{{ number_format((float) $item->quantity, 0) }}</td><td class="right">{{ number_format((float) $item->price, 2) }}</td><td class="right">{{ number_format((float) $item->total, 2) }}</td></tr>
            @endforeach
            <tr><td colspan="6"><strong>Subtotal</strong></td><td class="right">{{ number_format((float) $purchaseOrder->subtotal, 2) }}</td></tr>
            <tr><td colspan="6"><strong>Tax Amount</strong></td><td class="right">{{ number_format((float) $purchaseOrder->tax_amount, 2) }}</td></tr>
            <tr class="total"><td colspan="6">Total Amount</td><td class="right">{{ number_format((float) $purchaseOrder->total_amount, 2) }}</td></tr>
        </tbody>
    </table>
</body>
</html>
