<!DOCTYPE html>
<html>
  <head>
    @include('admin.css')
    <style>
      .inventory-wrap {
        background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.08), transparent 28%), #0c0d10;
        border: 1px solid #2b2f38;
        border-radius: 8px;
        color: #fff;
        margin: 0;
        min-height: calc(100vh - 120px);
        padding: 24px;
      }

      .inventory-header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 24px;
      }

      .inventory-card {
        background: #15171c;
        border: 1px solid #2b2f38;
        border-radius: 8px;
        overflow-x: auto;
      }

      .inventory-table {
        margin: 0;
        min-width: 980px;
        width: 100%;
      }

      .inventory-table th {
        background: #101116;
        border-bottom: 1px solid #2b2f38;
        color: #a6abb6;
        font-size: 12px;
        font-weight: 800;
        padding: 14px;
        text-align: left;
        text-transform: uppercase;
      }

      .inventory-table td {
        background: #15171c;
        border-top: 1px solid #2b2f38;
        color: #fff;
        padding: 14px;
        vertical-align: middle;
      }

      .inventory-table tr:hover td {
        background: #1f2229;
      }

      .inventory-table img {
        background: #0f1015;
        border: 1px solid #2b2f38;
        border-radius: 6px;
        height: 76px;
        object-fit: contain;
        padding: 4px;
        width: 76px;
      }

      .stock-form {
        align-items: center;
        display: flex;
        flex-wrap: nowrap;
        gap: 8px;
        min-width: 170px;
      }

      .stock-form input {
        background: #101116;
        border: 1px solid #4a4f5c;
        color: #fff;
        flex: 0 0 84px;
        font-size: 17px;
        font-weight: 800;
        height: 50px;
        line-height: 1;
        max-width: none;
        min-width: 84px;
        padding: 8px 12px;
        text-align: center;
        width: 84px !important;
      }

      .stock-form input:focus {
        background: #101116;
        border-color: #F88379;
        box-shadow: 0 0 0 3px rgba(255, 33, 79, .18);
        color: #fff;
      }

      .stock-form button {
        flex: 0 0 auto;
        height: 50px;
        padding: 8px 15px;
      }

      .inventory-table th:last-child,
      .inventory-table td:last-child {
        min-width: 190px;
        width: 190px;
      }

      .stock-badge {
        border-radius: 999px;
        display: inline-block;
        font-weight: 700;
        padding: 5px 10px;
      }

      .transaction-time {
        color: #dbeafe;
        display: block;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
      }

      .transaction-time small {
        color: #94a3b8;
        display: block;
        font-size: 11px;
        font-weight: 800;
        margin-bottom: 2px;
        text-transform: uppercase;
      }

      .stock-ok { background: #0f766e; }
      .stock-low { background: #F88379; }
      .stock-out { background: #b91c1c; }
    </style>
  </head>
  <body>
    @php
      $adminMenuImages = [
        'chicken-burger' => 'chicken burger.png',
        'egg-bunwich' => 'egg-bunwich.png',
        'cheesy-chicken' => 'chessy-chicken.png',
        'chessy-chicken' => 'chessy-chicken.png',
        'cheesy-chicken-hotdog-sandwich' => 'chessy-chicken.png',
        'chessy-chicken-hotdog-sandwich' => 'chessy-chicken.png',
        'cheesy-chicken-hotdog' => 'chessy-chicken.png',
        'fries' => 'fries.png',
        'classic-fries' => 'fries.png',
        'ice-cream' => 'ice-cream.png',
        'mi-cusina-ice-cream' => 'ice-cream.png',
        'creamy-carbonara' => 'creamy-carbonara.png',
        'classic-spaghetti' => 'classic-spaghetti.png',
        'chicken-nugget' => 'chicken-nugget.png',
        'chicken-nuggets' => 'chicken-nugget.png',
        'chicken-nuggets-rice-bowl' => 'chicken-nugget.png',
        '2-pcs-chicken-meal' => '2-pcs chicken meal.png',
        '2-pc-chicken-meal' => '2-pcs chicken meal.png',
        '1-pc-chicken-meal' => '1-pc-chicken meal.png',
        'chicken-spaghetti' => 'chicken-spaghetti.png',
        'chicken-adobo-bunwich' => 'chicken-adobo-bundwich.png',
        'chicken-adobo-bundwich' => 'chicken-adobo-bundwich.png',
        'chicken-fillet' => 'chicken-fillet.png',
        'chicken-burger-spaghetti' => 'chicken-burger-spaghetti.png',
        'chicken-adobo-flakes' => 'chicken-adobo-flakes.png',
        'chicken-teriyaki' => 'chicken-tereyaki.png',
        'chicken-tereyaki' => 'chicken-tereyaki.png',
        'ham-bowl' => 'ham-bowl.png',
        'siomai' => 'siomai.png',
        'siomai-egg-rice-bowl' => 'siomai.png',
      ];
    @endphp

    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-content">
      <div class="page-header">
        <div class="container-fluid inventory-wrap">
          <div class="inventory-header">
            <h1>Inventory</h1>
            <a class="btn btn-warning" href="{{ url('add_food') }}">Add Food</a>
          </div>

          @if(session()->has('message'))
            <div class="alert alert-success">{{ session()->get('message') }}</div>
          @endif

          <div class="inventory-card">
            <table class="inventory-table">
              <tr>
                <th>Image</th>
                <th>Food</th>
                <th>Price</th>
                <th>Available Stock</th>
                <th>Status</th>
                <th>Added At</th>
                <th>Updated At</th>
                <th>Update Stock</th>
              </tr>

              @foreach($data as $food)
                @php
                  $mappedImage = $adminMenuImages[\Illuminate\Support\Str::slug($food->title)] ?? null;
                  $foodImage = $mappedImage ? asset('assets/imgs/' . $mappedImage) : asset('food_img/' . $food->image);
                @endphp
                <tr>
                  <td><img src="{{ $foodImage }}" alt="{{ $food->title }}"></td>
                  <td>{{ $food->title }}</td>
                  <td>&#8369;{{ number_format((float) $food->price, 2) }}</td>
                  <td>{{ $food->stock }}</td>
                  <td>
                    @if($food->stock <= 0)
                      <span class="stock-badge stock-out">Out of Stock</span>
                    @elseif($food->stock <= 5)
                      <span class="stock-badge stock-low">Low Stock</span>
                    @else
                      <span class="stock-badge stock-ok">In Stock</span>
                    @endif
                  </td>
                  <td>
                    <span class="transaction-time">
                      <small>Added</small>
                      {{ $food->created_at ? $food->created_at->format('M d, Y g:i A') : 'N/A' }}
                    </span>
                  </td>
                  <td>
                    <span class="transaction-time">
                      <small>Latest</small>
                      {{ $food->updated_at ? $food->updated_at->format('M d, Y g:i A') : 'N/A' }}
                    </span>
                  </td>
                  <td>
                    <form class="stock-form" action="{{ url('update_stock', $food->id) }}" method="post">
                      @csrf
                      <input class="form-control" type="number" name="stock" min="0" value="{{ $food->stock }}" required>
                      <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>

    @include('admin.js')
  </body>
</html>
