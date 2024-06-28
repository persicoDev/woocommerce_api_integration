<?php

namespace App\Providers;

use App\Libs\Woocommerce;
use Illuminate\Support\ServiceProvider;

class WoocommerceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Woocommerce::class, function ($app) {
            return new Woocommerce();
        });
    }

    public function boot()
    {
        //
    }
}
