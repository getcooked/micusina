<?php

namespace App\Providers;

use App\Models\Food;
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

        View::composer('admin.header', function ($view) {
            $threshold = (int) config('services.low_stock.threshold', 5);

            $view->with([
                'headerLowStockFoods' => Food::where('stock', '<=', $threshold)
                    ->orderBy('stock')
                    ->orderBy('title')
                    ->get(),
                'headerLowStockThreshold' => $threshold,
            ]);
        });

    }
}
