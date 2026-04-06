<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StockLocatorData;
use Inertia\Inertia;
use Inertia\Response;

class StockLocatorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('StockLocator/Index', [
            'rows' => StockLocatorData::rows(),
            'categories' => StockLocatorData::categories(),
            'locations' => StockLocatorData::locations(),
            'statuses' => StockLocatorData::statuses(),
            'summary' => StockLocatorData::summary(),
            'legacyStockLocatorUrl' => config('storefront.legacy_stock_locator_url'),
        ]);
    }
}
