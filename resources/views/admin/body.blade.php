<style>
  body,
  .page-content,
  .page-header {
    background: #050505;
  }

  .admin-dashboard {
    --mi-accent: #38bdf8;
    --mi-accent-soft: rgba(56, 189, 248, 0.16);
    background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.08), transparent 28%), #0c0d10;
    border: 1px solid #2b2f38;
    border-radius: 8px;
    color: #ffffff;
    margin: 0;
    min-height: calc(100vh - 120px);
    padding: 28px;
    box-shadow: 0 18px 34px rgba(0, 0, 0, 0.28);
  }

  .admin-dashboard h2 {
    color: #ffffff;
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 18px;
    text-shadow: none;
  }

  .sales-card,
  .mini-card,
  .chart-panel {
    border-radius: 8px;
    box-shadow: 0 12px 26px rgba(0, 0, 0, 0.34);
  }

  .sales-card {
    background: #15171c;
    border: 1px solid #2b2f38;
    color: #fff;
    min-height: 108px;
    overflow: hidden;
    padding: 14px;
    position: relative;
    text-align: left;
  }

  .sales-card::after {
    content: none;
  }

  .sales-card .icon {
    align-items: center;
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    border-radius: 8px;
    color: #ffffff;
    display: inline-flex;
    font-size: 17px;
    height: 32px;
    justify-content: center;
    margin-bottom: 10px;
    position: relative;
    width: 32px;
    z-index: 1;
  }

  .dashboard-gap:first-of-type .col-xl-2:nth-child(1) .sales-card .icon {
    background: linear-gradient(135deg, #0284c7, #38bdf8);
  }

  .dashboard-gap:first-of-type .col-xl-2:nth-child(2) .sales-card .icon {
    background: linear-gradient(135deg, #4f46e5, #818cf8);
  }

  .dashboard-gap:first-of-type .col-xl-2:nth-child(3) .sales-card .icon {
    background: linear-gradient(135deg, #2563eb, #60a5fa);
  }

  .dashboard-gap:first-of-type .col-xl-2:nth-child(4) .sales-card .icon {
    background: linear-gradient(135deg, #0f766e, #2dd4bf);
  }

  .dashboard-gap:first-of-type .col-xl-2:nth-child(5) .sales-card .icon {
    background: linear-gradient(135deg, #15803d, #4ade80);
  }

  .sales-card .label {
    color: #a6abb6;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 4px;
    position: relative;
    z-index: 1;
  }

  .sales-card .value {
    font-size: 22px;
    font-weight: 800;
    position: relative;
    z-index: 1;
  }

  .sales-card.bg-teal,
  .sales-card.bg-orange,
  .sales-card.bg-blue,
  .sales-card.bg-cyan,
  .sales-card.bg-green {
    background: #15171c;
  }

  .bg-teal,
  .bg-orange,
  .bg-blue,
  .bg-cyan,
  .bg-green {
    background: #2d3037;
  }

  .mini-card {
    align-items: center;
    background: #15171c;
    border: 1px solid #2b2f38;
    display: flex;
    gap: 12px;
    min-height: 70px;
    padding: 13px 14px;
  }

  .mini-card:hover,
  .sales-card:hover {
    border-color: rgba(56, 189, 248, 0.38);
  }

  .mini-icon {
    align-items: center;
    border-radius: 999px;
    color: #fff;
    display: flex;
    font-size: 17px;
    height: 38px;
    justify-content: center;
    width: 38px;
  }

  .mini-icon.bg-cyan { background: linear-gradient(135deg, #0891b2, #22d3ee); }
  .mini-icon.bg-orange { background: linear-gradient(135deg, #F88379, #FFA69E); }
  .mini-icon.bg-teal { background: linear-gradient(135deg, #0f766e, #2dd4bf); }
  .mini-icon.bg-blue { background: linear-gradient(135deg, #2563eb, #60a5fa); }
  .mini-icon.bg-green { background: linear-gradient(135deg, #15803d, #4ade80); }

  .mini-label {
    color: #a6abb6;
    font-size: 14px;
  }

  .mini-value {
    color: #fff;
    font-size: 21px;
    font-weight: 800;
  }

  .chart-panel {
    background: #15171c;
    border: 1px solid #2b2f38;
    display: flex;
    flex-direction: column;
    margin-top: 24px;
    padding: 24px 22px;
  }

  .analytics-grid {
    display: grid;
    gap: 18px;
    grid-template-columns: repeat(12, 1fr);
    margin-top: 24px;
  }

  .analytics-grid .chart-panel {
    margin-top: 0;
    min-height: 330px;
  }

  .chart-wide {
    grid-column: span 7;
  }

  .chart-medium {
    grid-column: span 5;
  }

  .chart-half {
    grid-column: span 6;
  }

  .chart-third {
    grid-column: span 4;
  }

  .chart-full {
    grid-column: span 12;
  }

  .best-selling-panel {
    background: #15171c;
    color: #fff;
  }

  .chart-panel.best-selling-panel h3 {
    color: #fff;
  }

  .chart-panel h3 {
    color: #fff;
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 18px;
  }

  .rider-list {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }

  .rider-row {
    align-items: center;
    background: #101116;
    border: 1px solid #2b2f38;
    border-radius: 8px;
    display: flex;
    gap: 12px;
    justify-content: space-between;
    padding: 12px;
  }

  .analytics-grid {
    gap: 8px;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    margin-top: 8px;
  }

  .analytics-grid .chart-panel {
    grid-column: span 3;
    min-height: 225px;
  }

  .chart-panel h3 {
    font-size: 14px;
    margin-bottom: 7px;
  }

  .chart-tabs {
    margin-bottom: 5px;
  }

  .chart-tabs span {
    font-size: 10px;
    padding: 4px 7px;
  }

  .chart-box,
  .chart-box-tall {
    height: 160px;
    min-height: 160px;
  }

  .best-seller-list {
    gap: 8px;
  }

  .best-seller-row {
    color: #fff;
    font-size: 11px;
  }

  .best-seller-track {
    background: #292934;
    height: 6px;
  }

  .best-seller-bar {
    background: #F88379;
  }

  .rider-row strong {
    color: #fff;
    display: block;
  }

  .rider-row small {
    color: #a6abb6;
  }

  .rider-status {
    border-radius: 999px;
    color: #fff;
    display: inline-flex;
    font-size: 12px;
    font-weight: 800;
    margin-top: 6px;
    padding: 4px 9px;
  }

  .rider-available { background: #15803d; }
  .rider-unavailable { background: #b91c1c; }

  .chart-tabs {
    border-bottom: 1px solid #2b2f38;
    display: flex;
    gap: 32px;
    margin-bottom: 20px;
  }

  .chart-tabs span {
    color: #a6abb6;
    display: inline-block;
    font-size: 15px;
    padding: 0 0 12px;
  }

  .chart-tabs .active {
    border-bottom: 2px solid var(--mi-accent);
    color: var(--mi-accent);
  }

  .chart-box {
    height: 230px;
    min-height: 230px;
  }

  .chart-box-tall {
    height: 270px;
    min-height: 270px;
  }

  .best-seller-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .best-seller-row {
    color: #fff;
    padding: 0;
  }

  .best-seller-meta {
    align-items: center;
    display: flex;
    font-size: 16px;
    font-weight: 700;
    justify-content: space-between;
    margin-bottom: 8px;
  }

  .best-seller-title {
    display: block;
    line-height: 1.35;
  }

  .best-seller-sold {
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    white-space: nowrap;
  }

  .best-seller-track {
    background: #2b2f38;
    border-radius: 999px;
    height: 12px;
    overflow: hidden;
    width: 100%;
  }

  .best-seller-bar {
    background: #ffffff;
    border-radius: 999px;
    height: 100%;
    min-width: 8px;
  }

  .dashboard-gap {
    margin-bottom: 20px;
  }

  .admin-dashboard > .row.dashboard-gap {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    margin-left: 0;
    margin-right: 0;
  }

  .admin-dashboard > .row.dashboard-gap:nth-of-type(2) {
    grid-template-columns: repeat(5, minmax(0, 1fr));
  }

  .admin-dashboard > .row.dashboard-gap:nth-of-type(n+3) {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }

  .sales-card-col {
    max-width: none;
    padding-left: 0;
    padding-right: 0;
  }

  .mini-card-col {
    max-width: none;
    padding-left: 0;
    padding-right: 0;
  }

  @media (max-width: 767px) {
    .admin-dashboard > .row.dashboard-gap,
    .admin-dashboard > .row.dashboard-gap:nth-of-type(2),
    .admin-dashboard > .row.dashboard-gap:nth-of-type(n+3) {
      grid-template-columns: 1fr;
    }

    .analytics-grid {
      grid-template-columns: 1fr;
    }

    .chart-wide,
    .chart-medium,
    .chart-half,
    .chart-third,
    .chart-full {
      grid-column: span 1;
    }
  }

  @media (min-width: 768px) and (max-width: 1199px) {
    .admin-dashboard > .row.dashboard-gap,
    .admin-dashboard > .row.dashboard-gap:nth-of-type(2),
    .admin-dashboard > .row.dashboard-gap:nth-of-type(n+3) {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .chart-wide,
    .chart-medium,
    .chart-half,
    .chart-third,
    .chart-full {
      grid-column: span 12;
    }
  }
  /* Color-coded dashboard symbols on the white theme. */
  html body .sales-card .icon,
  html body .mini-card .mini-icon {
    border-radius: 10px;
  }
  html body .sales-card.bg-teal .icon, html body .mini-icon.bg-teal { background: #ccfbf1 !important; color: #0f766e !important; }
  html body .sales-card.bg-orange .icon, html body .mini-icon.bg-orange { background: #ffedd5 !important; color: #ea580c !important; }
  html body .sales-card.bg-blue .icon, html body .mini-icon.bg-blue { background: #dbeafe !important; color: #2563eb !important; }
  html body .sales-card.bg-cyan .icon, html body .mini-icon.bg-cyan { background: #cffafe !important; color: #0891b2 !important; }
  html body .sales-card.bg-green .icon, html body .mini-icon.bg-green { background: #dcfce7 !important; color: #15803d !important; }
  html body .sales-card .icon i, html body .mini-card .mini-icon i { color: inherit !important; }
</style>

<style>
  body,
  .page-content,
  .page-header {
    background: #050507;
  }

  .admin-dashboard {
    background: transparent;
    border: 0;
    border-radius: 0;
    box-shadow: none;
    color: #fff;
    min-height: auto;
    padding: 0;
  }

  .admin-dashboard h2 {
    color: #fff;
    font-size: 20px;
    margin: 0 0 10px;
  }

  .admin-dashboard > .row.dashboard-gap:first-of-type {
    background: #0b0b10;
    border: 1px solid #252530;
    border-radius: 16px;
    gap: 10px;
    padding: 10px;
  }

  .sales-card,
  .mini-card,
  .chart-panel,
  .best-selling-panel {
    background: #101016;
    border: 1px solid #252530;
    box-shadow: none;
    color: #fff;
  }

  .sales-card {
    background: #14141c !important;
    border: 1px solid #252530;
    border-radius: 13px;
    min-height: 112px;
    padding: 12px;
  }

  .sales-card .icon {
    border-radius: 9px;
    box-shadow: 0 8px 16px rgba(99, 102, 241, .18);
    height: 32px;
    margin-bottom: 8px;
    width: 32px;
  }

  .dashboard-gap:first-of-type .sales-card-col:nth-child(1) .icon {
    background: linear-gradient(135deg, #F88379, #FFA69E);
  }

  .dashboard-gap:first-of-type .sales-card-col:nth-child(2) .icon {
    background: linear-gradient(135deg, #7657e8, #a991ff);
  }

  .dashboard-gap:first-of-type .sales-card-col:nth-child(3) .icon {
    background: linear-gradient(135deg, #5468ee, #8da2ff);
  }

  .dashboard-gap:first-of-type .sales-card-col:nth-child(4) .icon {
    background: linear-gradient(135deg, #1999bd, #6dd2e7);
  }

  .dashboard-gap:first-of-type .sales-card-col:nth-child(5) .icon {
    background: linear-gradient(135deg, #8abe1f, #c5e46e);
  }

  .sales-card .label,
  .mini-label {
    color: #a4a4b2;
    font-size: 11px;
  }

  .sales-card .value {
    color: #fff;
    font-size: 18px;
    margin-top: 2px;
  }

  .admin-dashboard > .row.dashboard-gap:nth-of-type(2),
  .admin-dashboard > .row.dashboard-gap:nth-of-type(3) {
    gap: 8px;
    margin-top: 8px;
  }

  .mini-card {
    border-radius: 12px;
    min-height: 58px;
    padding: 8px 10px;
  }

  .mini-value {
    color: #fff;
    font-size: 16px;
  }

  .chart-panel {
    border-radius: 14px;
    padding: 12px;
  }

  .chart-panel h3,
  .chart-panel.best-selling-panel h3,
  .best-selling-panel {
    color: #fff;
  }

  .data-fallback {
    align-items: end;
    display: flex;
    gap: 10px;
    inset: 0;
    padding: 16px 0 0;
    position: absolute;
  }

  .chart-box { position: relative; }

  .data-fallback.is-hidden {
    display: none;
  }

  .fallback-column {
    align-items: center;
    display: flex;
    flex: 1;
    flex-direction: column;
    height: 100%;
    justify-content: flex-end;
    min-width: 0;
  }

  .fallback-value {
    color: #626270;
    font-size: 10px;
    margin-bottom: 6px;
  }

  .fallback-bar {
    background: linear-gradient(180deg, #60a5fa, #2563eb);
    border-radius: 8px 8px 3px 3px;
    min-height: 4px;
    width: min(42px, 70%);
  }

  #weeklySalesFallback .fallback-bar {
    background: linear-gradient(180deg, #FFA69E, #F88379);
  }

  #weeklySalesFallback .fallback-value,
  #weeklySalesFallback .fallback-label,
  #monthlySalesFallback .fallback-value,
  #monthlySalesFallback .fallback-label {
    color: #fff;
  }

  #monthlySalesFallback .fallback-bar {
    background: linear-gradient(180deg, #4ade80, #15803d);
  }

  .best-seller-bar {
    background: #fff;
  }

  .best-seller-row:nth-child(2) .best-seller-bar,
  .best-seller-row:nth-child(3) .best-seller-bar,
  .best-seller-row:nth-child(4) .best-seller-bar,
  .best-seller-row:nth-child(5) .best-seller-bar { background: #fff; }

  .fallback-label {
    color: #747480;
    font-size: 10px;
    margin-top: 8px;
    overflow: hidden;
    text-align: center;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
  }

  .chart-tabs span {
    background: #1a1a23;
    color: #aaaab6;
  }

  .chart-tabs span.active {
    background: transparent;
    border-bottom: 2px solid #fff;
    color: #fff;
  }

  .analytics-grid .chart-panel:first-child .chart-tabs span.active {
    border-bottom-color: #fff;
    color: #fff;
  }

  .best-seller-meta {
    border-bottom: 1px solid #252530;
    padding-bottom: 5px;
  }

  .best-seller-sold {
    color: #fff;
  }

  .rider-item,
  .best-selling-item {
    background: #15151d;
    border-color: #292934;
    color: #fff;
  }

  @media (max-width: 1199px) {
    .admin-dashboard > .row.dashboard-gap:first-of-type {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .analytics-grid .chart-panel {
      grid-column: span 6;
    }
  }
</style>

<style>
  /* Light, information-first dashboard layout. */
  body, .page-content, .page-header { background: #f8fafc; }
  .admin-dashboard { background: transparent; color: #172033; padding: 8px 0 28px; }
  .admin-dashboard h2 { color: #172033; font-size: 26px; letter-spacing: -.02em; margin: 0 0 20px; }
  .admin-dashboard > .row.dashboard-gap { gap: 16px; margin-bottom: 16px; }
  .admin-dashboard > .row.dashboard-gap:first-of-type { background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; gap: 14px; padding: 14px; }
  .sales-card, .mini-card, .chart-panel, .best-selling-panel { background: #fff !important; border: 1px solid #e2e8f0; box-shadow: 0 8px 24px rgba(15, 23, 42, .05); color: #172033; transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease; }
  .sales-card:hover, .mini-card:hover, .chart-panel:hover { border-color: #cbd5e1; box-shadow: 0 14px 30px rgba(15, 23, 42, .09); transform: translateY(-2px); }
  .sales-card { border-radius: 13px; min-height: 122px; padding: 16px; }
  .sales-card .label, .mini-label { color: #64748b; font-size: 12px; font-weight: 700; }
  .sales-card .value, .mini-value { color: #0f172a; font-size: 22px; font-weight: 800; }
  .mini-card { border-radius: 13px; min-height: 74px; padding: 13px 16px; }
  .mini-value { font-size: 20px; }
  .chart-panel { border-radius: 16px; padding: 18px; }
  .chart-panel h3, .chart-panel.best-selling-panel h3 { color: #172033; font-size: 16px; }
  .chart-tabs { border-color: #e2e8f0; }
  .chart-tabs span, .chart-tabs span.active { color: #64748b; }
  .chart-tabs span.active { border-bottom-color: #f88379; color: #ea580c; }
  .analytics-grid { gap: 16px; margin-top: 18px; }
  .analytics-grid .chart-panel { min-height: 300px; }
  .analytics-grid .best-selling-panel { grid-column: span 3; }
  .best-selling-panel, .best-selling-row, .best-seller-sold, .chart-panel.best-selling-panel h3 { color: #172033; }
  .best-seller-meta { border-color: #e2e8f0; }
  .best-seller-track { background: #f1f5f9; }
  .best-seller-bar { background: linear-gradient(90deg, #f88379, #fb7185); }
  .data-fallback, .fallback-value, .fallback-label { color: #64748b; }
  .stock-alert-modal { background: rgba(15, 23, 42, .44) !important; backdrop-filter: blur(4px); }
  .stock-alert-modal .modal-content { background: #fff; border: 1px solid #fed7aa; border-radius: 18px; box-shadow: 0 24px 60px rgba(15, 23, 42, .24); color: #172033; overflow: hidden; }
  .stock-alert-modal .modal-header { align-items: center; background: #fff7ed; border-color: #fed7aa; padding: 18px 22px; }
  .stock-alert-modal .modal-title { color: #9a3412; font-size: 19px; }
  .stock-alert-modal .modal-title .fa { color: #f97316; }
  .stock-alert-modal .modal-body { color: #334155; padding: 20px 22px; }
  .stock-alert-modal .modal-body ul { margin: 14px 0 0; padding-left: 20px; }
  .stock-alert-modal .modal-body li { color: #172033; margin-bottom: 8px; }
  .stock-alert-modal .modal-footer { background: #f8fafc; border-color: #e2e8f0; padding: 14px 22px; }
  .stock-alert-modal .btn-secondary { background: #172033; border-color: #172033; border-radius: 8px; font-weight: 700; padding: 8px 18px; }
  @media (max-width: 1199px) { .analytics-grid .best-selling-panel { grid-column: span 6; } }
  @media (max-width: 767px) { .analytics-grid .best-selling-panel { grid-column: span 1; } .admin-dashboard h2 { font-size: 22px; } }
</style>

<div class="admin-dashboard">
  <h2>Dashboard Overview</h2>

  @if($low_stock > 0)
    <div class="stock-alert-modal" id="lowStockAlert" role="dialog" aria-modal="true" aria-labelledby="lowStockAlertTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="lowStockAlertTitle"><span class="stock-alert-symbol"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span> Low-stock alert</h3>
            <button type="button" class="close text-white" data-stock-alert-close aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            {{ $low_stock }} food item{{ $low_stock === 1 ? ' is' : 's are' }} running low ({{ $lowStockThreshold }} or fewer remaining).
            <ul class="low-stock-list">
              @foreach($inventory_alerts->where('stock', '>', 0) as $food)
                <li>
                  <img src="{{ asset('food_img/' . $food->image) }}" alt="{{ $food->title }}" onerror="this.style.display='none'">
                  <span><strong>{{ $food->title }}</strong>: {{ $food->stock }} left</span>
                </li>
              @endforeach
            </ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-stock-alert-close>Dismiss</button>
          </div>
        </div>
      </div>
    </div>
    <style>
      .stock-alert-modal { align-items:center; background:rgba(0, 0, 0, .65); display:none; inset:0; justify-content:center; padding:16px; position:fixed; z-index:2000; }
      .stock-alert-modal.is-visible { display:flex; }
      .stock-alert-modal .modal-dialog { margin:0; max-width:520px; width:100%; }
      .stock-alert-modal .modal-content { background:#15171c; border:1px solid rgba(248, 131, 121, .7); border-radius:10px; color:#fff; }
      .stock-alert-modal .modal-header, .stock-alert-modal .modal-footer { border-color:#2b2f38; }
      .stock-alert-modal .modal-title { font-weight:800; }
      .stock-alert-modal .modal-title .fa { color:#fca5a5; }
      .stock-alert-modal .modal-body { color:#d1d5db; }
      .stock-alert-modal .modal-body li { color:#fff; margin-bottom:6px; }
      .stock-alert-modal { background:rgba(15, 23, 42, .44) !important; backdrop-filter:blur(4px); }
      .stock-alert-modal .modal-content { background:#fff; border:1px solid #fed7aa; border-radius:18px; box-shadow:0 24px 60px rgba(15, 23, 42, .24); color:#172033; overflow:hidden; }
      .stock-alert-modal .modal-header { align-items:center; background:#fff7ed; border-color:#fed7aa; padding:18px 22px; }
      .stock-alert-modal .modal-title { color:#9a3412; font-size:19px; }
      .stock-alert-modal .stock-alert-symbol { align-items:center; background:#fff1bf; border-radius:12px; display:inline-flex; height:42px; justify-content:center; margin-right:8px; width:42px; }
      .stock-alert-modal .modal-title .stock-alert-symbol .fa { color:#f59e0b; font-size:23px; }
      .stock-alert-modal .modal-body { color:#334155; padding:20px 22px; }
      .stock-alert-modal .low-stock-list { display:grid; gap:13px; list-style:none; margin:18px 0 0; padding:0; }
      .stock-alert-modal .low-stock-list li { align-items:center; color:#172033; display:flex; gap:13px; margin:0; }
      .stock-alert-modal .low-stock-list img { background:#fff7ed; border:2px solid #fff; border-radius:50%; box-shadow:0 4px 12px rgba(15, 23, 42, .12); height:48px; object-fit:cover; width:48px; }
      .stock-alert-modal .low-stock-list strong { color:#172033; }
      .stock-alert-modal .modal-footer { background:#f8fafc; border-color:#e2e8f0; padding:14px 22px; }
      .stock-alert-modal .btn-secondary { background:linear-gradient(135deg, #fb7185, #f88379); border:0; border-radius:10px; box-shadow:0 8px 16px rgba(248, 131, 121, .3); font-weight:800; padding:10px 22px; }
      /* Match the low-stock alert reference design. */
      .stock-alert-modal { background:rgba(71, 85, 105, .42) !important; backdrop-filter:blur(3px); }
      .stock-alert-modal .modal-dialog { max-width:570px; }
      .stock-alert-modal .modal-content { background:#fff; border:0; border-radius:20px; box-shadow:0 20px 46px rgba(15, 23, 42, .22); color:#1f2937; }
      .stock-alert-modal .modal-header { background:#fff; border-bottom:1px solid #e5e7eb; padding:26px 28px; }
      .stock-alert-modal .modal-title { align-items:center; color:#1f2937; display:flex; font-size:25px; font-weight:800; letter-spacing:-.02em; }
      .stock-alert-modal .stock-alert-symbol { background:transparent; border-radius:0; display:inline; height:auto; margin-right:10px; width:auto; }
      .stock-alert-modal .modal-title .stock-alert-symbol .fa { color:#fbbf24; font-size:36px; vertical-align:middle; }
      .stock-alert-modal .close { color:#64748b; font-size:29px; font-weight:300; opacity:1; text-shadow:none; }
      .stock-alert-modal .modal-body { color:#1f2937; font-size:17px; padding:24px 28px 26px; }
      .stock-alert-modal .low-stock-list { gap:16px; margin-top:20px; }
      .stock-alert-modal .low-stock-list li { color:#1e293b; font-size:18px; gap:16px; }
      .stock-alert-modal .low-stock-list img { border:0; border-radius:50%; box-shadow:0 5px 12px rgba(15, 23, 42, .16); height:52px; width:52px; }
      .stock-alert-modal .low-stock-list strong { color:#1e293b; }
      .stock-alert-modal .modal-footer { background:#fff; border-top:1px solid #e5e7eb; padding:18px 20px; }
      .stock-alert-modal .btn-secondary { background:#f87171; border:0; border-radius:10px; box-shadow:none; color:#fff; font-size:16px; font-weight:700; padding:10px 24px; }
      .stock-alert-modal .btn-secondary:hover { background:#ef4444; }
    </style>
    <script>
      (function () {
        function showLowStockAlert() {
        var lowStockAlert = document.getElementById('lowStockAlert');
        if (!lowStockAlert) return;

        // Move the overlay to the page root so it always covers the entire
        // admin dashboard (including the sidebar and header).
        document.body.appendChild(lowStockAlert);

        function closeLowStockAlert() {
          lowStockAlert.classList.remove('is-visible');
          lowStockAlert.setAttribute('aria-hidden', 'true');
        }

        lowStockAlert.classList.add('is-visible');
        lowStockAlert.setAttribute('aria-hidden', 'false');
        lowStockAlert.querySelectorAll('[data-stock-alert-close]').forEach(function (button) {
          button.addEventListener('click', closeLowStockAlert);
        });
        lowStockAlert.addEventListener('click', function (event) {
          if (event.target === lowStockAlert) closeLowStockAlert();
        });
        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') closeLowStockAlert();
        });
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', showLowStockAlert);
        } else {
          showLowStockAlert();
        }
      })();
    </script>
  @endif

  <div class="row dashboard-gap">
    <div class="sales-card-col">
      <div class="sales-card bg-teal">
        <div class="icon"><i class="fa fa-cubes"></i></div>
        <div class="label">Today Orders</div>
        <div class="value">&#8369;{{ number_format($daily_sales, 2) }}</div>
      </div>
    </div>

    <div class="sales-card-col">
      <div class="sales-card bg-orange">
        <div class="icon"><i class="fa fa-cubes"></i></div>
        <div class="label">Yesterday Orders</div>
        <div class="value">&#8369;{{ number_format($yesterday_sales, 2) }}</div>
      </div>
    </div>

    <div class="sales-card-col">
      <div class="sales-card bg-blue">
        <div class="icon"><i class="fa fa-refresh"></i></div>
        <div class="label">This Month</div>
        <div class="value">&#8369;{{ number_format($monthly_sales, 2) }}</div>
      </div>
    </div>

    <div class="sales-card-col">
      <div class="sales-card bg-cyan">
        <div class="icon"><i class="fa fa-calendar"></i></div>
        <div class="label">Last Month</div>
        <div class="value">&#8369;{{ number_format($last_month_sales, 2) }}</div>
      </div>
    </div>

    <div class="sales-card-col">
      <div class="sales-card bg-green">
        <div class="icon"><i class="fa fa-calendar"></i></div>
        <div class="label">All-Time Sales</div>
        <div class="value">&#8369;{{ number_format($total_sales, 2) }}</div>
      </div>
    </div>
  </div>

  <div class="row dashboard-gap">
    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-cyan"><i class="fa fa-archive"></i></div>
        <div>
          <div class="mini-label">Available Stock</div>
          <div class="mini-value">{{ $total_stock }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card low-stock-card">
        <div class="mini-icon bg-orange"><i class="fa fa-warning"></i></div>
        <div>
          <div class="mini-label">Low Stock</div>
          <div class="mini-value">{{ $low_stock }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-teal"><i class="fa fa-cutlery"></i></div>
        <div>
          <div class="mini-label">Food Items</div>
          <div class="mini-value">{{ $total_food }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-blue"><i class="fa fa-users"></i></div>
        <div>
          <div class="mini-label">Registered Users</div>
          <div class="mini-value">{{ $total_user }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-blue"><i class="fa fa-ban"></i></div>
        <div>
          <div class="mini-label">Out of Stock</div>
          <div class="mini-value">{{ $out_of_stock }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row dashboard-gap">
    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-orange"><i class="fa fa-shopping-cart"></i></div>
        <div>
          <div class="mini-label">Total Orders</div>
          <div class="mini-value">{{ $total_order }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-teal"><i class="fa fa-refresh"></i></div>
        <div>
          <div class="mini-label">Orders Pending</div>
          <div class="mini-value">{{ $pending_orders }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-blue"><i class="fa fa-truck"></i></div>
        <div>
          <div class="mini-label">Orders Processing</div>
          <div class="mini-value">{{ $processing_orders }}</div>
        </div>
      </div>
    </div>

    <div class="mini-card-col">
      <div class="mini-card">
        <div class="mini-icon bg-green"><i class="fa fa-check"></i></div>
        <div>
          <div class="mini-label">Orders Delivered</div>
          <div class="mini-value">{{ $total_delivered }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="analytics-grid">
    <div class="chart-panel chart-third">
      <h3>Daily Sales</h3>
      <div class="chart-tabs">
        <span class="active">Daily Sales</span>
      </div>
      <div class="chart-box chart-box-tall">
        <canvas id="dailySalesChart"></canvas>
        <div class="data-fallback" id="dailySalesFallback">
          @foreach($daily_sales_labels as $index => $label)
            <div class="fallback-column">
              <span class="fallback-value">&#8369;{{ number_format((float) ($daily_sales_values[$index] ?? 0)) }}</span>
              <span class="fallback-bar" style="height: {{ max(3, (((float) ($daily_sales_values[$index] ?? 0)) / $daily_sales_max) * 76) }}%"></span>
              <span class="fallback-label">{{ $label }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="chart-panel chart-third">
      <h3>Weekly Overview</h3>
      <div class="chart-tabs">
        <span class="active">Weekly Sales</span>
      </div>
      <div class="chart-box chart-box-tall">
        <canvas id="weeklySalesChart"></canvas>
        <div class="data-fallback" id="weeklySalesFallback">
          @foreach($weekly_sales_labels as $index => $label)
            <div class="fallback-column">
              <span class="fallback-value">&#8369;{{ number_format((float) ($weekly_sales_values[$index] ?? 0)) }}</span>
              <span class="fallback-bar" style="height: {{ max(3, (((float) ($weekly_sales_values[$index] ?? 0)) / $weekly_sales_max) * 76) }}%"></span>
              <span class="fallback-label">{{ $label }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="chart-panel chart-third">
      <h3>Monthly Sales</h3>
      <div class="chart-tabs">
        <span class="active">Monthly Trend</span>
      </div>
      <div class="chart-box chart-box-tall">
        <canvas id="monthlySalesChart"></canvas>
        <div class="data-fallback" id="monthlySalesFallback">
          @foreach($monthly_sales_labels as $index => $label)
            <div class="fallback-column">
              <span class="fallback-value">&#8369;{{ number_format((float) ($monthly_sales_values[$index] ?? 0)) }}</span>
              <span class="fallback-bar" style="height: {{ max(3, (((float) ($monthly_sales_values[$index] ?? 0)) / $monthly_sales_max) * 76) }}%"></span>
              <span class="fallback-label">{{ $label }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="chart-panel chart-full best-selling-panel">
      <h3>Best Selling Items</h3>
      <div class="best-seller-list">
        @forelse($best_selling_items as $item)
          <div class="best-seller-row">
            <div class="best-seller-meta">
              <span class="best-seller-title">{{ $loop->iteration }}. {{ $item['title'] }}</span>
              <span class="best-seller-sold">{{ (int) $item['sold'] }}</span>
            </div>
            <div class="best-seller-track">
              <div class="best-seller-bar" style="width: {{ max(6, (((int) $item['sold']) / $best_selling_max) * 100) }}%;"></div>
            </div>
          </div>
        @empty
          <div class="best-seller-row">No sold items yet.</div>
        @endforelse
      </div>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
      return;
    }

    function chartRendered(fallbackId) {
      var fallback = document.getElementById(fallbackId);
      if (fallback) fallback.classList.add('is-hidden');
    }
    var darkAxis = {
      ticks: { fontColor: '#646471', beginAtZero: true },
      gridLines: { color: 'rgba(23,23,33,0.08)' }
    };

    var mutedTicks = { fontColor: '#374151' };
    var whiteTicks = { fontColor: '#111827' };
    var dailyAxis = {
      scaleLabel: { display: true, labelString: 'Sales (PHP)', fontColor: '#111827' },
      ticks: { fontColor: '#111827', beginAtZero: true, callback: function (value) { return 'PHP ' + Number(value).toLocaleString(); } },
      gridLines: { color: 'rgba(17,24,39,0.10)' }
    };

    new Chart(document.getElementById('dailySalesChart'), {
      type: 'bar',
      data: {
        labels: @json($daily_sales_labels),
        datasets: [{
          label: 'Sales',
          data: @json($daily_sales_values),
          backgroundColor: ['#60a5fa', '#2563eb', '#60a5fa', '#2563eb', '#60a5fa', '#2563eb', '#60a5fa'],
          borderColor: ['#60a5fa', '#2563eb', '#60a5fa', '#2563eb', '#60a5fa', '#2563eb', '#60a5fa'],
          hoverBackgroundColor: '#93c5fd',
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
          xAxes: [{ scaleLabel: { display: true, labelString: 'Day', fontColor: '#111827' }, ticks: whiteTicks, gridLines: { display: false } }],
          yAxes: [dailyAxis]
        },
        tooltips: {
          callbacks: {
            label: function (tooltipItem) {
              return 'PHP ' + Number(tooltipItem.yLabel).toLocaleString();
            }
          }
        }
      }
    });
    chartRendered('dailySalesFallback');

    new Chart(document.getElementById('weeklySalesChart'), {
      type: 'pie',
      data: {
        labels: @json($weekly_sales_labels),
        datasets: [{
          label: 'Weekly Sales',
          data: @json($weekly_sales_values),
          backgroundColor: ['#56a6d9', '#f277a7', '#ff941f', '#a8d12b', '#F88379', '#2563eb'],
          borderColor: '#101016',
          hoverBorderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: true, position: 'bottom', labels: { fontColor: '#111827', boxWidth: 12, fontSize: 11, padding: 12 } },
        tooltips: { callbacks: { label: function (tooltipItem, data) {
          var label = data.labels[tooltipItem.index] || '';
          var value = data.datasets[0].data[tooltipItem.index] || 0;
          return label + ': PHP ' + Number(value).toLocaleString();
        } } }
      }
    });
    chartRendered('weeklySalesFallback');

    new Chart(document.getElementById('monthlySalesChart'), {
      type: 'line',
      data: {
        labels: @json($monthly_sales_labels),
        datasets: [{
          label: 'Monthly Sales',
          data: @json($monthly_sales_values),
          backgroundColor: 'rgba(34, 197, 94, 0.12)',
          borderColor: '#22c55e',
          pointBackgroundColor: '#fff',
          pointBorderColor: '#15803d',
          pointRadius: 3,
          borderWidth: 3,
          fill: true,
          lineTension: 0.42
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
          xAxes: [{
            scaleLabel: { display: true, labelString: 'Month', fontColor: '#111827' },
            ticks: mutedTicks,
            gridLines: { display: false }
          }],
          yAxes: [{
            ticks: {
              fontColor: '#111827',
              beginAtZero: true,
              callback: function (value) {
                return 'PHP ' + Number(value).toLocaleString();
              }
            },
            scaleLabel: { display: true, labelString: 'Sales (PHP)', fontColor: '#111827' },
            gridLines: { color: 'rgba(17,24,39,0.10)' }
          }]
        }
      }
    });
    chartRendered('monthlySalesFallback');
  });
</script>
