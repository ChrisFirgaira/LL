<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function show(string $slug): Response
    {
        $collection = StorefrontData::resolveCollection($slug);

        $products = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->whereHas('collections', fn ($query) => $query->whereKey($collection->id))
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Collections/Show', [
            'collection' => StorefrontData::collectionCard($collection),
            'products' => $products->getCollection()
                ->map(fn ($product) => StorefrontData::productCard($product))
                ->values(),
            'pagination' => StorefrontData::pagination($products),
        ]);
    }
}
