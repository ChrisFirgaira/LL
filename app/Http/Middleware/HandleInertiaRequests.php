<?php

namespace App\Http\Middleware;

use App\Support\Storefront\StorefrontData;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'appName' => config('app.name', 'Lunar Store'),
            'announcement' => config('storefront.announcement'),
            'contact' => [
                'email' => config('storefront.contact.display_email'),
                'phone' => config('storefront.contact.display_phone'),
                'onlineEmail' => config('storefront.contact.online_email'),
                'partnershipsEmail' => config('storefront.contact.partnerships_email'),
            ],
            'navigation' => [
                'homeUrl' => route('home'),
                'shopUrl' => route('shop.index'),
                'cartUrl' => route('cart.show'),
                'checkoutUrl' => route('checkout.index'),
                'accountUrl' => route('account.index'),
                'contactUrl' => route('contact.index'),
                'locationsUrl' => route('locations.index'),
                'stockLocatorUrl' => route('stock-locator.index'),
                'legacyStockLocatorUrl' => config('storefront.legacy_stock_locator_url'),
                'collections' => StorefrontData::navigationCollections(),
            ],
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                    ]
                    : null,
            ],
            'cartSummary' => fn () => StorefrontData::cartSummary(),
            'cartPreview' => fn () => StorefrontData::cart(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
