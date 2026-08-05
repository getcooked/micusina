<style>
    .ordering-stage {
        background: #101010;
        padding: 70px 5vw;
    }

    .ordering-shell {
        background: #fafafa;
        border-radius: 22px;
        box-shadow: 0 26px 55px rgba(0,0,0,.35);
        display: grid;
        gap: 28px;
        grid-template-columns: 210px 1fr;
        margin: 0 auto;
        max-width: 1320px;
        min-height: 720px;
        overflow: hidden;
    }

    .ordering-side {
        background: #fff;
        border-right: 1px solid #eee;
        padding: 32px 22px;
    }

    .ordering-logo {
        align-items: center;
        color: #1f1f25;
        display: flex;
        font-size: 34px;
        font-weight: 900;
        gap: 10px;
        margin-bottom: 34px;
    }

    .ordering-logo img {
        height: 54px;
        object-fit: contain;
        width: 54px;
    }

    .ordering-menu-title {
        color: #888;
        font-size: 12px;
        margin: 24px 0 12px;
        text-transform: uppercase;
    }

    .ordering-chip {
        align-items: center;
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        color: #34343b;
        display: flex;
        font-weight: 800;
        gap: 12px;
        margin-bottom: 12px;
        padding: 13px 14px;
    }

    .ordering-chip.active {
        background: #F88379;
        color: #fff;
    }

    .ordering-chip span {
        background: #ffd4dd;
        border-radius: 8px;
        display: inline-flex;
        font-size: 20px;
        height: 38px;
        justify-content: center;
        width: 38px;
    }

    .ordering-main {
        padding: 34px 0 34px;
    }

    .ordering-search {
        align-items: center;
        background: #fff;
        border-radius: 999px;
        box-shadow: 0 12px 30px rgba(0,0,0,.05);
        color: #999;
        display: flex;
        gap: 14px;
        height: 52px;
        margin-bottom: 32px;
        padding: 0 18px;
    }

    .ordering-main h2 {
        color: #F88379;
        font-size: 22px;
        margin-bottom: 26px;
    }

    .food-grid {
        display: grid;
        gap: 28px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .food-card {
        background: #fff;
        border: 0;
        border-radius: 8px;
        box-shadow: 0 12px 26px rgba(0,0,0,.08);
        color: #202028;
        min-height: 270px;
        padding: 20px 18px 18px;
        text-align: center;
    }

    .food-card img {
        height: 130px;
        object-fit: contain;
        width: 100%;
    }

    .food-card h3 {
        color: #202028;
        font-size: 20px;
        font-weight: 900;
        margin: 8px 0;
    }

    .food-card .stars {
        color: #F88379;
        font-size: 13px;
        margin-bottom: 12px;
    }

    .food-buy {
        align-items: center;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: center;
        padding-top: 14px;
    }

    .qty-control {
        align-items: center;
        background: #ffd4dd;
        border-radius: 999px;
        display: flex;
        gap: 10px;
        padding: 7px 12px;
    }

    .qty-control input {
        background: transparent;
        border: 0;
        color: #111;
        font-weight: 900;
        text-align: center;
        width: 34px;
    }

    .qty-control button {
        background: #F88379;
        border: 0;
        border-radius: 999px;
        color: #fff;
        height: 24px;
        width: 24px;
    }

    @media (max-width: 1120px) {
        .ordering-shell { grid-template-columns: 1fr; }
        .ordering-main, .ordering-side { padding: 24px; }
    }

    @media (max-width: 640px) {
        .ordering-stage { padding: 36px 12px; }
        .food-grid { grid-template-columns: 1fr; }
    }
</style>

@php
    $menuItems = isset($data) ? collect($data)->take(4) : collect();
@endphp

<section id="gallary" class="ordering-stage">
    <div class="ordering-shell">
        <aside class="ordering-side">
            <div class="ordering-logo">
                <img src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina logo">
                <span>Cafe</span>
            </div>
            <div class="ordering-menu-title">Main Menu</div>
            <div class="ordering-chip active"><span><i class="ti-layout-grid2"></i></span> Burgers</div>
            <div class="ordering-chip"><span><i class="ti-view-list"></i></span> Sandwich</div>
            <div class="ordering-chip"><span><i class="ti-layers"></i></span> Pasta</div>
            <div class="ordering-chip"><span><i class="ti-package"></i></span> Chicken</div>
        </aside>

        <main class="ordering-main">
            <div class="ordering-search"><i class="ti-search"></i> Search By Food Name..</div>
            <h2>80+ Delicious Food Menu</h2>

            <div class="food-grid">
                @forelse($menuItems as $food)
                    <form class="food-card" action="{{ url('add_cart', $food->id) }}" method="post">
                        @csrf
                        <img src="{{ asset('food_img/' . $food->image) }}" alt="{{ $food->title }}">
                        <h3>{{ $food->title }}</h3>
                        <div class="stars">★★★★★</div>
                        <div class="food-buy">
                            <div class="qty-control">
                                <input type="number" name="qty" min="1" max="{{ max(1, $food->stock) }}" value="1">
                                <button type="submit">+</button>
                            </div>
                        </div>
                    </form>
                @empty
                    <div class="food-card">
                        <img src="{{ asset('assets/imgs/chicken burger.png') }}" alt="Chicken burger">
                        <h3>Chicken Burger</h3>
                        <div class="stars">★★★★★</div>
                    </div>
                @endforelse
            </div>
        </main>
    </div>
</section>
