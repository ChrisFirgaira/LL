<?php

namespace App\Support\Storefront;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Order;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;

class StorefrontData
{
    public static function defaultChannel(): ?Channel
    {
        return Channel::getDefault();
    }

    public static function defaultCurrency(): ?Currency
    {
        return Currency::getDefault();
    }

    public static function defaultCustomerGroup(): ?CustomerGroup
    {
        return CustomerGroup::getDefault();
    }

    public static function productQuery(): Builder
    {
        return Product::query()
            ->status('published')
            ->channel(static::defaultChannel())
            ->customerGroup(static::defaultCustomerGroup());
    }

    public static function collectionQuery(): Builder
    {
        return Collection::query()
            ->channel(static::defaultChannel())
            ->customerGroup(static::defaultCustomerGroup());
    }

    public static function productRelations(): array
    {
        return [
            'defaultUrl',
            'thumbnail',
            'collections.defaultUrl',
            'variants.prices.currency',
            'variants.values',
        ];
    }

    public static function collectionRelations(): array
    {
        return [
            'defaultUrl',
            'thumbnail',
            'children.defaultUrl',
        ];
    }

    public static function resolveProduct(string $slug): Product
    {
        return static::productQuery()
            ->with(static::productRelations())
            ->where(function (Builder $query) use ($slug) {
                $query->whereHas('defaultUrl', fn (Builder $urlQuery) => $urlQuery->where('slug', $slug));

                if (is_numeric($slug)) {
                    $query->orWhereKey((int) $slug);
                }
            })
            ->firstOrFail();
    }

    public static function resolveCollection(string $slug): Collection
    {
        return static::collectionQuery()
            ->with(static::collectionRelations())
            ->where(function (Builder $query) use ($slug) {
                $query->whereHas('defaultUrl', fn (Builder $urlQuery) => $urlQuery->where('slug', $slug));

                if (is_numeric($slug)) {
                    $query->orWhereKey((int) $slug);
                }
            })
            ->firstOrFail();
    }

    public static function navigationCollections(int $limit = 6): array
    {
        return static::collectionQuery()
            ->with(static::collectionRelations())
            ->whereNull('parent_id')
            ->limit($limit)
            ->get()
            ->map(fn (Collection $collection) => static::collectionCard($collection))
            ->values()
            ->all();
    }

    public static function productCard(Product $product): array
    {
        $variant = $product->variants->first();
        $price = $variant?->prices->sortBy('min_quantity')->first()?->price;

        return [
            'id' => $product->id,
            'name' => static::productName($product),
            'description' => static::translatedAttribute($product, 'description'),
            'url' => static::productUrl($product),
            'image' => static::productImage($product),
            'price' => $price?->formatted,
            'priceValue' => $price?->value,
            'variantCount' => $product->variants->count(),
            'collectionNames' => $product->collections
                ->map(fn (Collection $collection) => static::translatedAttribute($collection, 'name'))
                ->filter()
                ->values()
                ->all(),
        ];
    }

    public static function productDetail(Product $product): array
    {
        $variants = $product->variants->map(fn (ProductVariant $variant) => static::variantData($variant))->values();

        return [
            ...static::productCard($product),
            'longDescription' => static::translatedAttribute($product, 'description'),
            'sku' => $variants->first()['sku'] ?? null,
            'variants' => $variants->all(),
            'collections' => $product->collections
                ->map(fn (Collection $collection) => static::collectionCard($collection))
                ->values()
                ->all(),
        ];
    }

    public static function collectionCard(Collection $collection): array
    {
        $children = $collection->relationLoaded('children') ? $collection->children : collect();

        return [
            'id' => $collection->id,
            'name' => static::translatedAttribute($collection, 'name') ?: "Collection {$collection->id}",
            'description' => static::translatedAttribute($collection, 'description'),
            'url' => static::collectionUrl($collection),
            'image' => $collection->thumbnail?->getUrl(),
            'children' => $children
                ->map(fn (Collection $child) => [
                    'id' => $child->id,
                    'name' => static::translatedAttribute($child, 'name') ?: "Collection {$child->id}",
                    'url' => static::collectionUrl($child),
                ])
                ->values()
                ->all(),
        ];
    }

    public static function cart(?Cart $cart = null): array
    {
        $cart ??= CartSession::current();

        if (! $cart) {
            return [
                'id' => null,
                'itemCount' => 0,
                'lines' => [],
                'subTotal' => null,
                'discountTotal' => null,
                'taxTotal' => null,
                'shippingTotal' => null,
                'total' => null,
            ];
        }

        return [
            'id' => $cart->id,
            'itemCount' => $cart->lines->sum('quantity'),
            'lines' => $cart->lines->map(function ($line) {
                $variant = $line->purchasable;
                $product = $variant?->product;

                return [
                    'id' => $line->id,
                    'name' => $product ? static::productName($product) : $variant?->getDescription(),
                    'option' => $variant?->getOption(),
                    'sku' => $variant?->sku,
                    'quantity' => $line->quantity,
                    'unitPrice' => $line->unitPrice?->formatted,
                    'total' => $line->total?->formatted,
                    'image' => $variant?->getThumbnail()?->getUrl(),
                    'url' => $product ? static::productUrl($product) : null,
                ];
            })->values()->all(),
            'subTotal' => $cart->subTotal?->formatted,
            'discountTotal' => $cart->discountTotal?->formatted,
            'taxTotal' => $cart->taxTotal?->formatted,
            'shippingTotal' => $cart->shippingTotal?->formatted,
            'total' => $cart->total?->formatted,
        ];
    }

