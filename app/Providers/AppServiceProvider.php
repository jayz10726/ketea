<?php

namespace App\Providers;

use App\Models\Consumable;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share low-stock badge count with every view
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $view->with(
                    'lowStockBadge',
                    Consumable::whereColumn('quantity_in_stock', '<=', 'reorder_level')->count()
                );
            } else {
                $view->with('lowStockBadge', 0);
            }
        });
    }
}