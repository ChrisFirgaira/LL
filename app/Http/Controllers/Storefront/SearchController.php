<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Support\Storefront\StorefrontData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = trim((string) ($validated['q'] ?? ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'data' => [],
            ]);
        }

        $products = StorefrontData::productQuery()
            ->with(StorefrontData::productRelations())
            ->where(function ($nestedQuery) use ($query) {
                $nestedQuery
                    ->whereHas('defaultUrl', fn ($urlQuery) => $urlQuery->where('slug', 'like', "%{$query}%"))
                    ->orWhere('attribute_data', 'like', "%{$query}%");
            })
            ->limit(8)
            ->get()
            ->map(function ($product) {
                $card = StorefrontData::productCard($product);

                return [
                    'id' => $card['id'],
                    'name' => $card['name'],
                    'url' => $card['url'],
                    'image' => $card['image'],
                    'price' => $card['price'],
                    'isOnSale' => $card['isOnSale'],
                ];
            })
            ->values();

        return response()->json([
            'data' => $products,
        ]);
    }
}
