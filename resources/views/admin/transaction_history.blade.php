<!DOCTYPE html>
<html>
<head>
  @include('admin.css')
  <style>
    .history { background:var(--mc-shell); border:1px solid var(--mc-border); border-radius:8px; min-height:calc(100vh - 120px); padding:26px; }
    .history h2 { font-size:25px; font-weight:800; margin:0; } .history p { color:var(--mc-muted); margin:5px 0 0; }
    .history-filter { align-items:end; background:var(--mc-panel); border:1px solid var(--mc-border); border-radius:8px; display:flex; flex-wrap:wrap; gap:14px; margin:22px 0; padding:16px; }
    .history-filter label { color:var(--mc-muted); font-size:12px; font-weight:700; text-transform:uppercase; }
    .history-filter input { background:#101116; border:1px solid var(--mc-border-strong); border-radius:5px; color:#fff; display:block; margin-top:6px; padding:8px 10px; }
    .history-filter button { background:var(--mc-accent); border:0; border-radius:5px; color:#08090c; font-weight:800; padding:9px 18px; }
    .history-panel { background:var(--mc-panel); border:1px solid var(--mc-border); border-radius:8px; overflow-x:auto; }
    .history-table { border-collapse:collapse; min-width:850px; width:100%; } .history-table th { background:#f8fafc; border-bottom:1px solid #e2e8f0; color:#475569; font-size:11px; padding:13px 14px; text-align:left; text-transform:uppercase; }
    .history-table td { border-top:1px solid var(--mc-border); padding:14px; vertical-align:top; } .history-table td:last-child { text-align:right; white-space:nowrap; }
    .status { border-radius:999px; display:inline-block; font-size:11px; font-weight:800; padding:5px 9px; } .status-paid,.status-approved { background:rgba(16,185,129,.16); color:#6ee7b7; } .status-canceled { background:rgba(239,68,68,.16); color:#fca5a5; } .status-pending,.status-unpaid { background:rgba(251,191,36,.14); color:#fcd34d; } .muted { color:var(--mc-muted); font-size:12px; }
  </style>
</head>
<body>
  @include('admin.header') @include('admin.sidebar')
  <div class="page-content"><div class="page-header"><div class="container-fluid"><main class="history">
    <h2>Transaction History</h2><p>All food-order and table-reservation transactions for the selected period.</p>
    <form class="history-filter" method="GET" action="{{ route('admin.transaction-history') }}"><label>From <input type="date" name="from" value="{{ $from->toDateString() }}"></label><label>To <input type="date" name="to" value="{{ $to->toDateString() }}"></label><button type="submit">Apply range</button></form>
    <div class="history-panel"><table class="history-table"><thead><tr><th>Date</th><th>Type</th><th>Customer</th><th>Details</th><th>Method / reference</th><th>Status</th><th>Amount</th></tr></thead><tbody>@forelse($transactions as $transaction)<tr><td>{{ $transaction->date->format('M d, Y') }}<br><span class="muted">{{ $transaction->date->format('g:i A') }}</span></td><td>{{ $transaction->type }}</td><td>{{ $transaction->customer ?: '—' }}</td><td>{{ $transaction->description }}</td><td>{{ $transaction->method ?: '—' }} @if($transaction->reference)<br><span class="muted">{{ $transaction->reference }}</span>@endif</td><td><span class="status status-{{ strtolower(str_replace(' ', '-', $transaction->status)) }}">{{ $transaction->status }}</span></td><td>&#8369;{{ number_format($transaction->amount, 2) }}</td></tr>@empty<tr><td colspan="7">No transactions found for this period.</td></tr>@endforelse</tbody></table></div>
  </main></div></div></div>
  @include('admin.js')
</body>
</html>
