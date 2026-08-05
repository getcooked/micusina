@php
    $soldCounts = $soldCounts ?? collect();
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

    $normalizeMenuSlug = function ($title) {
        return \Illuminate\Support\Str::of($title)
            ->slug()
            ->replace('chessy', 'cheesy')
            ->replace('bundwich', 'bunwich')
            ->replace('tereyaki', 'teriyaki')
            ->toString();
    };

    $getMenuCategory = function ($food) {
        $text = \Illuminate\Support\Str::of(($food->title ?? '') . ' ' . ($food->detail ?? '') . ' ' . ($food->details ?? ''))
            ->lower()
            ->toString();

        if (str_contains($text, 'ice cream')) {
            return 'Desserts';
        }

        if (str_contains($text, 'fries') || str_contains($text, 'siomai')) {
            return 'Sides & Snacks';
        }

        if (str_contains($text, 'spaghetti') || str_contains($text, 'carbonara')) {
            return 'Pasta';
        }

        if (str_contains($text, 'bowl') || str_contains($text, 'rice')) {
            return 'Rice Bowls';
        }

        if (str_contains($text, 'burger') || str_contains($text, 'bunwich') || str_contains($text, 'sandwich') || str_contains($text, 'hotdog')) {
            return 'Burgers & Bunwiches';
        }

        if (str_contains($text, 'chicken') || str_contains($text, 'nugget') || str_contains($text, 'fillet')) {
            return 'Chicken Meals';
        }

        return 'Other';
    };

    $menuCategories = collect($data)
        ->map(fn ($food) => $getMenuCategory($food))
        ->unique()
        ->sort()
        ->values();
@endphp

<div id="blog" class="mic-marketplace">
    <div class="mic-marketplace-inner">
        <div class="mic-sortbar">
            <span>Sort by</span>
            <details class="mic-category-filter">
                <summary>
                    <span>Category</span>
                    <strong id="micCategoryLabel">All</strong>
                </summary>
                <div class="mic-category-menu" aria-label="Menu categories">
                    <button class="is-active" type="button" data-category="all">All Categories</button>
                    @foreach($menuCategories as $category)
                        <button type="button" data-category="{{ e($category) }}">{{ $category }}</button>
                    @endforeach
                </div>
            </details>
            <button class="mic-sort-btn" type="button" data-sort="latest">Latest</button>
            <button class="mic-sort-btn" type="button" data-sort="sales">Top Sales</button>
            <select class="mic-price-sort" aria-label="Sort by price">
                <option value="">Price</option>
                <option value="price-asc">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
            </select>
            <div class="mic-page-count"><span>1</span>/1</div>
        </div>

        <div class="mic-product-grid" id="micProductGrid">
            @foreach($data as $food)
                @php
                    $sold = (int) ($soldCounts[$food->title] ?? 0);
                    $price = (float) $food->price;
                    $menuImage = $menuImages[$normalizeMenuSlug($food->title)] ?? null;
                    $foodImage = $menuImage ? asset('assets/imgs/' . $menuImage) : asset('food_img/' . $food->image);
                @endphp

                <article class="mic-product-card"
                    data-title="{{ e($food->title) }}"
                    data-detail="{{ e($food->detail) }}"
                    data-price="{{ $price }}"
                    data-sold="{{ $sold }}"
                    data-stock="{{ $food->stock }}"
                    data-date="{{ optional($food->created_at)->timestamp ?? 0 }}"
                    data-category="{{ e($getMenuCategory($food)) }}"
                    data-image="{{ $foodImage }}"
                    data-action="{{ url('/add_cart', $food->id) }}">
                    <button class="mic-product-open" type="button" aria-label="View {{ $food->title }}">
                        <span class="mic-product-image-wrap">
                            <img src="{{ $foodImage }}" alt="{{ $food->title }}">
                            <span class="mic-product-ribbon">Mi Cusina</span>
                        </span>
                        <span class="mic-product-body">
                            <span class="mic-product-title">{{ $food->title }}</span>
                            <span class="mic-product-price">&#8369;{{ number_format($price, 2) }}</span>
                            <span class="mic-stock {{ $food->stock <= 0 ? 'is-out' : '' }}">
                                {{ $food->stock > 0 ? $food->stock . ' available' : 'Out of stock' }}
                            </span>
                            <span class="mic-product-meta">
                                <span class="mic-stars">&#9733; 5.0</span>
                                <span>{{ $sold }} sold</span>
                            </span>
                            <span class="mic-location">Mi Cusina Kitchen</span>
                        </span>
                    </button>

                    <form action="{{ url('/add_cart', $food->id) }}" method="post" class="mic-card-cart">
                        @csrf
                        <input value="1" type="number" min="1" max="{{ max(1, $food->stock) }}" name="qty" required aria-label="Quantity" {{ $food->stock <= 0 ? 'disabled' : '' }}>
                        <button type="submit" {{ $food->stock <= 0 ? 'disabled' : '' }}>{{ $food->stock > 0 ? 'Add to Cart' : 'Out of Stock' }}</button>
                    </form>
                </article>
            @endforeach
        </div>
        <div class="mic-empty-category" id="micEmptyCategory">No food items found in this category.</div>
    </div>
