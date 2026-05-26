<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\MaterialPrima;
use App\Observers\MateriaPrimaObserver;

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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        MaterialPrima::observe(MateriaPrimaObserver::class);
    }
}
