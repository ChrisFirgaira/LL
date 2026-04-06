<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(): Response
    {
        $products = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->paginate(12)
            ->withQueryString();

        $collections = StorefrontData::collectionQuery()
            ->with(StorefrontData::collectionRelations())
            ->whereNull('parent_id')
            ->limit(8)
            ->get()
            ->map(fn ($collection) => StorefrontData::collectionCard($collection))
            ->values();

        return Inertia::render('Shop/Index', [
            'products' => $products->getCollection()
                ->map(fn ($product) => StorefrontData::productCard($product))
                ->values(),
            'pagination' => StorefrontData::pagination($products),
            'collections' => $collections,
        ]);
    }
}
