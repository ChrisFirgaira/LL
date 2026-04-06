<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StockLocatorData;
use App\Support\Storefront\StorefrontData;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $productCount = StorefrontData::productQuery()->count();
        $collectionCount = StorefrontData::collectionQuery()
            ->whereNull('parent_id')
            ->count();
        $catalogSample = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->latest('id')
            ->limit(24)
            ->get()
            ->map(fn ($product) => StorefrontData::productCard($product))
            ->values();

        $newArrivals = $catalogSample
            ->take(8)
            ->values();

        $saleProducts = $catalogSample
            ->filter(fn (array $product) => $product['isOnSale'] ?? false)
            ->take(4)
            ->values();

        $featuredProducts = ($saleProducts->isNotEmpty() ? $saleProducts : $catalogSample->skip(2)->take(4))
            ->values();

        $featuredCollections = StorefrontData::collectionQuery()
            ->with(StorefrontData::collectionRelations())
            ->whereNull('parent_id')
            ->limit(6)
            ->get()
            ->map(fn ($collection) => StorefrontData::collectionCard($collection))
            ->values();

        $stockRows = StockLocatorData::rows();

        return Inertia::render('Home', [
            'categories' => StorefrontData::navigationCollections(12),
            'newArrivals' => $newArrivals,
            'saleProducts' => $saleProducts,
            'featuredProducts' => $featuredProducts,
            'featuredCollections' => $featuredCollections,
            'stockRows' => $stockRows,
            'stockCategories' => StockLocatorData::categories(),
            'stockLocations' => StockLocatorData::locations(),
            'stockStatuses' => StockLocatorData::statuses(),
            'stockSummary' => StockLocatorData::summary(),
            'legacyStockLocatorUrl' => config('storefront.legacy_stock_locator_url'),
            'trustStrip' => [
                [
                    'title' => 'Free shipping over $99',
                    'copy' => 'A direct carry-over from the live Pop Attack trust message, surfaced as a utility-first store promise.',
                ],
                [
                    'title' => 'Money back guarantee',
                    'copy' => 'Keep confidence-building service cues visible before customers browse inventory.',
                ],
                [
                    'title' => 'Fast dispatch',
                    'copy' => 'Operational trust signals belong at the top of the experience, not hidden in support copy.',
                ],
            ],
            'servicePanels' => [
                [
                    'eyebrow' => 'Stock locator',
                    'title' => 'Search inventory before you travel',
                    'copy' => 'Bring the locator pattern front and center so customers can check store inventory immediately.',
                ],
                [
                    'eyebrow' => 'Locations',
                    'title' => 'Pair stock utility with nearby store discovery',
                    'copy' => 'The homepage should make stock levels and real-world locations feel like one connected system.',
                ],
                [
                    'eyebrow' => 'Catalog',
                    'title' => 'Move from utility search into shopping',
                    'copy' => 'Customers who start in the finder should have a clean handoff into the broader storefront catalog.',
                ],
            ],
            'stats' => [
                ['label' => 'Products live', 'value' => number_format($productCount)],
                ['label' => 'Collections', 'value' => number_format($collectionCount)],
                ['label' => 'Stock rows', 'value' => number_format(count($stockRows))],
            ],
        ]);
    }
}
