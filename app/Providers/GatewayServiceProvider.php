<?php

namespace App\Providers;

use App\Http\Services\Gateways\Contracts\Gateway;
use App\Http\Services\Gateways\Shepa;
use App\Http\Services\Gateways\Zarinpal;
use App\Http\Services\Gateways\Zibal;
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
        $this->app->bind(Gateway::class, function () {

            $availableGateways = config('gateways.available');
            $default = config('gateways.default');
            $requestedGateway = request()->gateway;

            if (! in_array($requestedGateway, $availableGateways)) {
                $requestedGateway = $default;
            }

            return match ($requestedGateway) {
                'zarinpal' => new Zarinpal(),
                'shepa'    => new Shepa(),
                'zibal'    => new Zibal(),
                default    => new Zarinpal(),
            };
        });
    }
}