</div>

<div class="mic-product-modal" id="micProductModal" aria-hidden="true">
    <div class="mic-product-modal-backdrop" data-close-product></div>
    <section class="mic-product-detail" role="dialog" aria-modal="true" aria-labelledby="micProductTitle">
        <button class="mic-product-close" type="button" data-close-product aria-label="Close product">&times;</button>
        <div class="mic-detail-media">
            <img id="micProductImage" src="" alt="">
            <div class="mic-detail-thumbs">
                <button type="button" class="is-active"><img id="micProductThumb" src="" alt=""></button>
            </div>
            <div class="mic-share-row">
                <span>Share:</span>
                <span class="mic-share-dot">f</span>
                <span class="mic-share-dot">x</span>
                <span class="mic-share-dot">p</span>
            </div>
        </div>
        <div class="mic-detail-info">
            <h3 id="micProductTitle"></h3>
            <div class="mic-detail-rating">
                <strong>5.0</strong>
                <span>&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                <span id="micProductSold"></span>
            </div>
            <div class="mic-detail-price" id="micProductPrice"></div>
            <div class="mic-detail-stock" id="micProductStock"></div>
            <div class="mic-detail-row">
                <span>Shipping</span>
                <strong>Available for Mi Cusina orders</strong>
            </div>
            <div class="mic-detail-row">
                <span>Guarantee</span>
                <strong>Freshly prepared food from this system</strong>
            </div>
            <p id="micProductDetail"></p>
            <form id="micProductForm" action="" method="post" class="mic-detail-cart">
                @csrf
                <label>
                    Quantity
                    <input value="1" type="number" min="1" name="qty" required>
                </label>
                <div class="mic-detail-actions">
                    <button type="submit" class="mic-outline-btn" id="micAddCartButton">Add To Cart</button>
                    <button type="submit" class="mic-solid-btn" id="micBuyNowButton">Buy Now</button>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
    .mic-marketplace {
        background: #050505;
        color: #222;
        padding: 28px 0 42px;
    }

    .mic-marketplace-inner {
        margin: 0 auto;
        width: min(1240px, calc(100% - 24px));
    }

    .mic-sortbar {
        align-items: center;
        background: #111;
        border: 1px solid #2a2a2a;
        color: #fff;
        display: flex;
        gap: 12px;
        margin-bottom: 14px;
        padding: 14px 22px;
    }

    .mic-sort-btn,
    .mic-category-filter summary,
    .mic-price-sort {
        background: #1c1c1c;
        border: 0;
        color: #fff;
        cursor: pointer;
        font-size: 16px;
        height: 42px;
        min-width: 112px;
    }

    .mic-category-filter {
        position: relative;
    }

    .mic-category-filter summary {
        align-items: center;
        display: inline-flex;
        gap: 10px;
        list-style: none;
        padding: 0 18px;
    }

    .mic-category-filter summary::-webkit-details-marker {
        display: none;
    }

    .mic-category-filter summary::after {
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #fff;
        content: "";
        margin-left: 8px;
    }

    .mic-category-filter[open] summary {
        background: #F88379;
    }

    .mic-category-filter strong {
        color: #ffb3c2;
        font-size: 13px;
        font-weight: 800;
    }

    .mic-category-menu {
        background: #111;
        border: 1px solid #343434;
        box-shadow: 0 18px 38px rgba(0, 0, 0, .35);
        display: grid;
        gap: 6px;
        left: 0;
        min-width: 230px;
        padding: 10px;
        position: absolute;
        top: calc(100% + 8px);
        z-index: 20;
    }

    .mic-category-menu button {
        background: transparent;
        border: 0;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        font-size: 14px;
        padding: 10px 12px;
        text-align: left;
    }

    .mic-category-menu button:hover,
    .mic-category-menu button.is-active {
        background: #F88379;
    }

    .mic-sort-btn.is-active {
        background: #F88379;
        color: #fff;
    }

    .mic-price-sort {
        flex: 0 1 250px;
        padding: 0 16px;
    }

    .mic-page-count {
        color: #fff;
        margin-left: auto;
    }

    .mic-page-count span {
        color: #F88379;
    }

    .mic-product-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .mic-product-card.is-hidden {
        display: none;
    }

    .mic-empty-category {
        border: 1px solid #2a2a2a;
        color: #d7d7d7;
        display: none;
        padding: 28px;
        text-align: center;
    }

    .mic-empty-category.is-visible {
        display: block;
    }

    .mic-product-card {
        background: #0f0f0f;
        border: 1px solid #242424;
        min-width: 0;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .mic-product-card:hover {
        box-shadow: 0 6px 18px rgba(255, 255, 255, .12);
        transform: translateY(-2px);
    }

    .mic-product-open {
        background: transparent;
        border: 0;
        cursor: pointer;
        padding: 0;
        text-align: left;
        width: 100%;
    }

    .mic-product-image-wrap {
        aspect-ratio: 1 / 1;
        background: #0f0f0f;
        display: block;
        overflow: hidden;
        position: relative;
    }

    .mic-product-image-wrap img {
        display: block;
        height: 100%;
        object-fit: contain;
        width: 100%;
    }

    .mic-product-ribbon {
        background: #F88379;
        bottom: 0;
        color: #fff;
        font-size: 12px;
        left: 0;
        padding: 3px 7px;
        position: absolute;
    }

    .mic-product-body {
        display: block;
        padding: 10px 9px 8px;
    }

    .mic-product-title {
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        color: #fff;
        display: -webkit-box;
        font-size: 18px;
        line-height: 1.24;
        min-height: 44px;
        overflow: hidden;
    }

    .mic-product-price {
        color: #F88379;
        display: block;
        font-size: 20px;
        margin-top: 8px;
    }

    .mic-product-meta,
    .mic-stock,
    .mic-location {
        color: #c7c7c7;
        display: flex;
        font-size: 12px;
        gap: 8px;
        justify-content: space-between;
        margin-top: 8px;
    }

    .mic-stars {
        color: #F88379;
    }

    .mic-stock {
        color: #0f766e;
        font-weight: 700;
    }

    .mic-stock.is-out {
        color: #b91c1c;
    }

    .mic-card-cart {
        display: flex;
        gap: 6px;
        padding: 0 9px 10px;
    }

    .mic-card-cart input {
        border: 1px solid #ddd;
        padding: 4px;
        width: 56px;
    }

    .mic-card-cart button {
        background: #1c1c1c;
        border: 1px solid #F88379;
        color: #F88379;
        cursor: pointer;
        flex: 1;
    }

    .mic-card-cart input:disabled,
    .mic-card-cart button:disabled {
        cursor: not-allowed;
        opacity: .55;
    }

    .mic-product-modal {
        display: none;
        inset: 0;
        position: fixed;
        z-index: 4000;
    }

    .mic-product-modal.is-open {
        display: block;
    }

    .mic-product-modal-backdrop {
        background: rgba(0, 0, 0, .48);
        inset: 0;
        position: absolute;
    }

    .mic-product-detail {
        background: #fff;
        color: #222;
        display: grid;
        gap: 34px;
        grid-template-columns: minmax(300px, 470px) 1fr;
        margin: 17px auto;
        max-height: calc(100vh - 34px);
        overflow-y: auto;
        padding: 24px;
        position: relative;
        width: min(1120px, calc(100% - 28px));
    }

    .mic-product-close {
        background: transparent;
        border: 0;
        cursor: pointer;
        font-size: 32px;
        position: absolute;
        right: 14px;
        top: 8px;
    }

    .mic-detail-media > img {
        aspect-ratio: 1 / 1;
        border: 1px solid #35d7c7;
        object-fit: contain;
        width: 100%;
    }

    .mic-detail-thumbs {
        display: flex;
        gap: 10px;
        margin-top: 14px;
    }

    .mic-detail-thumbs button {
        background: #fff;
        border: 2px solid #F88379;
        height: 86px;
        padding: 0;
        width: 86px;
    }

    .mic-detail-thumbs img {
        height: 100%;
        object-fit: contain;
        width: 100%;
    }

    .mic-share-row {
        align-items: center;
        display: flex;
        font-size: 18px;
        gap: 10px;
        margin-top: 22px;
    }

    .mic-share-dot {
        align-items: center;
        background: #1677f2;
        border-radius: 50%;
        color: #fff;
        display: inline-flex;
        font-weight: 700;
        height: 28px;
        justify-content: center;
        text-transform: uppercase;
        width: 28px;
    }

    .mic-detail-info h3 {
        font-size: 24px;
        line-height: 1.25;
        margin: 0 34px 12px 0;
    }

    .mic-detail-rating {
        align-items: center;
        color: #666;
        display: flex;
        gap: 16px;
        margin-bottom: 12px;
    }

    .mic-detail-rating span:nth-child(2) {
        color: #F88379;
    }

    .mic-detail-price {
        background: #fafafa;
        color: #F88379;
        font-size: 36px;
        margin-bottom: 22px;
        padding: 18px 24px;
    }

    .mic-detail-stock {
        color: #0f766e;
        font-size: 18px;
        font-weight: 700;
        margin: -10px 0 20px;
    }

    .mic-detail-stock.is-out {
        color: #b91c1c;
    }

    .mic-detail-row {
        display: grid;
        gap: 16px;
        grid-template-columns: 120px 1fr;
        margin: 18px 0;
    }

    .mic-detail-row span,
    .mic-detail-cart label {
        color: #666;
    }

    .mic-detail-info p {
        color: #444;
        line-height: 1.5;
        margin: 22px 0;
    }

    .mic-detail-cart label {
        align-items: center;
        display: flex;
        gap: 24px;
        margin-bottom: 28px;
    }

    .mic-detail-cart input {
        border: 1px solid #ddd;
        height: 40px;
        text-align: center;
        width: 84px;
    }

    .mic-detail-actions {
        display: flex;
        gap: 16px;
    }

    .mic-detail-actions button {
        cursor: pointer;
        font-size: 16px;
        height: 60px;
        min-width: 225px;
    }

    .mic-outline-btn {
        background: #fff0f3;
        border: 1px solid #F88379;
        color: #F88379;
    }

    .mic-solid-btn {
        background: #F88379;
        border: 1px solid #F88379;
        color: #fff;
    }

    @media (max-width: 991.98px) {
        .mic-product-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mic-product-detail {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .mic-sortbar {
            align-items: stretch;
            overflow-x: auto;
            padding: 12px;
        }

        .mic-category-menu {
            min-width: 210px;
        }

        .mic-product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mic-product-title {
            font-size: 14px;
        }

        .mic-detail-actions {
            flex-direction: column;
        }

        .mic-detail-actions button {
            min-width: 0;
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const grid = document.getElementById('micProductGrid');
        const modal = document.getElementById('micProductModal');
        if (!grid || !modal) return;

        const cards = Array.from(grid.querySelectorAll('.mic-product-card'));
        const sortButtons = document.querySelectorAll('.mic-sort-btn');
        const priceSort = document.querySelector('.mic-price-sort');
        const categoryFilter = document.querySelector('.mic-category-filter');
        const categoryButtons = document.querySelectorAll('.mic-category-menu button');
        const categoryLabel = document.getElementById('micCategoryLabel');
        const emptyCategory = document.getElementById('micEmptyCategory');
        const modalImage = document.getElementById('micProductImage');
        const modalThumb = document.getElementById('micProductThumb');
        const modalTitle = document.getElementById('micProductTitle');
        const modalSold = document.getElementById('micProductSold');
        const modalPrice = document.getElementById('micProductPrice');
        const modalStock = document.getElementById('micProductStock');
        const modalDetail = document.getElementById('micProductDetail');
        const modalForm = document.getElementById('micProductForm');
        const modalQty = modalForm.querySelector('input[name="qty"]');
        const addCartButton = document.getElementById('micAddCartButton');
        const buyNowButton = document.getElementById('micBuyNowButton');
        let activeCategory = 'all';

        function updateVisibleCards() {
            let visibleCount = 0;

            cards.forEach(function (card) {
                const showCard = activeCategory === 'all' || card.dataset.category === activeCategory;
                card.classList.toggle('is-hidden', !showCard);
                if (showCard) visibleCount++;
            });

            if (emptyCategory) {
                emptyCategory.classList.toggle('is-visible', visibleCount === 0);
            }
        }

        function sortCards(type) {
            const sorted = [...cards].sort(function (a, b) {
                if (type === 'latest') return Number(b.dataset.date) - Number(a.dataset.date);
                if (type === 'sales') return Number(b.dataset.sold) - Number(a.dataset.sold);
                if (type === 'price-asc') return Number(a.dataset.price) - Number(b.dataset.price);
                if (type === 'price-desc') return Number(b.dataset.price) - Number(a.dataset.price);
                return cards.indexOf(a) - cards.indexOf(b);
            });

            sorted.forEach(card => grid.appendChild(card));
            updateVisibleCards();
        }

        categoryButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activeCategory = button.dataset.category || 'all';
                categoryButtons.forEach(item => item.classList.remove('is-active'));
                button.classList.add('is-active');

                if (categoryLabel) {
                    categoryLabel.textContent = activeCategory === 'all' ? 'All' : button.textContent.trim();
                }

                if (categoryFilter) {
                    categoryFilter.open = false;
                }

                updateVisibleCards();
            });
        });

        sortButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                sortButtons.forEach(item => item.classList.remove('is-active'));
                button.classList.add('is-active');
                if (priceSort) priceSort.value = '';
                sortCards(button.dataset.sort);
            });
        });

        if (priceSort) {
            priceSort.addEventListener('change', function () {
                sortButtons.forEach(item => item.classList.remove('is-active'));
                sortCards(priceSort.value || 'relevance');
            });
        }

        updateVisibleCards();

        grid.addEventListener('click', function (event) {
            const opener = event.target.closest('.mic-product-open');
            if (!opener) return;

            const card = opener.closest('.mic-product-card');
            const stock = Number(card.dataset.stock);
            const price = Number(card.dataset.price).toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            modalImage.src = card.dataset.image;
            modalThumb.src = card.dataset.image;
            modalImage.alt = card.dataset.title;
            modalTitle.textContent = card.dataset.title;
            modalSold.textContent = card.dataset.sold + ' sold';
            modalPrice.textContent = '\u20b1' + price;
            modalStock.textContent = stock > 0 ? stock + ' available stock' : 'Out of stock';
            modalStock.classList.toggle('is-out', stock <= 0);
            modalDetail.textContent = card.dataset.detail || 'No extra details saved in the system.';
            modalForm.action = card.dataset.action;
            modalQty.value = 1;
            modalQty.max = Math.max(1, stock);
            modalQty.disabled = stock <= 0;
            addCartButton.disabled = stock <= 0;
            buyNowButton.disabled = stock <= 0;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });

        modal.addEventListener('click', function (event) {
            if (!event.target.closest('[data-close-product]')) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        });
    });
</script>
