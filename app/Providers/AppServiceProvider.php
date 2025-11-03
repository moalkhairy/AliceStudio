<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CoinPackage;
use App\Observers\CoinPackageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        CoinPackage::observe(CoinPackageObserver::class);
    }
}
