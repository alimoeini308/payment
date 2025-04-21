<?php

namespace App\Providers;

use App\Http\Services\Gateways\Contracts\GatewayManager;
use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Register Services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap Services.
     */
    public function boot(): void
    {
        $this->app->singleton(GatewayManager::class, fn() => new GatewayManager());
    }
}
