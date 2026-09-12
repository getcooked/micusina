<?php

namespace App\Providers;

use App\Models\Food;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use App\Observers\FoodObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Food::observe(FoodObserver::class);

        View::composer(['admin.header', 'admin.sidebar'], function ($view) {
            $threshold = (int) config('services.low_stock.threshold', 5);
            $pendingOrderCount = Order::query()
                ->where('delivery_status', 'In Progress')
                ->get(['id', 'checkout_group_id'])
                ->unique(fn (Order $order) => $order->checkout_group_id ?: 'legacy-'.$order->id)
                ->count();

            $newUserCount = User::query()
                ->where('usertype', 'user')
                ->whereDate('created_at', today())
                ->count();

            $pendingReservationCount = Book::query()
                ->where('status', 'Pending')
                ->where('payment_status', 'Paid')
                ->count();

            $lowStockFoods = Food::where('stock', '<=', $threshold)
                ->orderBy('stock')
                ->orderBy('title')
                ->get();

            $view->with([
                'headerLowStockFoods' => $lowStockFoods,
                'headerLowStockThreshold' => $threshold,
                'headerNewUserCount' => $newUserCount,
                'headerPendingOrderCount' => $pendingOrderCount,
                'headerPendingReservationCount' => $pendingReservationCount,
                'headerNotificationCount' => $lowStockFoods->count() + $newUserCount + $pendingOrderCount + $pendingReservationCount,
            ]);
        });

    }
}
