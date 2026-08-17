<!DOCTYPE html>
<html>
<head>
  @include('admin.css')
  <style>
    .sales-report { background:var(--mc-shell); border:1px solid var(--mc-border); border-radius:8px; margin:0; min-height:calc(100vh - 120px); padding:26px; }
    .sales-report h2 { font-size:25px; font-weight:800; margin:0; }
    .sales-report-heading { align-items:center; display:flex; gap:12px; }
    .sales-report-logo { height:46px; object-fit:contain; width:46px; }
    .sales-report .subtitle { color:var(--mc-muted); margin:5px 0 0; }
    .sales-filter, .sales-card, .sales-panel { background:var(--mc-panel); border:1px solid var(--mc-border); border-radius:8px; }
    .sales-filter { align-items:end; display:flex; flex-wrap:wrap; gap:14px; margin:22px 0; padding:16px; }
    .sales-filter label { color:var(--mc-muted); font-size:12px; font-weight:700; margin:0; text-transform:uppercase; }
    .sales-filter input { background:#101116; border:1px solid var(--mc-border-strong); border-radius:5px; color:#fff; display:block; margin-top:6px; padding:8px 10px; }
    .sales-filter button { background:var(--mc-accent); border:0; border-radius:5px; color:#08090c; font-weight:800; padding:9px 18px; }
    .sales-cards { display:grid; gap:16px; grid-template-columns:repeat(4, minmax(0, 1fr)); margin-bottom:18px; }
    .sales-card { padding:18px; } .sales-card span { color:var(--mc-muted); display:block; font-size:12px; font-weight:700; text-transform:uppercase; } .sales-card strong { display:block; font-size:25px; margin-top:9px; }
    .sales-grid { display:grid; gap:18px; grid-template-columns:1.2fr .8fr; } .sales-panel { overflow:hidden; padding:20px; } .sales-panel h3 { font-size:17px; font-weight:800; margin:0 0 16px; }
    .sales-table { border-collapse:collapse; width:100%; } .sales-table th { color:var(--mc-muted); font-size:11px; padding:10px 8px; text-align:left; text-transform:uppercase; } .sales-table td { border-top:1px solid var(--mc-border); padding:12px 8px; } .sales-table td:last-child, .sales-table th:last-child { text-align:right; }
    .sales-bars { display:grid; gap:12px; } .sales-bar-label { display:flex; font-size:12px; justify-content:space-between; margin-bottom:5px; } .sales-bar-track { background:#101116; border-radius:999px; height:8px; overflow:hidden; } .sales-bar-fill { background:var(--mc-accent); border-radius:999px; height:100%; }
    .sales-note { color:var(--mc-muted); font-size:12px; margin:18px 0 0; } @media (max-width:900px) { .sales-cards,.sales-grid { grid-template-columns:1fr 1fr; } } @media (max-width:560px) { .sales-cards,.sales-grid { grid-template-columns:1fr; } .sales-report { padding:16px; } }
  </style>
</head>
<body>
  @include('admin.header')
  @include('admin.sidebar')
  <div class="page-content"><div class="page-header"><div class="container-fluid"><main class="sales-report">
    <div class="sales-report-heading">
      <img class="sales-report-logo" src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina logo">
      <div><h2>Sales Report</h2><p class="subtitle">Paid sales from {{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }}</p></div>
    </div>
    <form class="sales-filter" method="GET" action="{{ route('admin.sales-report') }}">
      <label>From <input type="date" name="from" value="{{ $from->toDateString() }}"></label>
      <label>To <input type="date" name="to" value="{{ $to->toDateString() }}"></label>
      <button type="submit">Apply range</button>
    </form>
    <section class="sales-cards">
      <div class="sales-card"><span>Total sales</span><strong>₱{{ number_format($totalSales, 2) }}</strong></div>
      <div class="sales-card"><span>Food orders</span><strong>₱{{ number_format($orderSales, 2) }}</strong></div>
      <div class="sales-card"><span>Reservation deposits</span><strong>₱{{ number_format($reservationSales, 2) }}</strong></div>
      <div class="sales-card"><span>Paid transactions</span><strong>{{ $paidOrders + $approvedReservations }}</strong></div>
    </section>
    <section class="sales-grid">
      <div class="sales-panel"><h3>Top-selling items</h3><table class="sales-table"><thead><tr><th>Item</th><th>Qty sold</th><th>Sales</th></tr></thead><tbody>@forelse($topItems as $item)<tr><td>{{ $item->title }}</td><td>{{ $item->quantity_sold }}</td><td>₱{{ number_format($item->sales_total, 2) }}</td></tr>@empty<tr><td colspan="3">No paid food orders in this period.</td></tr>@endforelse</tbody></table></div>
      <div class="sales-panel"><h3>Daily sales</h3><div class="sales-bars">@php($maxSales = max(collect($salesByDay)->max('total'), 1)) @foreach($salesByDay as $day)<div><div class="sales-bar-label"><span>{{ $day['label'] }}</span><span>₱{{ number_format($day['total'], 2) }}</span></div><div class="sales-bar-track"><div class="sales-bar-fill" style="width: {{ ($day['total'] / $maxSales) * 100 }}%"></div></div></div>@endforeach</div></div>
    </section>
    <section class="sales-panel" style="margin-top:18px"><h3>Recent paid food orders</h3><table class="sales-table"><thead><tr><th>Date</th><th>Customer</th><th>Items</th><th>Payment</th><th>Total</th></tr></thead><tbody>@forelse($recentSales as $sale)<tr><td>{{ $sale->created_at->format('M d, Y g:i A') }}</td><td>{{ $sale->name }}</td><td>{{ $sale->title }} × {{ $sale->quantity }}</td><td>{{ $sale->payment_method ?? '—' }}</td><td>₱{{ number_format($sale->price, 2) }}</td></tr>@empty<tr><td colspan="5">No paid food orders in this period.</td></tr>@endforelse</tbody></table></section>
    <p class="sales-note">Food sales include orders marked Paid and exclude canceled orders. Reservation sales include approved deposits.</p>
  </main></div></div></div>
  @include('admin.js')
</body>
</html>
