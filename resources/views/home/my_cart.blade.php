<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.css')

    <style>
        body {
            background: #000;
            color: #111;
            margin: 0;
        }

        .cart-topbar {
            align-items: center;
            background: #050505;
            display: flex;
            justify-content: space-between;
            min-height: 96px;
            padding: 0 34px;
        }

        .cart-topbar a,
        .cart-topbar button {
            background: transparent;
            border: 0;
            color: #fff;
            cursor: pointer;
            font-size: 18px;
            font-weight: 800;
            padding: 0;
            text-decoration: none;
        }

        .cart-topbar a:hover,
        .cart-topbar button:hover {
            color: #F88379;
        }

        .cart-topbar form {
            margin: 0;
        }

        .cart-top-actions {
            align-items: center;
            display: flex;
            gap: 24px;
        }

        .cart-top-actions a {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
            text-decoration: none;
        }

        .cart-top-actions a:hover {
            color: #F88379;
        }

        .cart-user-menu {
            position: relative;
        }

        .cart-user-menu summary {
            align-items: center;
            background: #eef3ff;
            border: 3px solid #fff;
            border-radius: 999px;
            box-shadow: 0 0 0 2px #1d2330;
            color: #F88379;
            cursor: pointer;
            display: inline-flex;
            font-size: 18px;
            font-weight: 800;
            height: 46px;
            justify-content: center;
            list-style: none;
            width: 46px;
        }

        .cart-user-menu summary::-webkit-details-marker {
            display: none;
        }

        .cart-user-dropdown {
            background: #111;
            border: 1px solid #2b2b2b;
            border-radius: 8px;
            box-shadow: 0 18px 38px rgba(0, 0, 0, .34);
            color: #fff;
            min-width: 230px;
            padding: 10px;
            position: absolute;
            right: 0;
            top: calc(100% + 12px);
            z-index: 30;
        }

        .cart-user-info {
            border-bottom: 1px solid #2b2b2b;
            margin-bottom: 8px;
            padding: 8px 10px 12px;
        }

        .cart-user-info strong,
        .cart-user-info span {
            display: block;
        }

        .cart-user-info strong {
            color: #fff;
            font-size: 15px;
        }

        .cart-user-info span {
            color: #aaa;
            font-size: 13px;
            margin-top: 4px;
        }

        .cart-user-dropdown button {
            background: transparent;
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            padding: 10px;
            text-align: left;
            width: 100%;
        }

        .cart-user-dropdown a {
            border-radius: 6px;
            color: #fff;
            display: block;
            font-size: 14px;
            font-weight: 800;
            padding: 10px;
            text-decoration: none;
        }

        .cart-user-dropdown a:hover,
        .cart-user-dropdown button:hover {
            background: #F88379;
            color: #050505;
        }

        .cart-page {
            background: #000;
            min-height: calc(100vh - 96px);
            padding: 34px 28px 80px;
        }

        .cart-title {
            color: #fff;
            font-size: 34px;
            font-weight: 500;
            letter-spacing: 7px;
            margin: 0 0 28px;
            text-align: center;
            text-transform: uppercase;
        }

        .cart-shell {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 24px 60px rgba(255, 255, 255, 0.08);
            margin: 0 auto;
            max-width: 1460px;
            padding: 44px;
        }

        .cart-brand {
            align-items: center;
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }

        .cart-brand img {
            height: 44px;
            object-fit: contain;
            width: 44px;
        }

        .cart-brand strong {
            color: #111;
            font-size: 24px;
            font-weight: 900;
        }

        .cart-heading {
            color: #000;
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .cart-main {
            padding: 0;
        }

        .cart-main-head {
            align-items: center;
            border-bottom: 0;
            display: flex;
            justify-content: space-between;
            margin-bottom: 34px;
            padding-bottom: 0;
        }

        .cart-count {
            color: #222;
            font-size: 16px;
            font-weight: 900;
        }

        .cart-grid {
            align-items: start;
            display: grid;
            gap: 34px;
            grid-template-columns: minmax(0, 1fr) 390px;
        }

        .cart-list,
        .checkout-details {
            border: 1px solid #d8d8d8;
            border-radius: 18px;
            background: #fff;
        }

        .cart-list {
            border: 1px solid #d8d8d8;
            border-radius: 18px;
            overflow: hidden;
        }

        .cart-list-head,
        .cart-item {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(260px, 1fr) 170px 130px 130px;
            padding: 22px 24px;
        }

        .cart-list-head {
            border-bottom: 1px solid #d8d8d8;
            color: #222;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .cart-item {
            align-items: center;
            border-bottom: 1px solid #e5e5e5;
        }

        .cart-item:last-child {
            border-bottom: 0;
        }

        .cart-product {
            align-items: center;
            display: grid;
            gap: 18px;
            grid-template-columns: 96px minmax(0, 1fr);
        }

        .cart-product img {
            background: #f5f5f5;
            border-radius: 16px;
            height: 96px;
            object-fit: cover;
            width: 96px;
        }

        .cart-product strong {
            color: #000;
            display: block;
            font-size: 22px;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .cart-product span,
        .cart-muted {
            color: #777;
            font-size: 14px;
        }

        .qty-pill {
            align-items: center;
            border: 1px solid #111;
            border-radius: 999px;
            display: inline-flex;
            font-weight: 800;
            gap: 12px;
            justify-content: center;
            min-width: 82px;
            padding: 8px 12px;
        }

        .qty-stepper {
            align-items: center;
            border: 1px solid #d4d4d4;
            border-radius: 999px;
            display: inline-flex;
            gap: 10px;
            min-height: 42px;
            padding: 4px 8px;
        }

        .qty-stepper form {
            margin: 0;
        }

        .qty-stepper button {
            align-items: center;
            background: #fff;
            border: 0;
            border-radius: 999px;
            color: #111;
            cursor: pointer;
            display: inline-flex;
            font-size: 20px;
            font-weight: 800;
            height: 30px;
            justify-content: center;
            line-height: 1;
            width: 30px;
        }

        .qty-stepper button:hover {
            background: #111;
            color: #fff;
        }

        .qty-stepper button:disabled {
            color: #aaa;
            cursor: not-allowed;
        }

        .qty-stepper strong {
            color: #111;
            font-size: 16px;
            min-width: 20px;
            text-align: center;
        }

        .cart-price {
            color: #000;
            font-size: 20px;
            font-weight: 900;
        }

        .empty-cart {
            color: #555;
            font-size: 18px;
            padding: 44px 18px;
            text-align: center;
        }

        .continue-shopping {
            color: #F88379;
            display: inline-flex;
            font-size: 14px;
            font-weight: 800;
            margin-top: 28px;
            text-decoration: none;
        }

        .continue-shopping:hover {
            color: #111;
            text-decoration: none;
        }

        .order-summary,
        .checkout-details {
            padding: 24px;
        }

        .order-summary {
            align-self: stretch;
            background: #fff;
            border: 1px solid #d8d8d8;
            border-radius: 18px;
            min-height: 100%;
            padding: 34px 30px;
        }

        .order-summary h2,
        .checkout-details h2 {
            color: #000;
            font-size: 18px;
            font-weight: 900;
            margin: 0 0 22px;
        }

        .summary-line {
            align-items: center;
            color: #222;
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .summary-line strong {
            color: #000;
        }

        .summary-total {
            border-top: 1px solid #ddd;
            color: #000;
            display: flex;
            font-size: 24px;
            font-weight: 900;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 18px;
        }

        .checkout-button {
            background: #F88379;
            border: 0;
            border-radius: 999px;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            font-weight: 900;
            margin-top: 24px;
            min-height: 54px;
            width: 100%;
        }

        .checkout-button:disabled {
            background: #999;
            cursor: not-allowed;
            opacity: 1;
        }

        .checkout-details {
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .checkout-modal {
            align-items: center;
            background: rgba(0, 0, 0, 0.72);
            display: none;
            inset: 0;
            justify-content: center;
            padding: 24px;
            position: fixed;
            z-index: 2000;
        }

        .checkout-modal.is-visible {
            display: flex;
        }

        .checkout-modal .checkout-details {
            max-width: 520px;
            width: 100%;
        }

        .checkout-modal-head {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .checkout-modal-head h2 {
            margin: 0;
        }

        .checkout-close {
            align-items: center;
            background: #000;
            border: 0;
            border-radius: 999px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-size: 22px;
            height: 38px;
            justify-content: center;
            line-height: 1;
            width: 38px;
        }

        .checkout-form {
            display: grid;
            gap: 14px;
        }

        .form-row label {
            color: #222;
            display: block;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 7px;
        }

        .form-row input,
        .form-row select,
        .form-row textarea {
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 12px;
            color: #000;
            font-size: 15px;
            min-height: 46px;
            padding: 10px 12px;
            width: 100%;
        }

        .form-row textarea {
            min-height: 88px;
            resize: vertical;
        }

        .payment-qr-panel,
        .payment-receipt {
            background: #fafafa;
            border: 1px solid #ddd;
            border-radius: 14px;
            display: none;
            padding: 16px;
        }

        .payment-qr-panel.is-visible,
        .payment-receipt.is-visible {
            display: block;
        }

        .payment-qr-panel h3,
        .payment-receipt h3 {
            color: #000;
            font-size: 16px;
            margin: 0 0 10px;
        }

        .payment-qr-panel img {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: none;
            max-width: 180px;
            padding: 8px;
            width: 100%;
        }

        .payment-qr-panel p,
        .payment-receipt p {
            color: #555;
            margin: 8px 0;
        }

        .payment-receipt strong {
            color: #000;
        }

        @media (max-width: 991.98px) {
            .cart-grid {
                grid-template-columns: 1fr;
            }

            .cart-list-head {
                display: none;
            }

            .cart-item {
                grid-template-columns: 1fr;
            }

            .cart-grid {
                gap: 24px;
            }
        }

        @media (max-width: 575.98px) {
            .cart-shell {
                border-radius: 16px;
                padding: 22px;
            }

            .cart-heading {
                font-size: 34px;
            }
        }
    </style>
</head>

<body>
    <header class="cart-topbar">
        <a href="{{ url('/') }}">Home</a>
        @php
            $userInitial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
        @endphp
        <div class="cart-top-actions">
            <details class="cart-user-menu">
                <summary aria-label="Open user menu">{{ $userInitial }}</summary>
                <div class="cart-user-dropdown">
                    <div class="cart-user-info">
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>{{ Auth::user()->email }}</span>
                    </div>
                    <a href="{{ url('/') }}">Home</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Log Out</button>
                    </form>
                </div>
            </details>
        </div>
    </header>

    <main class="cart-page">
        <h1 class="cart-title">Cart Page</h1>

        <section class="cart-shell">
            @php
                $menuImages = [
                    'chicken-burger' => 'chicken burger.png',
                    'egg-bunwich' => 'egg-bunwich.png',
                    'cheesy-chicken' => 'chessy-chicken.png',
                    'cheesy-chicken-hotdog-sandwich' => 'chessy-chicken.png',
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
                    'chicken-fillet' => 'chicken-fillet.png',
                    'chicken-burger-spaghetti' => 'chicken-burger-spaghetti.png',
                    'chicken-adobo-flakes' => 'chicken-adobo-flakes.png',
                    'chicken-teriyaki' => 'chicken-tereyaki.png',
                    'ham-bowl' => 'ham-bowl.png',
                    'siomai' => 'siomai.png',
                    'siomai-egg-rice-bowl' => 'siomai.png',
                ];
                $resolveFoodImage = function ($title, $image) use ($menuImages) {
                    $slug = \Illuminate\Support\Str::of($title)
                        ->slug()
                        ->replace('chessy', 'cheesy')
                        ->replace('bundwich', 'bunwich')
                        ->replace('tereyaki', 'teriyaki')
                        ->toString();
                    return isset($menuImages[$slug]) ? asset('assets/imgs/' . $menuImages[$slug]) : asset('food_img/' . $image);
                };
                $total_price = 0;
                $total_items = 0;
            @endphp

            @foreach ($data as $cart)
                @php
                    $lineTotal = (float) $cart->price;
                    $total_price += $lineTotal;
                    $total_items += (int) $cart->quantity;
                @endphp
            @endforeach

                <div class="cart-grid">
                    <div class="cart-main">
                        <div class="cart-brand">
                            <img src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina logo">
                            <strong>Mi Cusina</strong>
                        </div>

                        <div class="cart-main-head">
                            <h2 class="cart-heading">Shopping Cart</h2>
                            <span class="cart-count">{{ $total_items }} {{ \Illuminate\Support\Str::plural('Item', $total_items) }}</span>
                        </div>

                        <div class="cart-list">
                            <div class="cart-list-head">
                                <div>Product Details</div>
                                <div>Quantity</div>
                                <div>Price</div>
                                <div>Total</div>
                            </div>

                            @forelse ($data as $cart)
                                @php
                                    $quantity = max(1, (int) $cart->quantity);
                                    $lineTotal = (float) $cart->price;
                                    $unitPrice = $lineTotal / $quantity;
                                    $decreaseQty = max(1, $quantity - 1);
                                    $increaseQty = $quantity + 1;
                                    $cartImage = $resolveFoodImage($cart->title, $cart->image);
                                @endphp
                                <div class="cart-item">
                                    <div class="cart-product">
                                        <img src="{{ $cartImage }}" alt="{{ $cart->title }}">
                                        <div>
                                            <strong>{{ $cart->title }}</strong>
                                            <span>&#8369;{{ number_format($unitPrice, 2) }} each</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="qty-stepper" aria-label="Quantity for {{ $cart->title }}">
                                            <form action="{{ url('update_cart', $cart->id) }}" method="post">
                                                @csrf
                                                <input type="hidden" name="quantity" value="{{ $decreaseQty }}">
                                                <button type="submit" {{ $quantity <= 1 ? 'disabled' : '' }} aria-label="Decrease {{ $cart->title }}">-</button>
                                            </form>
                                            <strong>{{ $quantity }}</strong>
                                            <form action="{{ url('update_cart', $cart->id) }}" method="post">
                                                @csrf
                                                <input type="hidden" name="quantity" value="{{ $increaseQty }}">
                                                <button type="submit" aria-label="Increase {{ $cart->title }}">+</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="cart-price">&#8369;{{ number_format($unitPrice, 2) }}</div>
                                    <div class="cart-price">&#8369;{{ number_format($lineTotal, 2) }}</div>
                                </div>
                            @empty
                                <div class="empty-cart">Your cart is empty.</div>
                            @endforelse
                        </div>

                        <a class="continue-shopping" href="{{ url('/?section=food') }}">&larr; Continue Shopping</a>
                    </div>

                    <aside>
                        <div class="order-summary">
                            <h2>Order Summary</h2>
                            <div class="summary-line">
                                <span>Items</span>
                                <strong>{{ $total_items }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Sub Total</span>
                                <strong>&#8369;{{ number_format($total_price, 2) }}</strong>
                            </div>
                            <div class="summary-line">
                                <span>Delivery Fee</span>
                                <strong>To be confirmed</strong>
                            </div>
                            <div class="summary-total">
                                <span>Total</span>
                                <span>&#8369;{{ number_format($total_price, 2) }}</span>
                            </div>
                            <button class="checkout-button" id="openCheckout" type="button" {{ $data->isEmpty() ? 'disabled' : '' }}>Checkout Now</button>
                        </div>
                    </aside>
                </div>

                <div class="checkout-modal" id="checkoutModal">
                    <div class="checkout-details" role="dialog" aria-modal="true" aria-labelledby="checkoutTitle">
                        <div class="checkout-modal-head">
                            <h2 id="checkoutTitle">Customer Details & Payment</h2>
                            <button class="checkout-close" id="closeCheckout" type="button" aria-label="Close checkout">&times;</button>
                        </div>

                        <form action="{{ url('confirm_order') }}" method="post">
                            @csrf
                            <div class="checkout-form">
                                <input type="hidden" name="email" value="{{ Auth()->user()->email }}">

                                <div class="form-row">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" name="name" value="" required>
                                </div>

                                <div class="form-row">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="tel" name="phone" value="" inputmode="tel"
                                        pattern="\+639[0-9]{9}" maxlength="13" required
                                        oninput="this.value = '+639' + this.value.replace(/[^0-9]/g, '').replace(/^639/, '').slice(0, 9)">
                                </div>

                                <div class="form-row">
                                    <label for="municipality">Municipality</label>
                                    <select id="municipality" name="municipality" required>
                                        <option value="">Select municipality</option>
                                        <option value="Bantayan">Bantayan</option>
                                        <option value="Madridejos">Madridejos</option>
                                        <option value="Santa Fe">Santa Fe</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <label for="barangay">Barangay</label>
                                    <select id="barangay" name="barangay" required>
                                        <option value="">Select barangay</option>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <label for="purok">Purok</label>
                                    <input id="purok" type="text" name="purok" required>
                                </div>

                                <div class="form-row">
                                    <label for="address_details">Details</label>
                                    <textarea id="address_details" name="address_details" placeholder="House color, landmark, gate number, or delivery notes"></textarea>
                                </div>

                                <div class="form-row">
                                    <label for="payment_method">Payment Method</label>
                                    <select id="payment_method" name="payment_method" required>
                                        <option value="Cash on Delivery">Cash on Delivery</option>
                                        <option value="GCash">GCash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                <div class="payment-qr-panel" id="paymentQrPanel">
                                    <h3 id="paymentQrTitle">Payment QR</h3>
                                    <img id="paymentQrImage" src="" alt="Payment QR">
                                    <p id="paymentQrMissing" style="display:none;">QR image not found. Please ask staff for payment details.</p>
                                    <p>After sending payment, type your reference number below.</p>
                                </div>

                                <div class="form-row" id="paymentReferenceRow" style="display:none;">
                                    <label for="payment_reference">Reference #</label>
                                    <input id="payment_reference" type="text" name="payment_reference" placeholder="Enter payment reference number">
                                </div>

                                <div class="payment-receipt" id="paymentReceipt">
                                    <h3>Payment Receipt</h3>
                                    <p>Method: <strong id="receiptMethod"></strong></p>
                                    <p>Reference #: <strong id="receiptReference"></strong></p>
                                    <p>Total Paid: <strong>&#8369;{{ number_format($total_price, 2) }}</strong></p>
                                </div>

                                <button class="checkout-button" type="submit">Confirm Order</button>
                            </div>
                        </form>
                    </div>
                </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var barangays = {
                'Bantayan': [
                    'Atop-atop', 'Baigad', 'Baod', 'Binaobao', 'Botigues', 'Doong', 'Guiwanon',
                    'Hilotongan', 'Kabac', 'Kabangbang', 'Kampingganon', 'Kangkaibe', 'Lipayran',
                    'Luyongbaybay', 'Mojon', 'Obo-ob', 'Patao', 'Putian', 'Sillon', 'Suba',
                    'Sulangan', 'Sungko', 'Tamiao', 'Ticad'
                ],
                'Madridejos': [
                    'Bunakan', 'Kangwayan', 'Kaongkod', 'Kodia', 'Maalat', 'Malbago',
                    'Mancilang', 'Pili', 'Poblacion', 'San Agustin', 'Tabagak',
                    'Talangnan', 'Tarong', 'Tugas'
                ],
                'Santa Fe': [
                    'Balidbid', 'Hagdan', 'Hilantagaan', 'Kinatarkan', 'Langub',
                    'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay'
                ]
            };

            var municipality = document.getElementById('municipality');
            var barangay = document.getElementById('barangay');
            var paymentMethod = document.getElementById('payment_method');
            var paymentQrPanel = document.getElementById('paymentQrPanel');
            var paymentQrTitle = document.getElementById('paymentQrTitle');
            var paymentQrImage = document.getElementById('paymentQrImage');
            var paymentQrMissing = document.getElementById('paymentQrMissing');
            var paymentReferenceRow = document.getElementById('paymentReferenceRow');
            var paymentReference = document.getElementById('payment_reference');
            var paymentReceipt = document.getElementById('paymentReceipt');
            var receiptMethod = document.getElementById('receiptMethod');
            var receiptReference = document.getElementById('receiptReference');
            var checkoutModal = document.getElementById('checkoutModal');
            var openCheckout = document.getElementById('openCheckout');
            var closeCheckout = document.getElementById('closeCheckout');

            function showCheckout() {
                checkoutModal.classList.add('is-visible');
                document.body.style.overflow = 'hidden';
                document.getElementById('name').focus();
            }

            function hideCheckout() {
                checkoutModal.classList.remove('is-visible');
                document.body.style.overflow = '';
            }

            if (openCheckout) {
                openCheckout.addEventListener('click', showCheckout);
            }

            closeCheckout.addEventListener('click', hideCheckout);
            checkoutModal.addEventListener('click', function (event) {
                if (event.target === checkoutModal) {
                    hideCheckout();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && checkoutModal.classList.contains('is-visible')) {
                    hideCheckout();
                }
            });

            function fillBarangays() {
                var selected = municipality.value;
                barangay.innerHTML = '<option value="">Select barangay</option>';

                (barangays[selected] || []).forEach(function (name) {
                    var option = document.createElement('option');
                    option.value = name;
                    option.textContent = name;
                    barangay.appendChild(option);
                });
            }

            municipality.addEventListener('change', fillBarangays);

            function updatePaymentFields() {
                var method = paymentMethod.value;
                var needsReference = method === 'GCash' || method === 'Bank Transfer';

                paymentQrPanel.classList.toggle('is-visible', needsReference);
                paymentReferenceRow.style.display = needsReference ? 'block' : 'none';
                paymentReference.required = needsReference;

                if (!needsReference) {
                    paymentReference.value = '';
                    paymentReceipt.classList.remove('is-visible');
                    paymentQrImage.style.display = 'none';
                    return;
                }

                paymentQrTitle.textContent = method + ' QR Payment';
                paymentQrImage.src = method === 'GCash' ? '{{ asset('payment/gcash-qr.png') }}' : '{{ asset('payment/bank-qr.png') }}';
                paymentQrImage.style.display = 'block';
                paymentQrMissing.style.display = 'none';
                receiptMethod.textContent = method;
                updateReceipt();
            }

            function updateReceipt() {
                var method = paymentMethod.value;
                var reference = paymentReference.value.trim();
                var canShow = (method === 'GCash' || method === 'Bank Transfer') && reference.length > 0;

                receiptMethod.textContent = method;
                receiptReference.textContent = reference;
                paymentReceipt.classList.toggle('is-visible', canShow);
            }

            paymentQrImage.addEventListener('error', function () {
                paymentQrImage.style.display = 'none';
                paymentQrMissing.style.display = 'block';
            });

            paymentMethod.addEventListener('change', updatePaymentFields);
            paymentReference.addEventListener('input', updateReceipt);
            updatePaymentFields();
        });
    </script>
</body>
</html>
