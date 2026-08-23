<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 22px; }
    body { color:#1f2937; font-family: DejaVu Sans, sans-serif; font-size:8px; } h1 { font-size:19px; margin:0 0 3px; } .muted { color:#64748b; }
    table { border-collapse:collapse; margin-top:18px; width:100%; } th { background:#f1f5f9; color:#475569; font-size:7px; text-align:left; text-transform:uppercase; } th,td { border:1px solid #e2e8f0; padding:6px; vertical-align:top; } .right { text-align:right; }
  </style>
</head>
<body>
  <h1>Mi Cusina Transaction History</h1>
  <div class="muted">{{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }} &middot; Generated {{ now()->format('M d, Y g:i A') }}</div>
  <table><thead><tr><th>Date</th><th>Type</th><th>Customer</th><th>Details</th><th>Method / Reference</th><th>Status</th><th class="right">Amount</th></tr></thead><tbody>@forelse($transactions as $transaction)<tr><td>{{ $transaction->date->format('M d, Y g:i A') }}</td><td>{{ $transaction->type }}</td><td>{{ $transaction->customer ?: '—' }}</td><td>{{ $transaction->description }}</td><td>{{ $transaction->method ?: '—' }}@if($transaction->reference)<br>{{ $transaction->reference }}@endif</td><td>{{ $transaction->status }}</td><td class="right">&#8369;{{ number_format($transaction->amount, 2) }}</td></tr>@empty<tr><td colspan="7">No transactions found for this period.</td></tr>@endforelse</tbody></table>
</body>
</html>
