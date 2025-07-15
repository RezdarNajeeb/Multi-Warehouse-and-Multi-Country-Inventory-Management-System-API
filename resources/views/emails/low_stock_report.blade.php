<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Stock Report</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-family: Arial, sans-serif;
        }

        th {
            background: #f5f5f5;
        }
    </style>
</head>
<body>
<h2>Products That Have Reached Minimum Quantity</h2>

<table>
    <thead>
    <tr>
        <th>Product</th>
        <th>SKU</th>
        <th>Qty</th>
        <th>Min Qty</th>
        <th>Warehouse</th>
        <th>Country</th>
        <th>Supplier Contact</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $inv)
        @php
            $supplier = optional($inv->product->suppliers->first());
            $contact  = $supplier?->contact_info ?? [];
        @endphp
        <tr>
            <td>{{ $inv->product->name }}</td>
            <td>{{ $inv->product->sku }}</td>
            <td>{{ $inv->quantity }}</td>
            <td>{{ $inv->min_quantity }}</td>
            <td>{{ $inv->warehouse->location }}</td>
            <td>{{ $inv->warehouse->country->name ?? '—' }}</td>
            <td>{{ ($contact['email'] ?? 'N/A') . ' | ' . ($contact['phone'] ?? 'N/A') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
