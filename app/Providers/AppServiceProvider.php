<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Lunar\Base\ShippingModifiers;
use Lunar\Admin\Support\Facades\LunarPanel;
use App\Checkout\FlatRateShipping;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        LunarPanel::register();
    }

    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(FlatRateShipping::class);
    }
}