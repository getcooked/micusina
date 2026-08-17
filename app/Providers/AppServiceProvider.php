<?php

namespace App\Providers;

use App\Models\Food;
use App\Observers\FoodObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
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

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user->usertype === 'admin' && request()->hasSession()) {
                request()->session()->flash('show_low_stock_alert', true);
            }
        });
    }
}
