<?php

namespace App\Checkout;

use Closure;
use Lunar\Base\ShippingModifier;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\TaxClass;

class FlatRateShipping extends ShippingModifier
{
    public function handle($cart, Closure $next)
    {
        if (! $cart instanceof Cart || ! $cart->isShippable()) {
            return $next($cart);
        }

        $taxClass = TaxClass::getDefault() ?: TaxClass::query()->first();

        if (! $taxClass) {
            return $next($cart);
        }

        $currency = $cart->currency;
        $subTotal = $cart->subTotal?->value ?? 0;

        $standardRate = $subTotal >= config('storefront.shipping.free_over')
            ? 0
            : config('storefront.shipping.standard_rate');

        ShippingManifest::addOption(new ShippingOption(
            name: $standardRate === 0 ? 'Standard Delivery (Free)' : 'Standard Delivery',
            description: $standardRate === 0 ? 'Free tracked delivery on qualifying orders.' : 'Tracked delivery in 3 to 5 business days.',
            identifier: 'standard-delivery',
            price: new Price($standardRate, $currency),
            taxClass: $taxClass,
        ));

        ShippingManifest::addOption(new ShippingOption(
            name: 'Express Delivery',
            description: 'Priority dispatch with faster delivery times.',
            identifier: 'express-delivery',
            price: new Price(config('storefront.shipping.express_rate'), $currency),
            taxClass: $taxClass,
        ));

        ShippingManifest::addOption(new ShippingOption(
            name: 'Store Pickup',
            description: 'Collect your order in person.',
            identifier: 'store-pickup',
            price: new Price(0, $currency),
            taxClass: $taxClass,
            collect: true,
        ));

        return $next($cart);
    }
}
