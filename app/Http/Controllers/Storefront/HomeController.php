<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $newArrivals = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn ($product) => StorefrontData::productCard($product))
            ->values();

        $featuredProducts = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->latest('id')
            ->skip(2)
            ->limit(4)
            ->get()
            ->map(fn ($product) => StorefrontData::productCard($product))
            ->values();

        $featuredCollections = StorefrontData::collectionQuery()
            ->with(StorefrontData::collectionRelations())
            ->whereNull('parent_id')
            ->limit(6)
            ->get()
            ->map(fn ($collection) => StorefrontData::collectionCard($collection))
            ->values();

        return Inertia::render('Home', [
            'categories' => StorefrontData::navigationCollections(12),
            'newArrivals' => $newArrivals,
            'featuredProducts' => $featuredProducts,
            'featuredCollections' => $featuredCollections,
            'stats' => [
                ['label' => 'Store engine', 'value' => 'Lunar powered'],
                ['label' => 'Frontend', 'value' => 'Inertia + Vue'],
                ['label' => 'Checkout', 'value' => 'Live flow'],
            ],
        ]);
    }
}
