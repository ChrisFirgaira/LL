<?php

namespace App\Providers;

use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Lunar\Base\ShippingModifiers;
use Lunar\Admin\Support\Facades\LunarPanel;
use App\Checkout\FlatRateShipping;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        LunarPanel::panel(function (Panel $panel): Panel {
            return $panel
                ->brandName('Pop Attack')
                ->brandLogo(asset('images/popattack-logo.png'))
                ->darkModeBrandLogo(asset('images/popattack-logo.png'))
                ->brandLogoHeight('2.25rem')
                ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
                    <style>
                        /*
                         * Filament uses an opacity-0 bootstrap state on .fi-main-ctn.
                         * During wire:navigate transitions that can briefly expose
                         * body dark background (gray-950), perceived as black flicker.
                         */
                        .fi-body.fi-panel-lunar .fi-main-ctn {
                            opacity: 1 !important;
                        }
                    </style>
                HTML
            );
        })->register();
    }

    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(FlatRateShipping::class);
    }
}