    public static function cartSummary(?Cart $cart = null): array
    {
        $data = static::cart($cart);

        return [
            'itemCount' => $data['itemCount'],
            'total' => $data['total'],
        ];
    }

    public static function countries(): array
    {
        return Country::query()
            ->orderBy('name')
            ->get(['id', 'name', 'iso2'])
            ->map(fn (Country $country) => [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->iso2,
            ])
            ->all();
    }

    public static function checkout(?Cart $cart = null): array
    {
        $cart ??= CartSession::current();

        $billingAddress = $cart?->billingAddress;
        $shippingAddress = $cart?->shippingAddress;
        $selectedShippingOption = $shippingAddress?->shipping_option;

        return [
            'cart' => static::cart($cart),
            'billingAddress' => static::address($billingAddress),
            'shippingAddress' => static::address($shippingAddress),
            'sameAsBilling' => $billingAddress && $shippingAddress
                ? static::addressValuesMatch($billingAddress->toArray(), $shippingAddress->toArray())
                : true,
            'shippingOptions' => $cart ? static::shippingOptions($cart, $selectedShippingOption) : [],
            'selectedShippingOption' => $selectedShippingOption,
            'canPlaceOrder' => (bool) ($cart?->canCreateOrder()),
        ];
    }

    public static function order(Order $order): array
    {
        $order->loadMissing([
            'lines',
            'currency',
            'billingAddress.country',
            'shippingAddress.country',
        ]);

        return [
            'id' => $order->id,
            'reference' => $order->reference,
            'status' => $order->status_label,
            'placedAt' => optional($order->placed_at)->toDateTimeString(),
            'total' => $order->total?->formatted,
            'subTotal' => $order->sub_total?->formatted,
            'taxTotal' => $order->tax_total?->formatted,
            'billingAddress' => static::address($order->billingAddress),
            'shippingAddress' => static::address($order->shippingAddress),
            'lines' => $order->productLines->map(fn ($line) => [
                'id' => $line->id,
                'description' => $line->description,
                'option' => $line->option,
                'quantity' => $line->quantity,
                'total' => $line->total?->formatted,
            ])->values()->all(),
        ];
    }

    public static function pagination(LengthAwarePaginator $paginator): array
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'hasMorePages' => $paginator->hasMorePages(),
            'previousPageUrl' => $paginator->previousPageUrl(),
            'nextPageUrl' => $paginator->nextPageUrl(),
        ];
    }

    protected static function variantData(ProductVariant $variant): array
    {
        $priceModel = $variant->prices->sortBy('min_quantity')->first();
        $price = $priceModel?->price;

        return [
            'id' => $variant->id,
            'sku' => $variant->sku,
            'name' => trim($variant->getDescription().' '.$variant->getOption()),
            'option' => $variant->getOption(),
            'price' => $price?->formatted,
            'priceValue' => $price?->value,
            'comparePrice' => $priceModel?->compare_price?->formatted,
            'stock' => $variant->stock,
            'canPurchase' => $variant->canBeFulfilledAtQuantity(1),
        ];
    }

    protected static function productName(Product $product): string
    {
        return static::translatedAttribute($product, 'name') ?: "Product {$product->id}";
    }

    protected static function productUrl(Product $product): string
    {
        return route('products.show', $product->defaultUrl?->slug ?: (string) $product->id);
    }

    protected static function collectionUrl(Collection $collection): string
    {
        return route('collections.show', $collection->defaultUrl?->slug ?: (string) $collection->id);
    }

    protected static function productImage(Product $product): ?string
    {
        $variant = $product->variants->first();

        return $variant?->getThumbnail()?->getUrl() ?: $product->thumbnail?->getUrl();
    }

    protected static function translatedAttribute(object $model, string $attribute): ?string
    {
        if (! method_exists($model, 'translateAttribute')) {
            return null;
        }

        $value = $model->translateAttribute($attribute);

        return is_string($value) && filled(trim($value)) ? $value : null;
    }

    protected static function address(?object $address): ?array
    {
        if (! $address) {
            return null;
        }

        return [
            'title' => $address->title,
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'company_name' => $address->company_name,
            'line_one' => $address->line_one,
            'line_two' => $address->line_two,
            'city' => $address->city,
            'state' => $address->state,
            'postcode' => $address->postcode,
            'country_id' => $address->country_id,
            'country' => $address->country->name ?? null,
            'contact_email' => $address->contact_email,
            'contact_phone' => $address->contact_phone,
            'delivery_instructions' => $address->delivery_instructions,
        ];
    }

    protected static function shippingOptions(Cart $cart, ?string $selected = null): array
    {
        if (! $cart->isShippable() || ! $cart->shippingAddress) {
            return [];
        }

        return CartSession::getShippingOptions()
            ->map(fn ($option) => [
                'identifier' => $option->identifier,
                'name' => $option->name,
                'description' => $option->description,
                'price' => $option->price->formatted,
                'collect' => $option->collect,
                'selected' => $selected === $option->identifier,
            ])
            ->values()
            ->all();
    }

    protected static function addressValuesMatch(array $billing, array $shipping): bool
    {
        $keys = [
            'title',
            'first_name',
            'last_name',
            'company_name',
            'line_one',
            'line_two',
            'city',
            'state',
            'postcode',
            'country_id',
            'contact_email',
            'contact_phone',
        ];

        foreach ($keys as $key) {
            if (($billing[$key] ?? null) !== ($shipping[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
