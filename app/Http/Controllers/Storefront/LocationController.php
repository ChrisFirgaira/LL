<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Locations/Index', [
            'locations' => config('storefront.locations', []),
            'mapbox' => [
                'token' => config('storefront.mapbox.token'),
                'styleLight' => config('storefront.mapbox.style_light'),
                'styleDark' => config('storefront.mapbox.style_dark'),
            ],
            'stockLocatorUrl' => route('stock-locator.index'),
            'contact' => [
                'email' => config('storefront.contact.display_email'),
                'phone' => config('storefront.contact.display_phone'),
            ],
        ]);
    }
}
