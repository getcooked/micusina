<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 28px; }
    body { color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    h1 { font-size: 21px; margin: 0 0 3px; } h2 { font-size: 13px; margin: 22px 0 8px; }
    .muted { color: #64748b; } .summary { margin-top: 18px; width: 100%; } .summary td { border: 1px solid #e2e8f0; padding: 12px; width: 25%; }
    .summary span { color: #64748b; display: block; font-size: 9px; text-transform: uppercase; } .summary strong { display:block; font-size:15px; margin-top:5px; }
    table { border-collapse: collapse; width: 100%; } th { background:#f1f5f9; color:#475569; font-size:9px; text-align:left; text-transform:uppercase; } th, td { border:1px solid #e2e8f0; padding:7px; } .right { text-align:right; }
  </style>
</head>
<body>
  <h1>Mi Cusina Sales Report</h1>
  <div class="muted">{{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }} &middot; Generated {{ now()->format('M d, Y g:i A') }}</div>
  <table class="summary"><tr><td><span>Total sales</span><strong>&#8369;{{ number_format($totalSales, 2) }}</strong></td><td><span>Food orders</span><strong>&#8369;{{ number_format($orderSales, 2) }}</strong></td><td><span>Reservation deposits</span><strong>&#8369;{{ number_format($reservationSales, 2) }}</strong></td><td><span>Paid transactions</span><strong>{{ $paidOrders + $approvedReservations }}</strong></td></tr></table>
  <h2>Top-selling items</h2>
  <table><thead><tr><th>Item</th><th class="right">Quantity sold</th><th class="right">Sales</th></tr></thead><tbody>@forelse($topItems as $item)<tr><td>{{ $item->title }}</td><td class="right">{{ $item->quantity_sold }}</td><td class="right">&#8369;{{ number_format($item->sales_total, 2) }}</td></tr>@empty<tr><td colspan="3">No paid food orders in this period.</td></tr>@endforelse</tbody></table>
  <h2>Recent paid food orders</h2>
  <table><thead><tr><th>Date</th><th>Customer</th><th>Items</th><th>Payment</th><th class="right">Total</th></tr></thead><tbody>@forelse($recentSales as $sale)<tr><td>{{ $sale->created_at->format('M d, Y g:i A') }}</td><td>{{ $sale->name }}</td><td>{{ $sale->title }} x {{ $sale->quantity }}</td><td>{{ $sale->payment_method ?: '—' }}</td><td class="right">&#8369;{{ number_format($sale->price, 2) }}</td></tr>@empty<tr><td colspan="5">No paid food orders in this period.</td></tr>@endforelse</tbody></table>
</body>
</html>
