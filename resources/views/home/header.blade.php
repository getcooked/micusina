@php
    $cartBadgeCount = Auth::check() ? \App\Models\Cart::where('userid', Auth::id())->sum('quantity') : 0;
    $userInitial = Auth::check() ? strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) : 'U';
@endphp

@if(!request('section'))
<style>
    body.front-only {
        background: #000;
        margin: 0;
    }

    .burger-front {
        align-items: center;
        background: #000;
        display: flex;
        min-height: 100vh;
        padding: 0;
    }

    .burger-panel {
        background:
            radial-gradient(circle at 82% 48%, rgba(39, 53, 62, .5), transparent 36%),
            linear-gradient(90deg, #020306 0%, #05080c 54%, #020306 100%);
        border-radius: 8px;
        box-shadow: none;
        display: grid;
        grid-template-columns: 54% 46%;
        min-height: 100vh;
        overflow: hidden;
        position: relative;
        width: 100%;
    }

    .burger-topbar {
        align-items: center;
        display: grid;
        grid-template-columns: 260px 1fr 260px;
        left: 7vw;
        position: absolute;
        right: 7vw;
        top: 55px;
        z-index: 5;
    }

    .burger-mark {
        display: inline-flex;
        width: 86px;
    }

    .burger-mark img {
        display: block;
        height: auto;
        width: 100%;
    }

    .burger-nav {
        align-items: center;
        display: flex;
        gap: 44px;
        justify-content: center;
    }

    .burger-nav a {
        color: #fff;
        text-decoration: none;
    }

    .burger-nav a {
        font-size: 17px;
        font-weight: 500;
        line-height: 1;
    }

    .burger-nav a.active,
    .burger-nav a:hover {
        color: #F88379;
    }

    .burger-login {
        align-items: center;
        display: flex;
        gap: 24px;
        justify-content: flex-end;
    }

    .burger-login form {
        margin: 0;
    }

    .burger-login a,
    .burger-login button {
        background: transparent;
        border: 0;
        color: #fff;
        cursor: pointer;
        font-size: 17px;
        font-weight: 700;
        line-height: 1;
        padding: 0;
        text-decoration: none;
    }

    .burger-login a:hover,
    .burger-login button:hover {
        color: #F88379;
    }

    .cart-badge-link {
        align-items: center;
        display: inline-flex;
        gap: 7px;
    }

    .cart-badge {
        align-items: center;
        background: #F88379;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        height: 20px;
        justify-content: center;
        line-height: 1;
        min-width: 20px;
        padding: 0 6px;
    }

    .front-user-menu {
        position: relative;
    }

    .front-user-menu summary {
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

    .front-user-menu summary::-webkit-details-marker {
        display: none;
    }

    .front-user-menu summary img {
        border-radius: 999px;
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .front-user-dropdown {
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

    .front-user-info {
        border-bottom: 1px solid #2b2b2b;
        margin-bottom: 8px;
        padding: 8px 10px 12px;
    }

    .front-user-info strong,
    .front-user-info span {
        display: block;
    }

    .front-user-info strong {
        color: #fff;
        font-size: 15px;
    }

    .front-user-info span {
        color: #aaa;
        font-size: 13px;
        margin-top: 4px;
    }

    .front-user-dropdown form {
        margin: 0;
    }

    .front-user-dropdown button {
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

    .front-user-dropdown a {
        border-radius: 6px;
        color: #fff;
        display: block;
        font-size: 14px;
        font-weight: 800;
        padding: 10px;
        text-decoration: none;
    }

    .front-user-dropdown a:hover,
    .front-user-dropdown button:hover {
        background: #F88379;
        color: #050505;
    }

    .customer-photo-label {
        border-radius: 6px;
        color: #fff;
        cursor: pointer;
        display: block;
        font-size: 14px;
        font-weight: 800;
        margin: 0;
        padding: 10px;
    }

    .customer-photo-label:hover { background: #F88379; color: #050505; }
    .customer-photo-error { color: #FFA69E; display: block; font-size: 11px; padding: 4px 10px; }

    .burger-copy {
        align-self: center;
        padding: 132px 0 118px 7vw;
        position: relative;
        z-index: 3;
    }

    .burger-copy h1 {
        color: #C8A2C8;
        font-family: Impact, "Arial Black", sans-serif;
        font-size: clamp(54px, 5vw, 88px);
        font-weight: 900;
        letter-spacing: 0;
        line-height: .9;
        margin: 0 0 30px;
        text-transform: uppercase;
    }

    .burger-copy h1 .headline-accent {
        color: inherit;
        text-shadow: 0 8px 28px rgba(200, 162, 200, .24);
    }

    .burger-copy p {
        color: #fff;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.25;
        margin: 0 0 29px;
        max-width: 640px;
    }

    .burger-actions {
        align-items: center;
        display: flex;
        gap: 54px;
    }

    .burger-primary,
    .burger-secondary {
        align-items: center;
        display: inline-flex;
        font-weight: 800;
        min-height: 52px;
        text-decoration: none;
    }

    .burger-primary {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 18px 34px rgba(0, 0, 0, .25);
        color: #F88379;
        justify-content: center;
        min-width: 170px;
        padding: 0 34px;
    }

    .burger-secondary {
        color: #F88379;
    }

    .burger-art {
        background: #020306;
        min-height: 100%;
        overflow: visible;
        position: relative;
    }

    .burger-art:before {
        background: radial-gradient(circle at 58% 50%, rgba(255, 33, 79, .10), transparent 42%);
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
        z-index: 0;
    }

    .burger-art img {
        filter: drop-shadow(0 34px 54px rgba(0, 0, 0, .52));
        height: 76%;
        max-height: none;
        max-width: none;
        object-fit: contain;
        object-position: center;
        position: absolute;
        right: -4%;
        top: 14%;
        width: 92%;
        z-index: 1;
    }

    .burger-art:after {
        background:
            linear-gradient(90deg, #05080c 0%, rgba(5, 8, 12, .92) 12%, rgba(5, 8, 12, .58) 31%, rgba(5, 8, 12, 0) 58%),
            linear-gradient(180deg, rgba(2, 3, 6, .42) 0%, rgba(2, 3, 6, 0) 28%, rgba(2, 3, 6, .58) 100%);
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
        z-index: 2;
    }

    .burger-thumbs {
        align-items: end;
        bottom: 46px;
        display: flex;
        gap: 24px;
        left: 7vw;
        position: absolute;
        z-index: 4;
    }

    .burger-thumb {
        align-items: center;
        appearance: none;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .85);
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        height: 134px;
        justify-content: center;
        overflow: hidden;
        padding: 0;
        position: relative;
        width: 134px;
    }

    .burger-thumb img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .burger-thumb.active {
        border-color: #F88379;
        height: 166px;
        width: 166px;
    }

    .burger-thumb.active:before {
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 9px solid #F88379;
        content: "";
        left: 50%;
        position: absolute;
        top: -31px;
        transform: translateX(-50%);
    }

    @media (max-width: 991.98px) {
        .burger-panel {
            grid-template-columns: 1fr;
            min-height: 920px;
        }

        .burger-topbar {
            grid-template-columns: auto 1fr auto;
            left: 28px;
            right: 28px;
            top: 32px;
        }

        .burger-nav {
            gap: 18px;
        }

        .burger-copy {
            padding: 132px 28px 260px;
        }

        .burger-art {
            min-height: 360px;
        }

        .burger-art img {
            height: 400px;
            right: -120px;
            top: -30px;
            width: 760px;
        }

        .burger-thumbs {
            bottom: 28px;
            gap: 12px;
            left: 28px;
            max-width: calc(100% - 56px);
            overflow-x: auto;
        }

        .burger-thumb,
        .burger-thumb.active {
            flex: 0 0 82px;
            height: 82px;
            width: 82px;
        }
    }

    /* White-theme hero layout repair. */
    html, body.front-only { background:#fff !important; overflow-x:hidden; }
    html body .burger-front,
    html body .burger-panel,
    html body .burger-topbar,
    html body .burger-copy { background:#fff !important; }
    html body .burger-panel {
        grid-template-columns:minmax(0, 53%) minmax(0, 47%);
        min-height:100vh;
        overflow:hidden;
    }
    html body .burger-topbar {
        box-sizing:border-box;
        grid-template-columns:120px minmax(0, 1fr) minmax(190px, auto);
        left:clamp(28px, 6vw, 116px);
        right:clamp(28px, 6vw, 116px);
        top:34px;
        width:auto;
    }
    html body .burger-nav { gap:clamp(22px, 3vw, 48px); min-width:0; }
    html body .burger-login { gap:24px; min-width:0; white-space:nowrap; }
    html body .burger-mark { width:92px; }
    html body .burger-copy { padding:150px 5vw 250px 6vw; }
    html body .burger-copy h1 { font-size:clamp(52px, 4.5vw, 82px); }
    html body .burger-art {
        align-self:center;
        background:#fff !important;
        height:calc(100vh - 150px);
        margin:110px 26px 40px 0;
        min-height:560px;
        overflow:hidden;
    }
    html body .burger-art::before,
    html body .burger-art::after { background:none !important; display:none; }
    html body .burger-art img {
        border-radius:24px;
        filter:none;
        height:100%;
        inset:0;
        object-fit:contain;
        position:absolute;
        width:100%;
    }
    html body .burger-thumbs { bottom:40px; gap:16px; max-width:52%; }
    html body .burger-thumb,
    html body .burger-thumb.active {
        background:#050505 !important;
        border:2px solid #e5e7eb;
        border-radius:14px;
        flex:0 0 126px;
        height:126px;
        width:126px;
    }
    html body .burger-thumb.active { border-color:#f25f5c !important; box-shadow:0 10px 24px rgba(242,95,92,.2); }

    @media (max-width:1199.98px) {
        html body .burger-topbar { grid-template-columns:90px 1fr auto; }
        html body .burger-nav { gap:18px; }
        html body .burger-login { gap:14px; }
        html body .burger-copy { padding-left:4vw; }
        html body .burger-thumbs { left:4vw; }
        html body .burger-thumb, html body .burger-thumb.active { flex-basis:96px; height:96px; width:96px; }
    }

    @media (max-width:991.98px) {
        html body .burger-panel { display:block; min-height:100vh; overflow:visible; }
        html body .burger-topbar { align-items:flex-start; grid-template-columns:auto 1fr auto; }
        html body .burger-nav { flex-wrap:wrap; }
        html body .burger-copy { padding:150px 28px 28px; }
        html body .burger-art { height:440px; margin:20px 28px 160px; min-height:0; }
        html body .burger-thumbs { bottom:30px; left:28px; max-width:calc(100% - 56px); }
    }
</style>

<header class="burger-front" id="home">
    <div class="burger-panel">
        <div class="burger-topbar">
            <a class="burger-mark" href="{{ url('/') }}" aria-label="Mi Cusina Home">
                <img src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina">
            </a>
            <nav class="burger-nav" aria-label="Primary">
                <a class="active" href="{{ url('/') }}">Home</a>
                <a href="{{ url('/?section=food') }}">Menu</a>
                <a href="{{ url('/?section=about') }}">About</a>
                <a href="{{ url('/?section=book') }}">Book Table</a>
            </nav>
            <div class="burger-login">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('my_orders') }}">Track Order</a>
                        <a class="cart-badge-link" href="{{ url('my_cart') }}">Cart <span class="cart-badge">{{ $cartBadgeCount }}</span></a>
                        <details class="front-user-menu">
                            <summary aria-label="Open user menu">@if(Auth::user()->profile_photo_path)<img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">@else{{ $userInitial }}@endif</summary>
                            <div class="front-user-dropdown">
                                <div class="front-user-info">
                                    <strong>{{ Auth::user()->name }}</strong>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                                <form action="{{ route('customer.profile-photo.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label class="customer-photo-label" for="customerProfilePhotoHome">Upload Profile Picture</label>
                                    <input id="customerProfilePhotoHome" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden onchange="if(this.files.length)this.form.submit()">
                                    @error('photo')<small class="customer-photo-error">{{ $message }}</small>@enderror
                                </form>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit">Log Out</button>
                                </form>
                            </div>
                        </details>
                    @else
                        <a href="{{ route('login') }}">Log In</a>
                        <a href="{{ Route::has('register') ? route('register') : url('/') }}">Register</a>
                    @endauth
                @endif
            </div>
        </div>

        <div class="burger-copy">
            <h1>Fresh <span class="headline-accent">Bites</span><br>Island <span class="headline-accent">Delights</span></h1>
            <p>Your daily comfort food, made local and served with love.</p>
            <div class="burger-actions">
                <a class="burger-primary" href="{{ url('/?section=food') }}">Order Now</a>
            </div>
        </div>

        <div class="burger-art" aria-live="polite">
            <img id="burgerMainImage" src="{{ asset('food_img/hero-chicken-teriyaki.png') }}" alt="Featured chicken burger">
        </div>

        <div class="burger-thumbs" aria-label="Featured burgers">
            <button class="burger-thumb active" type="button" data-image="{{ asset('food_img/hero-chicken-teriyaki.png') }}" aria-label="Show chicken burger">
                <img src="{{ asset('food_img/hero-chicken-teriyaki.png') }}" alt="">
            </button>
            <button class="burger-thumb" type="button" data-image="{{ asset('food_img/hero-adobo-bunwich.png') }}" aria-label="Show adobo bunwich">
                <img src="{{ asset('food_img/hero-adobo-bunwich.png') }}" alt="">
            </button>
            <button class="burger-thumb" type="button" data-image="{{ asset('food_img/hero-burger-spaghetti.png') }}" aria-label="Show hotdog sandwich">
                <img src="{{ asset('food_img/hero-burger-spaghetti.png') }}" alt="">
            </button>
            <button class="burger-thumb" type="button" data-image="{{ asset('food_img/hero-chicken-burger.png') }}" aria-label="Show teriyaki rice bowl">
                <img src="{{ asset('food_img/hero-chicken-burger.png') }}" alt="">
            </button>
            <button class="burger-thumb" type="button" data-image="{{ asset('food_img/hero-hotdog-sandwich.png') }}" aria-label="Show burger spaghetti">
                <img src="{{ asset('food_img/hero-hotdog-sandwich.png') }}" alt="">
            </button>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mainImage = document.getElementById('burgerMainImage');
        var thumbs = document.querySelectorAll('.burger-thumb');

        function activateThumb(thumb) {
            if (!thumb || !thumb.dataset.image) {
                return;
            }

            if (mainImage.src !== thumb.dataset.image) {
                mainImage.src = thumb.dataset.image;
            }

            thumbs.forEach(function (item) {
                item.classList.remove('active');
            });

            thumb.classList.add('active');
        }

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('mouseenter', function () {
                activateThumb(thumb);
            });

            thumb.addEventListener('focus', function () {
                activateThumb(thumb);
            });

            thumb.addEventListener('click', function () {
                activateThumb(thumb);
            });
        });
    });
</script>
@else
<style>
    body.content-page {
        background: #000;
    }

    .inner-navbar {
        background: #000 !important;
        border: 0;
        min-height: 96px;
        padding: 0 30px;
    }

    .inner-navbar.affix {
        background: #000 !important;
        border: 0;
        min-height: 96px;
    }

    .inner-navbar .navbar-collapse {
        align-items: center;
        display: flex !important;
        justify-content: space-between;
    }

    .inner-navbar .navbar-nav {
        align-items: center;
        display: flex;
        gap: 28px;
        margin: 0;
        width: auto !important;
    }

    .inner-navbar .nav-link {
        background: transparent;
        border: 0;
        color: #fff !important;
        cursor: pointer;
        font-size: 17px;
        font-weight: 800;
        padding: 0 !important;
    }

    .inner-navbar .nav-link:hover,
    .inner-navbar .nav-link.active {
        color: #F88379 !important;
    }

    .inner-navbar .cart-badge-link {
        align-items: center;
        display: inline-flex;
        gap: 7px;
    }

    .inner-navbar .cart-badge {
        align-items: center;
        background: #F88379;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        height: 20px;
        justify-content: center;
        line-height: 1;
        min-width: 20px;
        padding: 0 6px;
    }

    .inner-navbar .front-user-menu {
        position: relative;
    }

    .inner-navbar .front-user-menu summary {
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

    .inner-navbar .front-user-menu summary::-webkit-details-marker {
        display: none;
    }

    .inner-navbar .front-user-menu summary img {
        border-radius: 999px;
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .inner-navbar .front-user-dropdown {
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

    .inner-navbar .front-user-info {
        border-bottom: 1px solid #2b2b2b;
        margin-bottom: 8px;
        padding: 8px 10px 12px;
    }

    .inner-navbar .front-user-info strong,
    .inner-navbar .front-user-info span {
        display: block;
    }

    .inner-navbar .front-user-info strong {
        color: #fff;
        font-size: 15px;
    }

    .inner-navbar .front-user-info span {
        color: #aaa;
        font-size: 13px;
        margin-top: 4px;
    }

    .inner-navbar .front-user-dropdown form {
        margin: 0;
    }

    .inner-navbar .front-user-dropdown button {
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

    .inner-navbar .front-user-dropdown a {
        border-radius: 6px;
        color: #fff;
        display: block;
        font-size: 14px;
        font-weight: 800;
        padding: 10px;
        text-decoration: none;
    }

    .inner-navbar .front-user-dropdown a:hover,
    .inner-navbar .front-user-dropdown button:hover {
        background: #F88379;
        color: #050505;
    }

    .inner-navbar .customer-photo-label {
        border-radius: 6px;
        color: #fff;
        cursor: pointer;
        display: block;
        font-size: 14px;
        font-weight: 800;
        margin: 0;
        padding: 10px;
    }

    .inner-navbar .customer-photo-label:hover { background: #F88379; color: #050505; }
    .inner-navbar .customer-photo-error { color: #FFA69E; display: block; font-size: 11px; padding: 4px 10px; }

    .inner-navbar .navbar-toggler {
        margin-left: auto;
    }

    @media (max-width: 991.98px) {
        .inner-navbar {
            min-height: auto;
            padding: 16px 20px;
        }

        .inner-navbar .navbar-collapse {
            align-items: flex-start;
            flex-direction: column;
            gap: 18px;
            margin-top: 16px;
        }

        .inner-navbar .navbar-nav {
            align-items: flex-start;
            flex-direction: column;
            gap: 14px;
        }
    }
</style>
<nav class="custom-navbar inner-navbar navbar navbar-expand-lg navbar-dark fixed-top" data-spy="affix" data-offset-top="10">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link {{ request('section') ? '' : 'active' }}" href="{{ url('/') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link {{ request('section') === 'food' ? 'active' : '' }}" href="{{ url('/?section=food') }}">Menu</a></li>
            <li class="nav-item"><a class="nav-link {{ request('section') === 'about' ? 'active' : '' }}" href="{{ url('/?section=about') }}">About</a></li>
            <li class="nav-item"><a class="nav-link {{ request('section') === 'book' ? 'active' : '' }}" href="{{ url('/?section=book') }}">Book Table</a></li>
        </ul>
        <ul class="navbar-nav">
            @auth
                <li class="nav-item"><a class="nav-link" href="{{ url('my_orders') }}">Track Order</a></li>
            @endauth
            <li class="nav-item"><a class="nav-link cart-badge-link" href="{{ url('my_cart') }}">Cart <span class="cart-badge">{{ $cartBadgeCount }}</span></a></li>
            @if (Route::has('login'))
                @auth
                    <li class="nav-item">
                        <details class="front-user-menu">
                            <summary aria-label="Open user menu">@if(Auth::user()->profile_photo_path)<img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">@else{{ $userInitial }}@endif</summary>
                            <div class="front-user-dropdown">
                                <div class="front-user-info">
                                    <strong>{{ Auth::user()->name }}</strong>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                                <form action="{{ route('customer.profile-photo.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <label class="customer-photo-label" for="customerProfilePhotoInner">Upload Profile Picture</label>
                                    <input id="customerProfilePhotoInner" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden onchange="if(this.files.length)this.form.submit()">
                                    @error('photo')<small class="customer-photo-error">{{ $message }}</small>@enderror
                                </form>
                                <a href="{{ url('/') }}">Home</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit">Log Out</button>
                                </form>
                            </div>
                        </details>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Log In</a></li>
                @endauth
            @endif
        </ul>
    </div>
</nav>
@endif

@if(session()->has('message'))
    <div class="site-flash" id="siteFlash">{{ session()->get('message') }}</div>
    <script>
        window.setTimeout(function () {
            var flash = document.getElementById('siteFlash');
            if (flash) {
                flash.remove();
            }
        }, 2000);
    </script>
@endif
