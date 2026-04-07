<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'collection' => ['nullable', 'string', 'max:120'],
            'sale' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:latest,oldest,name_asc,name_desc'],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $collection = $filters['collection'] ?? null;
        $saleOnly = (bool) ($filters['sale'] ?? false);
        $sort = $filters['sort'] ?? 'latest';

        $productQuery = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->whereHas('defaultUrl', fn ($urlQuery) => $urlQuery->where('slug', 'like', "%{$search}%"))
                        ->orWhere('attribute_data', 'like', "%{$search}%");
                });
            })
            ->when($collection, function ($query) use ($collection) {
                $query->whereHas('collections.defaultUrl', fn ($collectionQuery) => $collectionQuery->where('slug', $collection));
            })
            ->when($saleOnly, function ($query) {
                $query->whereHas('variants.prices', fn ($priceQuery) => $priceQuery->whereColumn('compare_price', '>', 'price'));
            });

        match ($sort) {
            'oldest' => $productQuery->oldest('id'),
            'name_asc' => $productQuery->orderBy('attribute_data'),
            'name_desc' => $productQuery->orderByDesc('attribute_data'),
            default => $productQuery->latest('id'),
        };

        $products = $productQuery
            ->paginate(30)
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
            'filters' => [
                'search' => $search,
                'collection' => $collection,
                'sale' => $saleOnly,
                'sort' => $sort,
            ],
        ]);
    }
}
