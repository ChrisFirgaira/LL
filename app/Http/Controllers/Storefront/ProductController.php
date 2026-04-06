<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(string $slug): Response
    {
        $product = StorefrontData::resolveProduct($slug);

        $relatedProducts = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->whereKeyNot($product->id)
            ->limit(4)
            ->get()
            ->map(fn ($item) => StorefrontData::productCard($item))
            ->values();

        return Inertia::render('Products/Show', [
            'product' => StorefrontData::productDetail($product),
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
