<!DOCTYPE html>
<html lang="en">
<head>
    @include('home.css')
    <style>
        body {
            background: #000;
            color: #111;
            margin: 0;
            overflow: hidden;
        }

        .track-page {
            align-items: center;
            background: #000;
            display: flex;
            height: 100vh;
            justify-content: center;
            padding: 28px;
            position: relative;
        }

        .track-top {
            align-items: center;
            display: flex;
            gap: 24px;
            position: fixed;
            right: 34px;
            top: 28px;
            z-index: 20;
        }

        .track-cart {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
            text-decoration: none;
        }

        .track-cart-count {
            align-items: center;
            background: #F88379;
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 12px;
            height: 22px;
            justify-content: center;
            margin-left: 4px;
            min-width: 22px;
            padding: 0 6px;
        }

        .track-user-menu {
            position: relative;
        }

        .track-user-menu summary {
            align-items: center;
            background: #eef3ff;
            border: 3px solid #fff;
            border-radius: 999px;
            box-shadow: 0 0 0 2px #1d2330;
            color: #F88379;
            cursor: pointer;
            display: inline-flex;
            font-size: 18px;
            font-weight: 900;
            height: 52px;
            justify-content: center;
            list-style: none;
            width: 52px;
        }

        .track-user-menu summary::-webkit-details-marker {
            display: none;
        }

        .track-user-dropdown {
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

        .track-user-info {
            border-bottom: 1px solid #2b2b2b;
            margin-bottom: 8px;
            padding: 8px 10px 12px;
        }

        .track-user-info strong,
        .track-user-info span {
            display: block;
        }

        .track-user-info span {
            color: #aaa;
            font-size: 13px;
            margin-top: 4px;
        }

        .track-user-dropdown button {
            background: transparent;
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-weight: 900;
            padding: 10px;
            text-align: left;
            width: 100%;
        }

        .track-user-dropdown a {
            border-radius: 6px;
            color: #fff;
            display: block;
            font-size: 14px;
            font-weight: 900;
            padding: 10px;
            text-decoration: none;
        }

        .track-user-dropdown a:hover,
        .track-user-dropdown button:hover {
            background: #F88379;
            color: #050505;
        }

        .track-shell {
            background: #fff;
            border-radius: 22px;
            display: grid;
            gap: 34px;
            grid-template-columns: minmax(0, 1fr) 360px;
            max-height: calc(100vh - 56px);
            max-width: 1180px;
            padding: 42px;
            width: min(1180px, calc(100vw - 56px));
        }

        .track-back {
            color: #111;
            display: inline-flex;
            font-size: 30px;
            margin-bottom: 26px;
            text-decoration: none;
        }

        .track-title {
            color: #000;
            font-size: 42px;
            font-weight: 900;
            margin: 0 0 24px;
        }

        .track-status-card,
        .track-summary,
        .track-items {
            border: 1px solid #d8d8d8;
            border-radius: 18px;
            background: #fff;
        }

        .track-status-card {
            padding: 28px;
            text-align: center;
        }

        .track-status-card p {
            color: #555;
            font-size: 16px;
            margin: 0;
        }

        .track-status-card h2 {
            color: #111;
            font-size: 46px;
            font-weight: 900;
            margin: 8px 0 22px;
        }

        .track-bars {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, 1fr);
            margin: 0 auto 24px;
            max-width: 460px;
        }

        .track-bars span {
            background: #dedede;
            border-radius: 999px;
            height: 7px;
            overflow: hidden;
        }

        .track-bars span:before {
            background: #F88379;
            content: "";
            display: block;
            height: 100%;
            width: var(--fill, 0%);
        }

        .track-steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            margin: 0 auto 20px;
            max-width: 540px;
        }

        .track-step {
            align-items: center;
            display: flex;
            flex-direction: column;
            gap: 9px;
            position: relative;
        }

        .track-step:not(:last-child):after {
            background: #dedede;
            content: "";
            height: 4px;
            left: calc(50% + 25px);
            position: absolute;
            right: calc(-50% + 25px);
            top: 23px;
        }

        .track-step.is-complete:not(:last-child):after {
            background: #F88379;
        }

        .track-step-icon {
            align-items: center;
            background: #dedede;
            border: 4px solid #fff;
            border-radius: 999px;
            color: #777;
            display: flex;
            height: 50px;
            justify-content: center;
            width: 50px;
            z-index: 1;
        }

        .track-step.is-active .track-step-icon,
        .track-step.is-complete .track-step-icon {
            background: #F88379;
            color: #fff;
        }

        .track-step-title {
            color: #777;
            font-size: 13px;
            font-weight: 900;
        }

        .track-step.is-active .track-step-title,
        .track-step.is-complete .track-step-title {
            color: #111;
        }

        .track-summary {
            padding: 28px;
        }

        .track-summary h2 {
            color: #000;
            font-size: 22px;
            font-weight: 900;
            margin: 0 0 18px;
        }

        .summary-line {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .summary-line strong {
            color: #000;
        }

        .track-total {
            border-top: 1px solid #ddd;
            display: flex;
            font-size: 24px;
            font-weight: 900;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 20px;
        }

        .all-orders-button {
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

        .track-items {
            margin-top: 18px;
            padding: 18px;
        }

        .track-item {
            align-items: center;
            display: grid;
            gap: 14px;
            grid-template-columns: 72px 1fr auto;
        }

        .track-item img {
            border-radius: 10px;
            height: 72px;
            object-fit: cover;
            width: 72px;
        }

        .track-item strong,
        .track-item span {
            display: block;
        }

        .track-item span {
            color: #666;
            margin-top: 4px;
        }

        .track-note {
            background: #f7f7fb;
            border: 1px solid #e3e3ef;
            border-radius: 14px;
            margin-top: 18px;
            padding: 16px;
        }

        .track-note strong {
            display: block;
            margin-bottom: 4px;
        }

        .track-note span {
            display: block;
        }

        .rider-message-button {
            align-items: center;
            background: #F88379;
            border: 0;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-weight: 900;
            margin-top: 12px;
            height: 54px;
            justify-content: center;
            text-decoration: none;
            width: 54px;
        }

        .rider-message-button:hover {
            background: #ed0031;
            color: #fff;
            text-decoration: none;
        }

        .rider-message-icon {
            display: block;
            height: 36px;
            width: 36px;
        }

        .rider-message-button.is-disabled {
            background: #b8b8c8;
            cursor: not-allowed;
        }

        .rider-chat-panel {
            background: #fff;
            border: 1px solid #e0def0;
            border-radius: 8px;
            box-shadow: 0 14px 34px rgba(29, 24, 72, .12);
            display: none;
            margin-top: 14px;
            overflow: hidden;
        }

        .rider-chat-panel.is-open {
            display: block;
        }

        .rider-chat-head {
            align-items: center;
            background: #202020;
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
        }

        .rider-chat-head strong {
            margin: 0;
        }

        .rider-chat-close {
            background: transparent;
            border: 0;
            color: #fff;
            cursor: pointer;
            font-size: 24px;
            height: 28px;
            line-height: 1;
            width: 28px;
        }

        .rider-chat-messages {
            background: #f7f7fb;
            max-height: 190px;
            overflow-y: auto;
            padding: 12px;
        }

        .rider-chat-message {
            border-radius: 8px;
            clear: both;
            font-size: 14px;
            line-height: 1.35;
            margin-bottom: 10px;
            max-width: 82%;
            padding: 10px 12px;
            word-break: break-word;
        }

        .rider-chat-message-rider {
            background: #fff;
            border: 1px solid #e2e2e8;
            color: #111;
            float: left;
        }

        .rider-chat-message-user {
            background: #F88379;
            color: #fff;
            float: right;
        }

        .rider-chat-form {
            align-items: center;
            background: #fff;
            border-top: 1px solid #e5e5ef;
            display: flex;
            gap: 8px;
            padding: 10px;
        }

        .rider-chat-form input {
            border: 1px solid #d8d8e4;
            border-radius: 6px;
            color: #111;
            flex: 1;
            min-width: 0;
            padding: 10px;
        }

        .rider-chat-form button {
            background: #F88379;
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-weight: 900;
            min-height: 42px;
            padding: 0 14px;
        }

        .orders-modal {
            align-items: center;
            background: rgba(0, 0, 0, .72);
            display: none;
            inset: 0;
            justify-content: center;
            padding: 24px;
            position: fixed;
            z-index: 60;
        }

        .orders-modal.is-visible {
            display: flex;
        }

        .orders-panel {
            background: #fff;
            border-radius: 20px;
            max-height: min(720px, calc(100vh - 56px));
            max-width: 760px;
            overflow: auto;
            padding: 28px;
            width: 100%;
        }

        .orders-panel-head {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .orders-panel h2 {
            color: #000;
            font-size: 28px;
            font-weight: 900;
            margin: 0;
        }

        .orders-close {
            align-items: center;
            background: #111;
            border: 0;
            border-radius: 999px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-size: 22px;
            height: 40px;
            justify-content: center;
            width: 40px;
        }

        .order-row {
            align-items: center;
            border-bottom: 1px solid #eee;
            display: grid;
            gap: 16px;
            grid-template-columns: 1fr 80px;
            padding: 16px 0;
        }

        .order-row h3 {
            color: #000;
            font-size: 18px;
            font-weight: 900;
            margin: 0;
        }

        .order-row p {
            color: #666;
            margin: 5px 0;
        }

        .order-row a {
            color: #F88379;
            font-weight: 900;
        }

        .order-row img {
            border-radius: 10px;
            height: 80px;
            object-fit: cover;
            width: 80px;
        }

        @media (max-width: 960px) {
            body {
                overflow: auto;
            }

            .track-page {
                height: auto;
                min-height: 100vh;
            }

            .track-shell {
                grid-template-columns: 1fr;
                max-height: none;
            }
        }
    </style>
</head>
<body>
    @php
        $relatedOrders = $relatedOrders ?? collect([$order]);
        $allOrders = $allOrders ?? collect();
        $orderTotal = $relatedOrders->sum(fn ($item) => (float) $item->price);
        $itemCount = $relatedOrders->sum(fn ($item) => (int) $item->quantity);
        $status = $rider && $order->delivery_status === 'In Progress' ? 'On The Way' : $order->delivery_status;
        $isDelivered = $status === 'Delivered';
        $isOnWay = $status === 'On The Way';
        $isCanceled = $status === 'Canceled';
        $eta = $isDelivered ? '0 mins' : ($isOnWay ? '3 mins' : '20 mins');
        $headline = $isDelivered ? 'Your food was delivered.' : ($isOnWay ? 'Your rider has picked up your food.' : ($isCanceled ? 'Your order was canceled.' : 'Your order is being prepared.'));
        $progress = $isDelivered ? [100,100,100,100] : ($isOnWay ? [100,100,100,35] : ($isCanceled ? [0,0,0,0] : [100,35,0,0]));
        $currentStep = $isCanceled ? 0 : ($isDelivered ? 3 : ($isOnWay ? 2 : 1));
        $steps = [
            ['title' => 'Preparing', 'icon' => 'ti-package'],
            ['title' => 'On The Way', 'icon' => 'ti-truck'],
            ['title' => 'Delivered', 'icon' => 'ti-check'],
        ];
        $cartBadgeCount = \App\Models\Cart::where('userid', Auth::id())->sum('quantity');
        $userInitial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
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
    @endphp

    <div class="track-top">
        <a class="track-cart" href="{{ url('my_cart') }}">Cart <span class="track-cart-count">{{ $cartBadgeCount }}</span></a>
        <details class="track-user-menu">
            <summary aria-label="Open user menu">{{ $userInitial }}</summary>
            <div class="track-user-dropdown">
                <div class="track-user-info">
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

    <main class="track-page">
        <section class="track-shell">
            <div>
                <a class="track-back" href="{{ url('/home') }}"><i class="ti-arrow-left"></i></a>
                <h1 class="track-title">Track Order</h1>

                <div class="track-status-card">
                    <p>{{ $isDelivered ? 'Delivery completed' : 'Estimated delivery time updated' }}</p>
                    <h2>{{ $eta }}</h2>
                    <div class="track-bars">
                        @foreach($progress as $fill)
                            <span style="--fill: {{ $fill }}%;"></span>
                        @endforeach
                    </div>
                    <div class="track-steps">
                        @foreach($steps as $index => $step)
                            @php
                                $stepNumber = $index + 1;
                                $stepClass = $currentStep === $stepNumber ? 'is-active' : ($currentStep > $stepNumber ? 'is-complete' : '');
                            @endphp
                            <div class="track-step {{ $stepClass }}">
                                <div class="track-step-icon"><i class="{{ $step['icon'] }}"></i></div>
                                <div class="track-step-title">{{ $step['title'] }}</div>
                            </div>
                        @endforeach
                    </div>
                    <p>{{ $headline }}</p>
                </div>

                <div class="track-note">
                    @if($rider)
                        @php
                            $riderPhone = $rider->phone ?? '';
                        @endphp
                        <strong>Your rider: {{ $rider->name }}</strong>
                        <span>Phone: {{ $riderPhone ?: 'No phone listed' }}</span>
                        <button class="rider-message-button" type="button" aria-label="Message your rider" title="Message your rider" data-rider-chat-toggle>
                            <svg class="rider-message-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                                <path fill="#fff" d="M31.8 12C19.4 12 9.4 20.6 9.4 31.2c0 5.9 3.1 11.1 8 14.6l-1.5 8.7 9.5-4.6c2 .4 4.1.7 6.4.7 12.4 0 22.4-8.6 22.4-19.2S44.2 12 31.8 12Z"/>
                                <circle cx="23.4" cy="31.4" r="3.6" fill="#F88379"/>
                                <circle cx="32" cy="31.4" r="3.6" fill="#F88379"/>
                                <circle cx="40.6" cy="31.4" r="3.6" fill="#F88379"/>
                            </svg>
                        </button>
                        <div class="rider-chat-panel" data-rider-chat-panel>
                            <div class="rider-chat-head">
                                <strong>{{ $rider->name }}</strong>
                                <button class="rider-chat-close" type="button" aria-label="Close rider message" data-rider-chat-close>&times;</button>
                            </div>
                            <div class="rider-chat-messages" data-rider-chat-messages>
                                <div class="rider-chat-message rider-chat-message-rider">
                                    Hi, you can type your message for your rider here.
                                </div>
                            </div>
                            <form class="rider-chat-form" data-rider-chat-form>
                                <input type="text" name="message" autocomplete="off" maxlength="300" placeholder="Type message..." required>
                                <button type="submit">Send</button>
                            </form>
                        </div>
                    @else
                        <strong>Waiting for rider assignment</strong>
                        <span>Admin or cashier will confirm your delivery and assign an available rider.</span>
                    @endif
                </div>
            </div>

            <aside>
                <div class="track-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-line">
                        <span>Order #</span>
                        <strong>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                    <div class="summary-line">
                        <span>Status</span>
                        <strong>{{ $status }}</strong>
                    </div>
                    <div class="summary-line">
                        <span>Items</span>
                        <strong>{{ $itemCount }}</strong>
                    </div>
                    <div class="summary-line">
                        <span>Delivery Fee</span>
                        <strong>To be confirmed</strong>
                    </div>
                    <div class="track-total">
                        <span>Total</span>
                        <span>&#8369;{{ number_format($orderTotal, 2) }}</span>
                    </div>
                    <button class="all-orders-button" id="openOrders" type="button">All Orders</button>
                </div>

                <div class="track-items">
                    @foreach($relatedOrders->take(1) as $item)
                        @php
                            $itemImage = $resolveFoodImage($item->title, $item->image);
                        @endphp
                        <div class="track-item">
                            <img src="{{ $itemImage }}" alt="{{ $item->title }}">
                            <div>
                                <strong>{{ $item->title }}</strong>
                                <span>x{{ $item->quantity }}</span>
                            </div>
                            <strong>&#8369;{{ number_format((float) $item->price, 2) }}</strong>
                        </div>
                    @endforeach
                </div>
            </aside>
        </section>
    </main>

    <div class="orders-modal" id="ordersModal" aria-hidden="true">
        <section class="orders-panel" role="dialog" aria-modal="true" aria-labelledby="allOrdersTitle">
            <div class="orders-panel-head">
                <h2 id="allOrdersTitle">All Orders</h2>
                <button class="orders-close" id="closeOrders" type="button" aria-label="Close all orders">&times;</button>
            </div>

            @forelse($allOrders as $orderItem)
                @php
                    $orderItemImage = $resolveFoodImage($orderItem->title ?? '', $orderItem->image);
                @endphp
                <article class="order-row">
                    <div>
                        <h3>Order#: {{ $orderItem->order_number }}</h3>
                        <p>{{ $orderItem->created_at ? $orderItem->created_at->format('d-M-Y, g:i A') : '' }}</p>
                        <p>{{ $orderItem->item_count }} item(s) - &#8369;{{ number_format($orderItem->total, 2) }}</p>
                        <p>{{ $orderItem->delivery_status }} | <a href="{{ url('track_order', $orderItem->id) }}">Track</a></p>
                    </div>
                    <img src="{{ $orderItemImage }}" alt="Order item">
                </article>
            @empty
                <p>No orders yet.</p>
            @endforelse
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('ordersModal');
            var open = document.getElementById('openOrders');
            var close = document.getElementById('closeOrders');
            var riderChatToggle = document.querySelector('[data-rider-chat-toggle]');
            var riderChatPanel = document.querySelector('[data-rider-chat-panel]');
            var riderChatClose = document.querySelector('[data-rider-chat-close]');
            var riderChatForm = document.querySelector('[data-rider-chat-form]');
            var riderChatInput = riderChatForm ? riderChatForm.querySelector('input[name="message"]') : null;
            var riderChatMessages = document.querySelector('[data-rider-chat-messages]');

            function showOrders() {
                modal.classList.add('is-visible');
                modal.setAttribute('aria-hidden', 'false');
            }

            function hideOrders() {
                modal.classList.remove('is-visible');
                modal.setAttribute('aria-hidden', 'true');
            }

            function showRiderChat() {
                if (!riderChatPanel) return;
                riderChatPanel.classList.add('is-open');
                if (riderChatInput) {
                    riderChatInput.focus();
                }
            }

            function hideRiderChat() {
                if (!riderChatPanel) return;
                riderChatPanel.classList.remove('is-open');
            }

            function addRiderChatMessage(text, type) {
                if (!riderChatMessages) return;

                var bubble = document.createElement('div');
                bubble.className = 'rider-chat-message rider-chat-message-' + type;
                bubble.textContent = text;
                riderChatMessages.appendChild(bubble);
                riderChatMessages.scrollTop = riderChatMessages.scrollHeight;
            }

            if (riderChatToggle) {
                riderChatToggle.addEventListener('click', showRiderChat);
            }

            if (riderChatClose) {
                riderChatClose.addEventListener('click', hideRiderChat);
            }

            if (riderChatForm) {
                riderChatForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    var message = riderChatInput.value.trim();
                    if (!message) return;

                    addRiderChatMessage(message, 'user');
                    riderChatInput.value = '';
                });
            }

            open.addEventListener('click', showOrders);
            close.addEventListener('click', hideOrders);
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    hideOrders();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    hideOrders();
                    hideRiderChat();
                }
            });
        });
    </script>
</body>
</html>
