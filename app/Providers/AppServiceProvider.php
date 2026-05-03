<?php

namespace App\Providers;

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
        // Share currency symbol with all views (respects active locale / session language)
        View::composer('*', function ($view) {
            $view->with('cur', __('currency_symbol'));
        });
    }
}
